<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Model\InvoiceModel;
use MyApp\Model\ReservationModel;
use MyApp\Model\UserModel;
use MyApp\Entity\Invoice;
use MyApp\Entity\Reservation;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

// Tentative de chargement de FPDF depuis plusieurs emplacements possibles
if (file_exists(__DIR__ . '/../../vendor/setasign/fpdf/fpdf.php')) {
    require_once __DIR__ . '/../../vendor/setasign/fpdf/fpdf.php';
} elseif (file_exists(__DIR__ . '/../../vendor/fpdf/fpdf.php')) {
    require_once __DIR__ . '/../../vendor/fpdf/fpdf.php';
} else {
    // Fallback - création du répertoire si nécessaire
    $dir = __DIR__ . '/../../vendor/fpdf';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Si l'installation via Composer a échoué, informer l'utilisateur
    die("Erreur: Bibliothèque FPDF non trouvée. Veuillez exécuter 'composer require setasign/fpdf' ou télécharger FPDF manuellement.");
}

use FPDF;

class InvoiceController
{
    private Environment $twig;
    private InvoiceModel $invoiceModel;
    private ReservationModel $reservationModel;
    private UserModel $userModel;

    public function __construct(Environment $twig, DependencyContainer $container)
    {
        $this->twig = $twig;
        $this->invoiceModel = $container->get('InvoiceModel');
        $this->reservationModel = $container->get('ReservationModel');
        $this->userModel = $container->get('UserModel');
    }

    public function listInvoices()
    {
        $invoices = $this->invoiceModel->getAllInvoices();
        echo $this->twig->render('invoiceController/listInvoices.html.twig', [
            'invoices' => $invoices
        ]);
    }

    public function addInvoice()
    {
        $reservations = $this->reservationModel->getAllReservations();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_SANITIZE_NUMBER_INT);
            $invoice_number = filter_input(INPUT_POST, 'invoice_number', FILTER_SANITIZE_STRING);
            $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $tax_amount = filter_input(INPUT_POST, 'tax_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $total_amount = filter_input(INPUT_POST, 'total_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $due_date = $_POST['due_date'] ?? '';
            $status = $_POST['status'] ?? 'pending';
            $pdf_path = filter_input(INPUT_POST, 'pdf_path', FILTER_SANITIZE_STRING);

            if ($reservation_id && $invoice_number && $amount && $tax_amount && $total_amount && $due_date) {
                $reservation = $this->reservationModel->getOneReservation((int)$reservation_id);
                if ($reservation) {
                    $invoice = new Invoice(null, $reservation, $invoice_number, $amount, $tax_amount, $total_amount, '', $due_date, $status, $pdf_path);
                    $this->invoiceModel->createInvoice($invoice);
                    $_SESSION['message'] = 'Facture ajoutée';
                    header('Location: index.php?page=list-invoices');
                    exit();
                }
            }

            $_SESSION['message'] = 'Erreur lors de la création de la facture';
        }

        echo $this->twig->render('invoiceController/addInvoice.html.twig', [
            'reservations' => $reservations
        ]);
    }

    public function updateInvoice()
    {
        $reservations = $this->reservationModel->getAllReservations();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_SANITIZE_NUMBER_INT);
            $invoice_number = filter_input(INPUT_POST, 'invoice_number', FILTER_SANITIZE_STRING);
            $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $tax_amount = filter_input(INPUT_POST, 'tax_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $total_amount = filter_input(INPUT_POST, 'total_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $due_date = $_POST['due_date'] ?? '';
            $status = $_POST['status'] ?? 'pending';
            $pdf_path = filter_input(INPUT_POST, 'pdf_path', FILTER_SANITIZE_STRING);

            $reservation = $this->reservationModel->getOneReservation((int)$reservation_id);
            $invoice = $this->invoiceModel->getOneInvoice((int)$id);

            if ($reservation && $invoice) {
                $updatedInvoice = new Invoice((int)$id, $reservation, $invoice_number, $amount, $tax_amount, $total_amount, $invoice->getInvoiceDate(), $due_date, $status, $pdf_path);
                $this->invoiceModel->updateInvoice($updatedInvoice);
                $_SESSION['message'] = 'Facture mise à jour';
                header('Location: index.php?page=list-invoices');
                exit();
            } else {
                $_SESSION['message'] = 'Erreur sur la mise à jour';
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $invoice = $this->invoiceModel->getOneInvoice((int)$id);

            if (!$invoice) {
                $_SESSION['message'] = 'Facture introuvable';
                header('Location: index.php?page=list-invoices');
                exit();
            }

            echo $this->twig->render('invoiceController/updateInvoice.html.twig', [
                'invoice' => $invoice,
                'reservations' => $reservations
            ]);
        }
    }

    public function showInvoice()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $invoice = $this->invoiceModel->getOneInvoice((int)$id);

        if (!$invoice) {
            $_SESSION['message'] = 'Facture introuvable';
            header('Location: index.php?page=list-invoices');
            exit();
        }

        echo $this->twig->render('invoiceController/showInvoice.html.twig', [
            'invoice' => $invoice
        ]);
    }

    public function deleteInvoice()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($id) {
            $this->invoiceModel->deleteInvoice((int)$id);
        }
        header('Location: index.php?page=list-invoices');
    }

    /**
     * Génère une facture PDF pour une réservation
     * 
     * @param Invoice $invoice La facture à générer en PDF
     * @return string Le chemin relatif vers le fichier PDF généré
     */
    public function generateInvoicePdf(Invoice $invoice): string
    {
        $reservation = $invoice->getReservation();
        $user = $reservation->getUser();
        $room = $reservation->getRoom();
        
        // Calculer les dates et nombres de nuits
        $checkInDate = new \DateTime($reservation->getCheckIn());
        $checkOutDate = new \DateTime($reservation->getCheckOut());
        $interval = $checkInDate->diff($checkOutDate);
        $numberOfNights = $interval->days;
        
        // Démarrer le document PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Définir les polices
        $pdf->SetFont('Arial', 'B', 16);
        
        // En-tête de la facture
        $pdf->Image(__DIR__ . '/../../public/images/logo.png', 10, 10, 30);
        $pdf->SetXY(150, 10);
        $pdf->Cell(40, 10, 'FACTURE', 0, 1, 'R');
        
        // Numéro de facture et date
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(150, 20);
        $pdf->Cell(40, 5, 'Facture n°' . $invoice->getInvoiceNumber(), 0, 1, 'R');
        $pdf->SetXY(150, 25);
        $pdf->Cell(40, 5, 'Date: ' . date('d/m/Y'), 0, 1, 'R');
        
        // Informations de l'hôtel
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(10, 35);
        $pdf->Cell(100, 6, 'Hôtel Neptune', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(10);
        $pdf->Cell(100, 5, '123 Avenue des Étoiles', 0, 1);
        $pdf->SetX(10);
        $pdf->Cell(100, 5, '75000 Paris, France', 0, 1);
        $pdf->SetX(10);
        $pdf->Cell(100, 5, 'Tel: +33 1 23 45 67 89', 0, 1);
        $pdf->SetX(10);
        $pdf->Cell(100, 5, 'Email: contact@hotel-neptune.com', 0, 1);
        
        // Ligne de séparation
        $pdf->Ln(5);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(5);
        
        // Informations du client
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(100, 6, 'Facturé à:', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(100, 5, $user->getFirstName() . ' ' . $user->getLastName(), 0, 1);
        $pdf->Cell(100, 5, 'Email: ' . $user->getEmail(), 0, 1);
        $pdf->Cell(100, 5, 'Téléphone: ' . $user->getPhone(), 0, 1);
        
        // Détails de la réservation
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(190, 6, 'Détails de la réservation', 0, 1);
        
        // En-têtes de tableau
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(70, 7, 'Description', 1, 0, 'L', true);
        $pdf->Cell(30, 7, 'Dates', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Nuits', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Prix unitaire', 1, 0, 'R', true);
        $pdf->Cell(40, 7, 'Montant', 1, 1, 'R', true);
        
        // Ligne de détail
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(70, 7, 'Chambre ' . $room->getName(), 1, 0, 'L');
        $pdf->Cell(30, 7, $checkInDate->format('d/m/Y') . ' - ' . $checkOutDate->format('d/m/Y'), 1, 0, 'C');
        $pdf->Cell(20, 7, $numberOfNights, 1, 0, 'C');
        $pdf->Cell(30, 7, number_format($room->getPrice(), 2, ',', ' ') . ' €', 1, 0, 'R');
        $pdf->Cell(40, 7, number_format($invoice->getAmount(), 2, ',', ' ') . ' €', 1, 1, 'R');
        
        // Sous-total, taxes et total
        $pdf->Ln(5);
        $pdf->Cell(150, 7, 'Sous-total:', 0, 0, 'R');
        $pdf->Cell(40, 7, number_format($invoice->getAmount(), 2, ',', ' ') . ' €', 0, 1, 'R');
        
        $pdf->Cell(150, 7, 'Taxe de séjour (10%):', 0, 0, 'R');
        $pdf->Cell(40, 7, number_format($invoice->getTaxAmount(), 2, ',', ' ') . ' €', 0, 1, 'R');
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(150, 7, 'Total TTC:', 0, 0, 'R');
        $pdf->Cell(40, 7, number_format($invoice->getTotalAmount(), 2, ',', ' ') . ' €', 0, 1, 'R');
        
        // Informations de paiement
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(190, 6, 'Informations de paiement', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(190, 6, 'Statut: ' . ($invoice->getStatus() === 'paid' ? 'Payé' : 'En attente'), 0, 1);
        $pdf->Cell(190, 6, 'Date de paiement: ' . date('d/m/Y'), 0, 1);
        $pdf->Cell(190, 6, 'Méthode de paiement: Carte de crédit', 0, 1);
        
        // Pied de page
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->Cell(190, 5, 'Nous vous remercions pour votre confiance et vous souhaitons un agréable séjour.', 0, 1, 'C');
        $pdf->Cell(190, 5, 'Pour toute question concernant cette facture, merci de nous contacter.', 0, 1, 'C');
        
        // Créer le nom de fichier et le chemin
        $invoiceNumber = $invoice->getInvoiceNumber();
        $fileName = 'facture_' . $invoiceNumber . '.pdf';
        $filePath = __DIR__ . '/../../public/factures/' . $fileName;
        
        // Vérifier si le répertoire existe, sinon le créer
        $dir = __DIR__ . '/../../public/factures';
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                // Si la création échoue, log l'erreur
                error_log("Impossible de créer le répertoire des factures: $dir");
                // Utiliser un répertoire alternatif
                $dir = __DIR__ . '/../../public/';
            }
        }
        
        // Sauvegarder le PDF
        $pdf->Output('F', $filePath);
        
        // Mettre à jour le chemin du PDF dans l'entité Invoice
        $relativeFilePath = 'factures/' . $fileName;
        $invoice->setPdfPath($relativeFilePath);
        $this->invoiceModel->updateInvoice($invoice);
        
        // Retourner le chemin relatif pour l'accès via le navigateur
        return $relativeFilePath;
    }

    /**
     * Affiche la page de confirmation de réservation
     */
    public function reservationConfirmed()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vous devez être connecté pour voir cette page';
            header('Location: index.php?page=login');
            exit();
        }
        
        // Traitement des cas d'erreur
        if (!isset($_SESSION['reservation_confirmed'])) {
            if (isset($_SESSION['message'])) {
                $message = $_SESSION['message'];
            } else {
                $message = 'Aucune réservation à confirmer';
            }
            
            if (isset($_GET['invoice_id'])) {
                $invoiceId = filter_input(INPUT_GET, 'invoice_id', FILTER_SANITIZE_NUMBER_INT);
            } else {
                $_SESSION['message'] = $message;
                header('Location: index.php?page=dashboard');
                exit();
            }
        } else {
            $invoiceId = $_SESSION['invoice_id'] ?? 0;
        }
        
        // Récupération des données
        try {
            $invoice = $this->invoiceModel->getOneInvoice((int)$invoiceId);
            
            if (!$invoice) {
                throw new \Exception('Facture introuvable');
            }
            
            $reservation = $invoice->getReservation();
            $room = $reservation->getRoom();
            $user = $reservation->getUser();
            
            // Calcul du nombre de nuits
            $checkInDate = new \DateTime($reservation->getCheckIn());
            $checkOutDate = new \DateTime($reservation->getCheckOut());
            $interval = $checkInDate->diff($checkOutDate);
            $numberOfNights = $interval->days;
            
            // Vérification du PDF et génération si nécessaire
            if (empty($invoice->getPdfPath()) || !file_exists(__DIR__ . '/../../public/' . $invoice->getPdfPath())) {
                try {
                    $invoicePath = $this->generateInvoicePdf($invoice);
                } catch (\Exception $e) {
                    // En cas d'échec de génération PDF, utilisez la version HTML
                    error_log('Erreur génération PDF: ' . $e->getMessage());
                    $invoicePath = $this->generateInvoiceHtml($invoice);
                }
            } else {
                $invoicePath = $invoice->getPdfPath();
            }
            
            // Nettoyage de la session
            unset($_SESSION['reservation_confirmed']);
            unset($_SESSION['invoice_id']);
            
            // Affichage de la page de confirmation
            echo $this->twig->render('paymentController/confirmed.html.twig', [
                'reservation' => $reservation,
                'invoice' => $invoice,
                'room' => $room,
                'user' => $user,
                'numberOfNights' => $numberOfNights,
                'invoicePath' => $invoicePath
            ]);
            
        } catch (\Exception $e) {
            $_SESSION['message'] = 'Erreur: ' . $e->getMessage();
            header('Location: index.php?page=dashboard');
            exit();
        }
    }
}
