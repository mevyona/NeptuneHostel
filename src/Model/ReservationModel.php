<?php
declare(strict_types=1);

namespace MyApp\Model;

use MyApp\Entity\Reservation;
use MyApp\Entity\User;
use MyApp\Entity\Room;
use PDO;

class ReservationModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllReservations(): array
    {
        $sql = "SELECT r.*, 
                       u.id as user_id, u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated,
                       rm.id as room_id, rm.name as room_name, rm.is_available, rm.price, rm.capacity, rm.description, rm.featured_image_id, rm.created_at as room_created, rm.updated_at as room_updated
                FROM Reservation r
                INNER JOIN User u ON r.user_id = u.id
                INNER JOIN Room rm ON r.room_id = rm.id
                ORDER BY r.check_in";
        $stmt = $this->db->query($sql);
        $reservations = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User(
                $row['user_id'], $row['first_name'], $row['last_name'], $row['email'],
                $row['phone'], $row['password'], $row['role'], $row['user_created'], $row['user_updated']
            );

            $room = new Room(
                $row['room_id'], $row['room_name'], (bool)$row['is_available'], (float)$row['price'],
                (int)$row['capacity'], $row['description'], null, $row['room_created'], $row['room_updated']
            );

            $reservations[] = new Reservation(
                $row['id'], $user, $room, $row['check_in'], $row['check_out'],
                $row['status'], $row['total_price'], $row['special_requests'], $row['created_at'], $row['updated_at']
            );
        }

        return $reservations;
    }

    public function getOneReservation(int $id): ?Reservation
    {
        $sql = "SELECT r.*, 
                       u.id as user_id, u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated,
                       rm.id as room_id, rm.name as room_name, rm.is_available, rm.price, rm.capacity, rm.description, rm.featured_image_id, rm.created_at as room_created, rm.updated_at as room_updated
                FROM Reservation r
                INNER JOIN User u ON r.user_id = u.id
                INNER JOIN Room rm ON r.room_id = rm.id
                WHERE r.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        $user = new User(
            $row['user_id'], $row['first_name'], $row['last_name'], $row['email'],
            $row['phone'], $row['password'], $row['role'], $row['user_created'], $row['user_updated']
        );

        $room = new Room(
            $row['room_id'], $row['room_name'], (bool)$row['is_available'], (float)$row['price'],
            (int)$row['capacity'], $row['description'], null, $row['room_created'], $row['room_updated']
        );

        return new Reservation(
            $row['id'], $user, $room, $row['check_in'], $row['check_out'],
            $row['status'], $row['total_price'], $row['special_requests'], $row['created_at'], $row['updated_at']
        );
    }

    public function getReservationsByUserId(int $userId): array
    {
        $sql = "SELECT r.*, 
            u.id as user_id, u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated,
            rm.id as room_id, rm.name as room_name, rm.is_available, rm.price, rm.capacity, rm.description, rm.featured_image_id, rm.created_at as room_created, rm.updated_at as room_updated
        FROM Reservation r
        INNER JOIN User u ON r.user_id = u.id
        INNER JOIN Room rm ON r.room_id = rm.id
        WHERE r.user_id = :user_id
        ORDER BY r.check_in DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $reservations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User(
                $row['user_id'], $row['first_name'], $row['last_name'], $row['email'],
                $row['phone'], $row['password'], $row['role'], $row['user_created'], $row['user_updated']
            );
            
            $room = new Room(
                $row['room_id'], $row['room_name'], (bool)$row['is_available'], (float)$row['price'],
                (int)$row['capacity'], $row['description'], null, $row['room_created'], $row['room_updated']
            );
            
            $reservations[] = new Reservation(
                $row['id'], $user, $room, $row['check_in'], $row['check_out'],
                $row['status'], (float)$row['total_price'], $row['special_requests'],
                $row['created_at'], $row['updated_at']
            );
        }
        return $reservations;
    }

    public function createReservation(Reservation $reservation): bool
    {
        $sql = "INSERT INTO Reservation (user_id, room_id, check_in, check_out, status, total_price, special_requests)
                VALUES (:user_id, :room_id, :check_in, :check_out, :status, :total_price, :special_requests)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $reservation->getUser()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':room_id', $reservation->getRoom()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':check_in', $reservation->getCheckIn());
        $stmt->bindValue(':check_out', $reservation->getCheckOut());
        $stmt->bindValue(':status', $reservation->getStatus(), PDO::PARAM_STR);
        $stmt->bindValue(':total_price', $reservation->getTotalPrice());
        $stmt->bindValue(':special_requests', $reservation->getSpecialRequests(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateReservation(Reservation $reservation): bool
    {
        $sql = "UPDATE Reservation SET user_id = :user_id, room_id = :room_id, check_in = :check_in, check_out = :check_out,
                status = :status, total_price = :total_price, special_requests = :special_requests WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $reservation->getUser()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':room_id', $reservation->getRoom()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':check_in', $reservation->getCheckIn());
        $stmt->bindValue(':check_out', $reservation->getCheckOut());
        $stmt->bindValue(':status', $reservation->getStatus(), PDO::PARAM_STR);
        $stmt->bindValue(':total_price', $reservation->getTotalPrice());
        $stmt->bindValue(':special_requests', $reservation->getSpecialRequests(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $reservation->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteReservation(int $id): bool
    {
        $sql = "DELETE FROM Reservation WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}