<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Model\InvoiceModel;
use MyApp\Model\ReservationModel;
use MyApp\Model\UserModel;
use MyApp\Model\RoomModel;
use MyApp\Entity\Invoice;
use MyApp\Entity\Reservation;
use MyApp\Service\DependencyContainer;
use Twig\Environment;
use PDO;

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
    private RoomModel $roomModel;
    private PDO $db;

    public function __construct(Environment $twig, DependencyContainer $container)
    {
        $this->twig = $twig;
        $this->invoiceModel = $container->get('InvoiceModel');
        $this->reservationModel = $container->get('ReservationModel');
        $this->userModel = $container->get('UserModel');
        $this->roomModel = $container->get('RoomModel');
        $this->db = $container->get('PDO'); // Récupérer l'instance PDO du conteneur
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

    /**
     * Télécharge ou génère la facture PDF pour une réservation spécifique
     */
    public function downloadInvoice()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vous devez être connecté pour télécharger une facture.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=login');
            exit();
        }

        // Récupérer l'ID de la réservation
        $reservationId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$reservationId) {
            $_SESSION['message'] = 'Numéro de réservation invalide.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Récupérer les détails de la réservation
        $reservation = $this->reservationModel->getReservationById((int)$reservationId);
        
        if (!$reservation) {
            $_SESSION['message'] = 'Réservation introuvable.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Vérifier que la réservation appartient bien à l'utilisateur connecté
        // Sauf si l'utilisateur est un admin
        if ($reservation['user_id'] != $_SESSION['user_id'] && $_SESSION['user_role'] != 'admin') {
            $_SESSION['message'] = 'Vous n\'êtes pas autorisé à voir cette facture.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Vérifier si une facture existe déjà pour cette réservation
        $invoice = $this->invoiceModel->getInvoiceByReservationId((int)$reservationId);
        
        // Créer le répertoire pour les factures s'il n'existe pas
        $invoiceDir = sys_get_temp_dir() . '/neptune_factures';
        if (!is_dir($invoiceDir)) {
            // Utiliser 0777 pour être sûr que les permissions sont suffisantes
            if (!@mkdir($invoiceDir, 0777, true)) {
                // Si on ne peut pas créer le répertoire, utiliser le répertoire temp du système
                $invoiceDir = sys_get_temp_dir();
            }
        }

        // Nom du fichier PDF
        $filename = 'facture_' . date('Ymd') . '_reservation_' . $reservationId . '.pdf';
        $pdfPath = 'factures/' . $filename;
        $fullPath = $invoiceDir . '/' . $filename;

        // Générer ou récupérer le PDF
        if (!$invoice || empty($invoice['pdf_path']) || !file_exists(__DIR__ . '/../../public/' . $invoice['pdf_path'])) {
            // Générer le PDF
            $this->generateInvoicePDF($reservation, $fullPath);
            
            // Mettre à jour le chemin du PDF dans la base de données si la facture existe
            if ($invoice) {
                $sql = "UPDATE Invoice SET pdf_path = :pdf_path WHERE reservation_id = :reservation_id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':pdf_path', $pdfPath, PDO::PARAM_STR);
                $stmt->bindValue(':reservation_id', $reservationId, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // Créer une nouvelle facture si elle n'existe pas
                $invoiceNumber = 'INV-' . date('Ymd') . '-' . $reservationId;
                
                // Calculer le nombre de nuits
                $checkIn = new \DateTime($reservation['check_in']);
                $checkOut = new \DateTime($reservation['check_out']);
                $interval = $checkIn->diff($checkOut);
                $numberOfNights = $interval->days;
                
                // Récupérer le prix de la chambre
                $room = $this->roomModel->getOneRoom((int)$reservation['room_id']);
                $roomPrice = $room->getPrice();
                
                // Calculer les montants
                $amount = $roomPrice * $numberOfNights;
                $taxAmount = $amount * 0.1; // 10% de taxe
                $totalAmount = $amount + $taxAmount;
                
                // Créer l'entrée de facture
                $invoiceData = [
                    'reservation_id' => $reservationId,
                    'invoice_number' => $invoiceNumber,
                    'amount' => $amount,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'due_date' => date('Y-m-d', strtotime('+7 days')),
                    'status' => 'paid',
                    'pdf_path' => $pdfPath
                ];
                
                $this->invoiceModel->createInvoice($invoiceData);
            }
        } else {
            // Utiliser le PDF existant
            $fullPath = __DIR__ . '/../../public/' . $invoice['pdf_path'];
        }

        // Envoyer le PDF au navigateur
        if (file_exists($fullPath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="facture_reservation_' . $reservationId . '.pdf"');
            header('Content-Length: ' . filesize($fullPath));
            readfile($fullPath);
            exit();
        } else {
            $_SESSION['message'] = 'Erreur lors de la génération de la facture. Vérifiez les permissions.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=confirmationReservation&id=' . $reservationId);
            exit();
        }
    }

    /**
     * Génère un PDF de facture pour une réservation avec design moderne et support UTF-8
     */
    private function generateInvoicePDF(array $reservation, string $outputPath)
    {
        // Création d'une instance FPDF avec support UTF-8
        // Utilisation du format A4 en portrait
        $pdf = new FPDF('P', 'mm', 'A4');
        
        // Ajout de la page et définition des marges
        $pdf->AddPage();
        $pdf->SetMargins(15, 15, 15);
        
        // Définition des couleurs selon dashboard.css
        $primaryColor = [94, 117, 201];   // #5E75C9
        $secondaryColor = [108, 117, 125]; // #6c757d
        $successColor = [40, 167, 69];    // #28a745
        $lightBg = [248, 249, 250];       // #f8f9fa
        $borderColor = [238, 238, 238];   // #eeeeee
        
        // === HEADER SECTION ===
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->Rect(0, 0, 210, 50, 'F');
        
        // Logo et informations de l'hôtel
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->Cell(0, 20, utf8_decode('HÔTEL NEPTUNE'), 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, utf8_decode('2 Rue du Dépôt, 8200 France'), 0, 1, 'C');
        $pdf->Cell(0, 5, 'Tel: 06 00 00 00 00 | Email: contact@neptune-hotel.fr', 0, 1, 'C');
        
        // FACTURE title
        $pdf->SetY(60);
        $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(0, 10, 'FACTURE', 0, 1, 'C');
        
        // Line below title
        $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->Line(80, 73, 130, 73);
        $pdf->Ln(10);
        
        // Numéro de facture et date (sans cadre arrondi)
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Rect(15, 80, 180, 25, 'F');
        $pdf->SetY(85);
        $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(95, 5, utf8_decode('FACTURE #INV-' . date('Ymd') . '-' . $reservation['id']), 0, 0);
        $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->Cell(95, 5, 'Date: ' . date('d/m/Y'), 0, 1, 'R');
        
        // Statut "PAYÉ" avec badge (sans utiliser RoundedRect)
        $pdf->SetY(92);
        $pdf->SetFillColor($successColor[0], $successColor[1], $successColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Rect(155, 92, 30, 8, 'F');
        $pdf->SetXY(155, 92);
        $pdf->Cell(30, 8, utf8_decode('PAYÉ'), 0, 1, 'C');
        
        // Informations client
        $pdf->SetY(115);
        $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'INFORMATIONS CLIENT', 0, 1);
        
        $pdf->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
        $pdf->Line(15, 124, 195, 124);
        
        $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 10, 'Nom:', 0, 0);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(150, 10, utf8_decode(($reservation['first_name'] ?? '') . ' ' . ($reservation['last_name'] ?? '')), 0, 1);
        
        $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->Cell(40, 10, 'Email:', 0, 0);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(150, 10, ($reservation['email'] ?? ''), 0, 1);
        
        // Informations de réservation
        $pdf->SetY(145);
        $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, utf8_decode('DÉTAILS DE LA RÉSERVATION'), 0, 1);
        
        $pdf->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
        $pdf->Line(15, 154, 195, 154);
        
        $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 10, 'Chambre:', 0, 0);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(150, 10, utf8_decode(($reservation['room_name'] ?? '')), 0, 1);
        
        $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->Cell(40, 10, utf8_decode('Période:'), 0, 0);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(150, 10, utf8_decode('Du ' . date('d/m/Y', strtotime($reservation['check_in'])) . ' au ' . date('d/m/Y', strtotime($reservation['check_out']))), 0, 1);
        
        // Calculer le nombre de nuits
        $checkIn = new \DateTime($reservation['check_in']);
        $checkOut = new \DateTime($reservation['check_out']);
        $interval = $checkIn->diff($checkOut);
        $numberOfNights = $interval->days;
        
        $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->Cell(40, 10, 'Nombre de nuits:', 0, 0);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(150, 10, $numberOfNights, 0, 1);
        
        // Tableau de détails avec fond alterné
        $pdf->SetY(185);
        $pdf->SetFillColor($lightBg[0], $lightBg[1], $lightBg[2]);
        $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetFont('Arial', 'B', 10);
        
        // En-têtes de colonnes avec fond
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(90, 10, 'Description', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Prix unitaire', 1, 0, 'C', true);
        $pdf->Cell(30, 10, utf8_decode('Quantité'), 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Montant', 1, 1, 'C', true);
        
        // Obtenir le prix de la chambre (avec conversion en nombre)
        $roomPrice = isset($reservation['room_price']) ? (float)$reservation['room_price'] : 0;
        
        // Ligne de détail avec fond alterné
        $pdf->SetFillColor(248, 249, 250);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(90, 10, utf8_decode('Chambre ' . ($reservation['room_name'] ?? '')), 1, 0, 'L', true);
        
        // Fix pour le symbole euro - utiliser le code ASCII pour l'euro
        $pdf->Cell(30, 10, number_format($roomPrice, 2, ',', ' ') . ' EUR', 1, 0, 'R', true);
        $pdf->Cell(30, 10, $numberOfNights, 1, 0, 'C', true);
        
        $subtotal = (float)$roomPrice * $numberOfNights;
        $pdf->Cell(40, 10, number_format($subtotal, 2, ',', ' ') . ' EUR', 1, 1, 'R', true);
        
        // Calcul des taxes et du total
        $taxRate = 0.1; // 10%
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount;
        
        // Sous-total, taxe et total avec mise en forme moderne
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(150, 10, 'Sous-total', 1, 0, 'R');
        $pdf->Cell(40, 10, number_format($subtotal, 2, ',', ' ') . ' EUR', 1, 1, 'R');
        
        $pdf->Cell(150, 10, 'TVA (10%)', 1, 0, 'R');
        $pdf->Cell(40, 10, number_format($taxAmount, 2, ',', ' ') . ' EUR', 1, 1, 'R');
        
        // Total en évidence
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(150, 10, 'TOTAL', 1, 0, 'R', true);
        $pdf->Cell(40, 10, number_format($totalAmount, 2, ',', ' ') . ' EUR', 1, 1, 'R', true);
        
        // Notes de paiement
        $pdf->Ln(10);
        $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->Cell(0, 5, utf8_decode('Paiement reçu avec remerciements. Cette facture a été générée automatiquement.'), 0, 1, 'C');
        
        // Pied de page
        $pdf->SetY(-25);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 5, utf8_decode('Facture générée le ' . date('d/m/Y H:i:s')), 0, 1, 'C');
        $pdf->Cell(0, 5, utf8_decode('Merci d\'avoir choisi l\'Hôtel Neptune pour votre séjour!'), 0, 1, 'C');
        
        // S'assurer que le répertoire parent existe
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        
        // Sauvegarder le PDF
        try {
            $pdf->Output('F', $outputPath);
            return true;
        } catch (\Exception $e) {
            // En cas d'erreur, essayer de sauvegarder dans le répertoire temporaire
            $tempFile = sys_get_temp_dir() . '/' . basename($outputPath);
            $pdf->Output('F', $tempFile);
            
            // Copier le fichier vers l'emplacement final si possible
            @copy($tempFile, $outputPath);
            
            return file_exists($outputPath);
        }
    }
}
