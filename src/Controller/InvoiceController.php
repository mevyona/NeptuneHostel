<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Model\InvoiceModel;
use MyApp\Model\ReservationModel;
use MyApp\Entity\Invoice;
use MyApp\Entity\Reservation;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class InvoiceController
{
    private Environment $twig;
    private InvoiceModel $invoiceModel;
    private ReservationModel $reservationModel;

    public function __construct(Environment $twig, DependencyContainer $container)
    {
        $this->twig = $twig;
        $this->invoiceModel = $container->get('InvoiceModel');
        $this->reservationModel = $container->get('ReservationModel');
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
}
