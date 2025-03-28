<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Model\PaymentModel;
use MyApp\Model\InvoiceModel;
use MyApp\Entity\Payment;
use MyApp\Entity\Invoice;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class PaymentController
{
    private Environment $twig;
    private PaymentModel $paymentModel;
    private InvoiceModel $invoiceModel;

    public function __construct(Environment $twig, DependencyContainer $container)
    {
        $this->twig = $twig;
        $this->paymentModel = $container->get('PaymentModel');
        $this->invoiceModel = $container->get('InvoiceModel');
    }

    public function payment()
    {
        echo $this->twig->render('paymentController/payment.html.twig', []);
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
