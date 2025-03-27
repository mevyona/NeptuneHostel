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
                       r.id as reservation_id, r.user_id, r.room_id, r.check_in, r.check_out, r.status as reservation_status, r.total_price, r.special_requests, r.created_at as reservation_created, r.updated_at as reservation_updated,
                       u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated,
                       ro.name as room_name, ro.is_available, ro.price, ro.capacity, ro.description, ro.featured_image_id, ro.created_at as room_created, ro.updated_at as room_updated
                FROM Invoice i
                INNER JOIN Reservation r ON i.reservation_id = r.id
                INNER JOIN User u ON r.user_id = u.id
                INNER JOIN Room ro ON r.room_id = ro.id
                ORDER BY i.invoice_date DESC";
    
        $stmt = $this->db->query($sql);
        $invoices = [];
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User(
                $row['user_id'],
                $row['first_name'],
                $row['last_name'],
                $row['email'],
                $row['phone'],
                $row['password'],
                $row['role'],
                $row['user_created'],
                $row['user_updated']
            );
    
            $room = new Room(
                $row['room_id'],
                $row['room_name'],
                (bool)$row['is_available'],
                (float)$row['price'],
                (int)$row['capacity'],
                $row['description'],
                null, 
                $row['room_created'],
                $row['room_updated']
            );
    
            $reservation = new Reservation(
                $row['reservation_id'],
                $user,
                $room,
                $row['check_in'],
                $row['check_out'],
                $row['reservation_status'],
                (float)$row['total_price'],
                $row['special_requests'],
                $row['reservation_created'],
                $row['reservation_updated']
            );
    
            $invoices[] = new Invoice(
                $row['id'],
                $reservation,
                $row['invoice_number'],
                (float)$row['amount'],
                (float)$row['tax_amount'],
                (float)$row['total_amount'],
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
                       r.id as reservation_id, r.user_id, r.room_id, r.check_in, r.check_out, r.status as reservation_status, r.total_price, r.special_requests, r.created_at as reservation_created, r.updated_at as reservation_updated,
                       u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated,
                       ro.name as room_name, ro.is_available, ro.price, ro.capacity, ro.description, ro.featured_image_id, ro.created_at as room_created, ro.updated_at as room_updated
                FROM Invoice i
                INNER JOIN Reservation r ON i.reservation_id = r.id
                INNER JOIN User u ON r.user_id = u.id
                INNER JOIN Room ro ON r.room_id = ro.id
                WHERE i.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        $user = new User(
            $row['user_id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['phone'],
            $row['password'],
            $row['role'],
            $row['user_created'],
            $row['user_updated']
        );

        $room = new Room(
            $row['room_id'],
            $row['room_name'],
            (bool)$row['is_available'],
            (float)$row['price'],
            (int)$row['capacity'],
            $row['description'],
            null, 
            $row['room_created'],
            $row['room_updated']
        );

        $reservation = new Reservation(
            $row['reservation_id'],
            $user,
            $room,
            $row['check_in'],
            $row['check_out'],
            $row['reservation_status'],
            (float)$row['total_price'],
            $row['special_requests'],
            $row['reservation_created'],
            $row['reservation_updated']
        );

        return new Invoice(
            $row['id'],
            $reservation,
            $row['invoice_number'],
            (float)$row['amount'],
            (float)$row['tax_amount'],
            (float)$row['total_amount'],
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
