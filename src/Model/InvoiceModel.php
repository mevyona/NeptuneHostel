<?php
declare(strict_types=1);

namespace MyApp\Model;

use MyApp\Entity\Invoice;
use MyApp\Entity\Reservation;
use MyApp\Entity\User;
use MyApp\Entity\Room;
use PDO;

class InvoiceModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllInvoices(): array
    {
        $sql = "SELECT i.*, 
                       r.id as reservation_id, r.user_id, r.room_id, r.check_in, r.check_out, r.status as reservation_status, r.total_price, r.special_requests, r.created_at as reservation_created, r.updated_at as reservation_updated
                FROM Invoice i
                INNER JOIN Reservation r ON i.reservation_id = r.id
                ORDER BY i.invoice_date DESC";
        $stmt = $this->db->query($sql);
        $invoices = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reservation = new Reservation(
                $row['reservation_id'],
                new User($row['user_id'], '', '', '', null, '', '', '', ''),
                new Room($row['room_id'], '', true, 0, 0, '', null, '', ''),
                $row['check_in'], $row['check_out'], $row['reservation_status'],
                $row['total_price'], $row['special_requests'], $row['reservation_created'], $row['reservation_updated']
            );

            $invoices[] = new Invoice(
                $row['id'],
                $reservation,
                $row['invoice_number'],
                $row['amount'],
                $row['tax_amount'],
                $row['total_amount'],
                $row['invoice_date'],
                $row['due_date'],
                $row['status'],
                $row['pdf_path']
            );
        }

        return $invoices;
    }

    public function getOneInvoice(int $id): ?Invoice
    {
        $sql = "SELECT i.*, 
                       r.id as reservation_id, r.user_id, r.room_id, r.check_in, r.check_out, r.status as reservation_status, r.total_price, r.special_requests, r.created_at as reservation_created, r.updated_at as reservation_updated
                FROM Invoice i
                INNER JOIN Reservation r ON i.reservation_id = r.id
                WHERE i.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        $reservation = new Reservation(
            $row['reservation_id'],
            new User($row['user_id'], '', '', '', null, '', '', '', ''),
            new Room($row['room_id'], '', true, 0, 0, '', null, '', ''),
            $row['check_in'], $row['check_out'], $row['reservation_status'],
            $row['total_price'], $row['special_requests'], $row['reservation_created'], $row['reservation_updated']
        );

        return new Invoice(
            $row['id'],
            $reservation,
            $row['invoice_number'],
            $row['amount'],
            $row['tax_amount'],
            $row['total_amount'],
            $row['invoice_date'],
            $row['due_date'],
            $row['status'],
            $row['pdf_path']
        );
    }

    public function createInvoice(Invoice $invoice): bool
    {
        $sql = "INSERT INTO Invoice (reservation_id, invoice_number, amount, tax_amount, total_amount, due_date, status, pdf_path)
                VALUES (:reservation_id, :invoice_number, :amount, :tax_amount, :total_amount, :due_date, :status, :pdf_path)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':reservation_id', $invoice->getReservation()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':invoice_number', $invoice->getInvoiceNumber(), PDO::PARAM_STR);
        $stmt->bindValue(':amount', $invoice->getAmount());
        $stmt->bindValue(':tax_amount', $invoice->getTaxAmount());
        $stmt->bindValue(':total_amount', $invoice->getTotalAmount());
        $stmt->bindValue(':due_date', $invoice->getDueDate());
        $stmt->bindValue(':status', $invoice->getStatus(), PDO::PARAM_STR);
        $stmt->bindValue(':pdf_path', $invoice->getPdfPath(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateInvoice(Invoice $invoice): bool
    {
        $sql = "UPDATE Invoice SET reservation_id = :reservation_id, invoice_number = :invoice_number, amount = :amount, 
                tax_amount = :tax_amount, total_amount = :total_amount, due_date = :due_date, status = :status, pdf_path = :pdf_path
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':reservation_id', $invoice->getReservation()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':invoice_number', $invoice->getInvoiceNumber(), PDO::PARAM_STR);
        $stmt->bindValue(':amount', $invoice->getAmount());
        $stmt->bindValue(':tax_amount', $invoice->getTaxAmount());
        $stmt->bindValue(':total_amount', $invoice->getTotalAmount());
        $stmt->bindValue(':due_date', $invoice->getDueDate());
        $stmt->bindValue(':status', $invoice->getStatus(), PDO::PARAM_STR);
        $stmt->bindValue(':pdf_path', $invoice->getPdfPath(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $invoice->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteInvoice(int $id): bool
    {
        $sql = "DELETE FROM Invoice WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
