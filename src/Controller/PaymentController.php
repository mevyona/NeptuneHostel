<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Model\PaymentModel;
use MyApp\Model\InvoiceModel;
use MyApp\Model\RoomModel;
use MyApp\Model\UserModel;
use MyApp\Entity\Payment;
use MyApp\Entity\Invoice;
use MyApp\Entity\Reservation;
use MyApp\Model\ReservationModel;
use MyApp\Service\DependencyContainer;
use Twig\Environment;
use DateTime;

class PaymentController
{
    private Environment $twig;
    private PaymentModel $paymentModel;
    private InvoiceModel $invoiceModel;
    private RoomModel $roomModel;
    private UserModel $userModel;
    private ReservationModel $reservationModel;
    private DependencyContainer $container;
    private InvoiceController $invoiceController;

    public function __construct(Environment $twig, DependencyContainer $container)
    {
        $this->twig = $twig;
        $this->container = $container;
        $this->paymentModel = $container->get('PaymentModel');
        $this->invoiceModel = $container->get('InvoiceModel');
        $this->roomModel = $container->get('RoomModel');
        $this->userModel = $container->get('UserModel');
        $this->reservationModel = $container->get('ReservationModel');
        $this->invoiceController = new InvoiceController($twig, $container);
    }

    public function showPaymentPage()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vous devez être connecté pour effectuer un paiement.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=login');
            exit();
        }

        // Vérifier si les données de réservation sont disponibles
        if (!isset($_SESSION['reservation_data'])) {
            $_SESSION['message'] = 'Aucune réservation en cours. Veuillez d\'abord sélectionner une chambre.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=rooms');
            exit();
        }

        // Récupérer les données de réservation
        $reservationData = $_SESSION['reservation_data'];
        
        // Récupérer la chambre
        $room = $this->roomModel->getOneRoom((int)$reservationData['room_id']);
        if (!$room) {
            $_SESSION['message'] = 'Chambre introuvable.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=rooms');
            exit();
        }

        // Récupérer l'utilisateur
        $user = $this->userModel->getOneUser((int)$_SESSION['user_id']);
        if (!$user) {
            $_SESSION['message'] = 'Utilisateur introuvable.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=login');
            exit();
        }

        // Convertir les dates en chaînes de caractères si elles sont des objets DateTime
        $checkIn = $reservationData['check_in'];
        $checkOut = $reservationData['check_out'];
        
        // Convertir les dates en chaînes si nécessaire
        if ($checkIn instanceof DateTime) {
            $checkIn = $checkIn->format('Y-m-d');
        }
        
        if ($checkOut instanceof DateTime) {
            $checkOut = $checkOut->format('Y-m-d');
        }

        // Render the payment page
        echo $this->twig->render('paymentController/payment.html.twig', [
            'room' => $room,
            'user' => $user,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'numberOfNights' => $reservationData['number_of_nights'],
            'roomTotal' => $reservationData['room_total'],
            'taxAmount' => $reservationData['tax_amount'],
            'totalAmount' => $reservationData['total_amount'],
            'session' => $_SESSION ?? []
        ]);
    }

    public function processPayment()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vous devez être connecté pour effectuer une réservation.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=login');
            exit();
        }

        // Vérifier si les données de réservation existent en session
        if (!isset($_SESSION['reservation_data'])) {
            $_SESSION['message'] = 'Les informations de réservation sont manquantes.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=rooms');
            exit();
        }

        // Récupérer les données du formulaire de paiement
        $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
        $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
        $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
        $paymentMethod = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);

        // Validation des données
        if (!$firstName || !$lastName || !$email || !$phone || !$address || !$zip || !$city || !$country || !$paymentMethod) {
            $_SESSION['message'] = 'Veuillez remplir tous les champs obligatoires.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=payment');
            exit();
        }

        // Récupérer les données de réservation de la session
        $reservationData = $_SESSION['reservation_data'];
        $roomId = (int)$reservationData['room_id']; // Convertir en entier ici
        $checkIn = $reservationData['check_in'];
        $checkOut = $reservationData['check_out'];
        $totalAmount = $reservationData['total_amount'];
        $specialRequests = $reservationData['special_requests'] ?? null;

        // Créer une nouvelle réservation
        $reservation = [
            'user_id' => (int)$_SESSION['user_id'], // Convertir en entier également
            'room_id' => $roomId,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_price' => $totalAmount,
            'status' => 'confirmed',
            'special_requests' => $specialRequests
        ];

        // Enregistrer la réservation dans la base de données
        $reservationId = $this->reservationModel->createReservation($reservation);

        if ($reservationId) {
            // Marquer la chambre comme indisponible
            // Utiliser (int) pour convertir explicitement en entier
            $this->roomModel->updateRoomAvailability($roomId, false);
            
            // Créer aussi une entrée dans la table Payment si nécessaire
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . $reservationId;
            
            // Créer une facture
            $invoiceData = [
                'reservation_id' => $reservationId,
                'invoice_number' => $invoiceNumber,
                'amount' => $totalAmount * 0.9, // Montant HT (sans la taxe)
                'tax_amount' => $totalAmount * 0.1, // 10% de taxe
                'total_amount' => $totalAmount,
                'due_date' => date('Y-m-d', strtotime('+7 days')),
                'status' => 'paid'
            ];
            
            $invoiceId = $this->invoiceModel->createInvoice($invoiceData);
            
            // Enregistrer les informations de paiement
            if ($invoiceId) {
                $paymentData = [
                    'invoice_id' => $invoiceId,
                    'amount' => $totalAmount,
                    'payment_method' => $paymentMethod,
                    'transaction_id' => 'TRX-' . time(),
                    'status' => 'completed',
                    'last_four_digits' => substr(filter_input(INPUT_POST, 'card_number', FILTER_SANITIZE_STRING) ?? '', -4)
                ];
                
                $this->paymentModel->createPayment($paymentData);
            }
            
            // Nettoyer les données de réservation en session
            unset($_SESSION['reservation_data']);

            // Rediriger vers la page de confirmation
            $_SESSION['message'] = 'Votre réservation a été confirmée avec succès!';
            $_SESSION['success'] = true;
            header("Location: index.php?page=confirmationReservation&id=$reservationId");
            exit();
        } else {
            $_SESSION['message'] = 'Une erreur est survenue lors de la confirmation de votre réservation.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=payment');
            exit();
        }
    }

    public function listPayments()
    {
        $payments = $this->paymentModel->getAllPayments();
        echo $this->twig->render('paymentController/listPayments.html.twig', [
            'payments' => $payments
        ]);
    }

    public function addPayment()
    {
        $invoices = $this->invoiceModel->getAllInvoices();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoice_id = filter_input(INPUT_POST, 'invoice_id', FILTER_SANITIZE_NUMBER_INT);
            $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);
            $transaction_id = filter_input(INPUT_POST, 'transaction_id', FILTER_SANITIZE_STRING);
            $status = $_POST['status'] ?? 'pending';
            $payment_date = $_POST['payment_date'] ?? '';
            $last_four_digits = filter_input(INPUT_POST, 'last_four_digits', FILTER_SANITIZE_STRING);

            $invoice = $this->invoiceModel->getOneInvoice((int)$invoice_id);
            if ($invoice && $amount && $payment_method && $payment_date) {
                $payment = new Payment(null, $invoice, $amount, $payment_method, $transaction_id, $status, $payment_date, $last_four_digits);
                $this->paymentModel->createPayment($payment);
                $_SESSION['message'] = 'Paiement enregistré';
                header('Location: index.php?page=list-payments');
                exit();
            } else {
                $_SESSION['message'] = 'Erreur lors de l\'ajout du paiement';
            }
        }

        echo $this->twig->render('paymentController/addPayment.html.twig', [
            'invoices' => $invoices
        ]);
    }

    public function updatePayment()
    {
        $invoices = $this->invoiceModel->getAllInvoices();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $invoice_id = filter_input(INPUT_POST, 'invoice_id', FILTER_SANITIZE_NUMBER_INT);
            $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);
            $transaction_id = filter_input(INPUT_POST, 'transaction_id', FILTER_SANITIZE_STRING);
            $status = $_POST['status'] ?? 'pending';
            $payment_date = $_POST['payment_date'] ?? '';
            $last_four_digits = filter_input(INPUT_POST, 'last_four_digits', FILTER_SANITIZE_STRING);

            $invoice = $this->invoiceModel->getOneInvoice((int)$invoice_id);
            $payment = $this->paymentModel->getOnePayment((int)$id);

            if ($payment && $invoice) {
                $updated = new Payment($id, $invoice, $amount, $payment_method, $transaction_id, $status, $payment_date, $last_four_digits);
                $this->paymentModel->updatePayment($updated);
                $_SESSION['message'] = 'Paiement mis à jour';
                header('Location: index.php?page=list-payments');
                exit();
            }

            $_SESSION['message'] = 'Erreur sur la mise à jour';
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $payment = $this->paymentModel->getOnePayment((int)$id);

            if (!$payment) {
                $_SESSION['message'] = 'Paiement introuvable';
                header('Location: index.php?page=list-payments');
                exit();
            }

            echo $this->twig->render('paymentController/updatePayment.html.twig', [
                'payment' => $payment,
                'invoices' => $invoices
            ]);
        }
    }

    public function deletePayment()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->paymentModel->deletePayment((int)$id);
        $_SESSION['message'] = 'Paiement supprimé';
        header('Location: index.php?page=list-payments');
    }
}
