<?php
declare (strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Cancellation;
use MyApp\Entity\Reservation;
use MyApp\Entity\Room;
use MyApp\Entity\User;
use PDO;

class CancellationModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllCancellations(): array
    {
        $sql = "SELECT c.*,
                   r.id as reservation_id, r.user_id, r.room_id, r.check_in, r.check_out, r.status as reservation_status, r.total_price, r.special_requests, r.created_at as reservation_created, r.updated_at as reservation_updated,
                   u.id as cancelled_by_id, u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated,
                   ro.name as room_name, ro.is_available, ro.price, ro.capacity, ro.description, ro.featured_image_id, ro.created_at as room_created, ro.updated_at as room_updated
            FROM Cancellation c
            INNER JOIN Reservation r ON c.reservation_id = r.id
            INNER JOIN User u ON c.cancelled_by_id = u.id
            INNER JOIN Room ro ON r.room_id = ro.id
            ORDER BY c.cancellation_date DESC";

        $stmt = $this->db->query($sql);
        $cancellations = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userReservation = new User(
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
                (bool) $row['is_available'],
                (float) $row['price'],
                (int) $row['capacity'],
                $row['description'],
                null,
                $row['room_created'],
                $row['room_updated']
            );

            $reservation = new Reservation(
                $row['reservation_id'],
                $userReservation,
                $room,
                $row['check_in'],
                $row['check_out'],
                $row['reservation_status'],
                (float) $row['total_price'],
                $row['special_requests'],
                $row['reservation_created'],
                $row['reservation_updated']
            );

            $cancelledBy = new User(
                $row['cancelled_by_id'],
                $row['first_name'],
                $row['last_name'],
                $row['email'],
                $row['phone'],
                $row['password'],
                $row['role'],
                $row['user_created'],
                $row['user_updated']
            );

            $cancellations[] = new Cancellation(
                $row['id'],
                $reservation,
                $row['reason'],
                (float) $row['refund_amount'],
                $row['cancellation_date'],
                $cancelledBy
            );
        }

        return $cancellations;
    }

    public function getOneCancellation(int $id): ?Cancellation
    {
        $sql = "SELECT c.*, 
                       r.id as reservation_id, r.user_id, r.room_id, r.check_in, r.check_out, r.status as reservation_status, r.total_price, r.special_requests, r.created_at as reservation_created, r.updated_at as reservation_updated,
                       u.id as cancelled_by_id, u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated,
                       ro.name as room_name, ro.is_available, ro.price, ro.capacity, ro.description, ro.featured_image_id, ro.created_at as room_created, ro.updated_at as room_updated
                FROM Cancellation c
                INNER JOIN Reservation r ON c.reservation_id = r.id
                INNER JOIN User u ON c.cancelled_by_id = u.id
                INNER JOIN Room ro ON r.room_id = ro.id
                WHERE c.id = :id";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
    
        $userReservation = new User(
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
            $userReservation,
            $room,
            $row['check_in'],
            $row['check_out'],
            $row['reservation_status'],
            (float)$row['total_price'],
            $row['special_requests'],
            $row['reservation_created'],
            $row['reservation_updated']
        );
    
        $cancelledBy = new User(
            $row['cancelled_by_id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['phone'],
            $row['password'],
            $row['role'],
            $row['user_created'],
            $row['user_updated']
        );
    
        return new Cancellation(
            $row['id'],
            $reservation,
            $row['reason'],
            (float)$row['refund_amount'],
            $row['cancellation_date'],
            $cancelledBy
        );
    }
    
    public function createCancellation(Cancellation $cancellation): bool
    {
        $sql = "INSERT INTO Cancellation (reservation_id, reason, refund_amount, cancellation_date, cancelled_by_id)
                VALUES (:reservation_id, :reason, :refund_amount, :cancellation_date, :cancelled_by_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':reservation_id', $cancellation->getReservation()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':reason', $cancellation->getReason(), PDO::PARAM_STR);
        $stmt->bindValue(':refund_amount', $cancellation->getRefundAmount());
        $stmt->bindValue(':cancellation_date', $cancellation->getCancellationDate());
        $stmt->bindValue(':cancelled_by_id', $cancellation->getCancelledBy()->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateCancellation(Cancellation $cancellation): bool
    {
        $sql = "UPDATE Cancellation SET reason = :reason, refund_amount = :refund_amount WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':reason', $cancellation->getReason(), PDO::PARAM_STR);
        $stmt->bindValue(':refund_amount', $cancellation->getRefundAmount());
        $stmt->bindValue(':id', $cancellation->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteCancellation(int $id): bool
    {
        $sql = "DELETE FROM Cancellation WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
