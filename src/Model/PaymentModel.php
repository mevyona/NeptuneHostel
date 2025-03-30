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
        $sql = "SELECT p.*, 
                       i.id as invoice_id, i.reservation_id, i.invoice_number, i.amount as invoice_amount, i.tax_amount, i.total_amount, i.invoice_date, i.due_date, i.status as invoice_status, i.pdf_path,
                       r.id as reservation_id, r.user_id, r.room_id, r.check_in, r.check_out, r.status as reservation_status, r.total_price, r.special_requests, r.created_at as reservation_created, r.updated_at as reservation_updated,
                       u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated,
                       ro.name as room_name, ro.is_available, ro.price, ro.capacity, ro.description, ro.featured_image_id, ro.created_at as room_created, ro.updated_at as room_updated
                FROM Payment p
                INNER JOIN Invoice i ON p.invoice_id = i.id
                INNER JOIN Reservation r ON i.reservation_id = r.id
                INNER JOIN User u ON r.user_id = u.id
                INNER JOIN Room ro ON r.room_id = ro.id
                ORDER BY p.payment_date DESC";
    
        $stmt = $this->db->query($sql);
        $payments = [];
    
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
    
            $invoice = new Invoice(
                $row['invoice_id'],
                $reservation,
                $row['invoice_number'],
                (float)$row['invoice_amount'],
                (float)$row['tax_amount'],
                (float)$row['total_amount'],
                $row['invoice_date'],
                $row['due_date'],
                $row['invoice_status'],
                $row['pdf_path']
            );
    
            $payments[] = new Payment(
                $row['id'],
                $invoice,
                (float)$row['amount'],
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
        $sql = "SELECT p.*, 
                       i.id as invoice_id, i.reservation_id, i.invoice_number, i.amount as invoice_amount, i.tax_amount, i.total_amount, i.invoice_date, i.due_date, i.status as invoice_status, i.pdf_path,
                       r.id as reservation_id, r.user_id, r.room_id, r.check_in, r.check_out, r.status as reservation_status, r.total_price, r.special_requests, r.created_at as reservation_created, r.updated_at as reservation_updated,
                       u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated,
                       ro.name as room_name, ro.is_available, ro.price, ro.capacity, ro.description, ro.featured_image_id, ro.created_at as room_created, ro.updated_at as room_updated
                FROM Payment p
                INNER JOIN Invoice i ON p.invoice_id = i.id
                INNER JOIN Reservation r ON i.reservation_id = r.id
                INNER JOIN User u ON r.user_id = u.id
                INNER JOIN Room ro ON r.room_id = ro.id
                WHERE p.id = :id";
    
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
    
        $invoice = new Invoice(
            $row['invoice_id'],
            $reservation,
            $row['invoice_number'],
            (float)$row['invoice_amount'],
            (float)$row['tax_amount'],
            (float)$row['total_amount'],
            $row['invoice_date'],
            $row['due_date'],
            $row['invoice_status'],
            $row['pdf_path']
        );
    
        return new Payment(
            $row['id'],
            $invoice,
            (float)$row['amount'],
            $row['payment_method'],
            $row['transaction_id'],
            $row['status'],
            $row['payment_date'],
            $row['last_four_digits']
        );
    }
    
    public function createPayment(array $data): int
    {
        $sql = "INSERT INTO Payment (invoice_id, amount, payment_method, transaction_id, status, last_four_digits) 
                VALUES (:invoice_id, :amount, :payment_method, :transaction_id, :status, :last_four_digits)";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':invoice_id', $data['invoice_id'], PDO::PARAM_INT);
        $stmt->bindValue(':amount', $data['amount'], PDO::PARAM_STR);
        $stmt->bindValue(':payment_method', $data['payment_method'], PDO::PARAM_STR);
        $stmt->bindValue(':transaction_id', $data['transaction_id'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);
        $stmt->bindValue(':last_four_digits', $data['last_four_digits'] ?? null, PDO::PARAM_STR);
        
        $stmt->execute();
        return (int)$this->db->lastInsertId();
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
