<?php



declare(strict_types=1);

namespace MyApp\Model;

use MyApp\Entity\Payment;
use MyApp\Entity\Invoice;
use MyApp\Entity\Reservation;
use MyApp\Entity\User;
use MyApp\Entity\Room;
use PDO;

class PaymentModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllPayments(): array
    {
        $sql = "SELECT p.*, i.id as invoice_id, i.reservation_id, i.invoice_number, i.amount as invoice_amount, i.tax_amount, i.total_amount, i.invoice_date, i.due_date, i.status as invoice_status, i.pdf_path
                FROM Payment p
                INNER JOIN Invoice i ON p.invoice_id = i.id
                ORDER BY p.payment_date DESC";
        $stmt = $this->db->query($sql);
        $payments = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $invoice = new Invoice(
                $row['invoice_id'],
                new Reservation($row['reservation_id'], new User(0, '', '', '', null, '', '', '', ''), new Room(0, '', true, 0, 0, '', null, '', ''), '', '', '', 0, '', '', ''),
                $row['invoice_number'],
                $row['invoice_amount'],
                $row['tax_amount'],
                $row['total_amount'],
                $row['invoice_date'],
                $row['due_date'],
                $row['invoice_status'],
                $row['pdf_path']
            );

            $payments[] = new Payment(
                $row['id'],
                $invoice,
                $row['amount'],
                $row['payment_method'],
                $row['transaction_id'],
                $row['status'],
                $row['payment_date'],
                $row['last_four_digits']
            );
        }

        return $payments;
    }

    public function getOnePayment(int $id): ?Payment
    {
        $sql = "SELECT p.*, i.id as invoice_id, i.reservation_id, i.invoice_number, i.amount as invoice_amount, i.tax_amount, i.total_amount, i.invoice_date, i.due_date, i.status as invoice_status, i.pdf_path
                FROM Payment p
                INNER JOIN Invoice i ON p.invoice_id = i.id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        $invoice = new Invoice(
            $row['invoice_id'],
            new Reservation($row['reservation_id'], new User(0, '', '', '', null, '', '', '', ''), new Room(0, '', true, 0, 0, '', null, '', ''), '', '', '', 0, '', '', ''),
            $row['invoice_number'],
            $row['invoice_amount'],
            $row['tax_amount'],
            $row['total_amount'],
            $row['invoice_date'],
            $row['due_date'],
            $row['invoice_status'],
            $row['pdf_path']
        );

        return new Payment(
            $row['id'],
            $invoice,
            $row['amount'],
            $row['payment_method'],
            $row['transaction_id'],
            $row['status'],
            $row['payment_date'],
            $row['last_four_digits']
        );
    }

    public function createPayment(Payment $payment): bool
    {
        $sql = "INSERT INTO Payment (invoice_id, amount, payment_method, transaction_id, status, payment_date, last_four_digits)
                VALUES (:invoice_id, :amount, :payment_method, :transaction_id, :status, :payment_date, :last_four_digits)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':invoice_id', $payment->getInvoice()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':amount', $payment->getAmount());
        $stmt->bindValue(':payment_method', $payment->getPaymentMethod(), PDO::PARAM_STR);
        $stmt->bindValue(':transaction_id', $payment->getTransactionId(), PDO::PARAM_STR);
        $stmt->bindValue(':status', $payment->getStatus(), PDO::PARAM_STR);
        $stmt->bindValue(':payment_date', $payment->getPaymentDate());
        $stmt->bindValue(':last_four_digits', $payment->getLastFourDigits(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updatePayment(Payment $payment): bool
    {
        $sql = "UPDATE Payment SET invoice_id = :invoice_id, amount = :amount, payment_method = :payment_method, 
                transaction_id = :transaction_id, status = :status, payment_date = :payment_date, last_four_digits = :last_four_digits
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':invoice_id', $payment->getInvoice()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':amount', $payment->getAmount());
        $stmt->bindValue(':payment_method', $payment->getPaymentMethod(), PDO::PARAM_STR);
        $stmt->bindValue(':transaction_id', $payment->getTransactionId(), PDO::PARAM_STR);
        $stmt->bindValue(':status', $payment->getStatus(), PDO::PARAM_STR);
        $stmt->bindValue(':payment_date', $payment->getPaymentDate());
        $stmt->bindValue(':last_four_digits', $payment->getLastFourDigits(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $payment->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deletePayment(int $id): bool
    {
        $sql = "DELETE FROM Payment WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
