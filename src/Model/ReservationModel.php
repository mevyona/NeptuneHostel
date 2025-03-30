<?php

declare(strict_types=1);

namespace MyApp\Model;

use PDO;
use MyApp\Entity\Reservation;

class ReservationModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function createReservation(array $data): int
    {
                if ($data instanceof Reservation) {
                        $user = $data->getUser();
            $room = $data->getRoom();
            $data = [
                'user_id' => $user->getId(),
                'room_id' => $room->getId(),
                'check_in' => $data->getCheckIn(),
                'check_out' => $data->getCheckOut(),
                'status' => $data->getStatus(),
                'total_price' => $data->getTotalPrice(),
                'special_requests' => $data->getSpecialRequests()
            ];
        }

        $sql = "INSERT INTO Reservation (user_id, room_id, check_in, check_out, status, total_price, special_requests) 
                VALUES (:user_id, :room_id, :check_in, :check_out, :status, :total_price, :special_requests)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':room_id', $data['room_id'], PDO::PARAM_INT);
        $stmt->bindValue(':check_in', $data['check_in'], PDO::PARAM_STR);
        $stmt->bindValue(':check_out', $data['check_out'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);
        $stmt->bindValue(':total_price', $data['total_price'], PDO::PARAM_STR);
        $stmt->bindValue(':special_requests', $data['special_requests'] ?? null, PDO::PARAM_STR);
        
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    public function getReservationById(int $id): ?array
    {
        $sql = "SELECT r.*, 
                      ro.name as room_name, ro.price as room_price, 
                      u.first_name, u.last_name, u.email,
                      DATEDIFF(r.check_out, r.check_in) as number_of_nights
                FROM Reservation r
                INNER JOIN Room ro ON r.room_id = ro.id
                INNER JOIN User u ON r.user_id = u.id
                WHERE r.id = :id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        return $reservation ?: null;
    }

        public function getUserReservations(int $userId): array
    {
        $sql = "SELECT r.*, ro.name as room_name, ro.price 
                FROM Reservation r
                INNER JOIN Room ro ON r.room_id = ro.id
                WHERE r.user_id = :user_id
                ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        public function getReservationsByUserId(int $userId): array
    {
        return $this->getUserReservations($userId);
    }

    public function updateReservationStatus(int $id, string $status): bool
    {
        $sql = "UPDATE Reservation SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function cancelReservation(int $id): bool
    {
        return $this->updateReservationStatus($id, 'cancelled');
    }

    public function getAllReservations(): array
    {
        $sql = "SELECT r.*, ro.name as room_name, u.first_name, u.last_name, u.email 
                FROM Reservation r
                INNER JOIN Room ro ON r.room_id = ro.id
                INNER JOIN User u ON r.user_id = u.id
                ORDER BY r.created_at DESC";
                
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isRoomBooked(int $roomId, string $checkIn, string $checkOut): bool
    {
        $sql = "SELECT COUNT(*) FROM Reservation 
                WHERE room_id = :room_id 
                AND status != 'cancelled'
                AND (
                    (check_in <= :check_in AND check_out > :check_in)
                    OR (check_in < :check_out AND check_out >= :check_out)
                    OR (:check_in <= check_in AND :check_out >= check_out)
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':room_id', $roomId, PDO::PARAM_INT);
        $stmt->bindValue(':check_in', $checkIn, PDO::PARAM_STR);
        $stmt->bindValue(':check_out', $checkOut, PDO::PARAM_STR);
        $stmt->execute();
        
        return (int)$stmt->fetchColumn() > 0;
    }

        public function getCompletedReservations(): array
    {
        $today = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM Reservation 
                WHERE check_out < :today 
                AND status != 'cancelled' 
                AND status != 'completed'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':today', $today, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        public function getFutureReservationsForRoom(int $roomId): array
    {
        $today = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM Reservation 
                WHERE room_id = :room_id 
                AND check_in > :today 
                AND status != 'cancelled'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':room_id', $roomId, PDO::PARAM_INT);
        $stmt->bindValue(':today', $today, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les réservations les plus récentes
     * 
     * @param int $limit Nombre maximum de réservations à récupérer
     * @return array Tableau de réservations récentes
     */
    public function getRecentReservations(int $limit = 5): array
    {
        try {
            // Corriger les noms de tables pour correspondre au reste du code
            $query = "SELECT r.*, 
                        u.first_name, u.last_name, 
                        ro.name AS room_name
                      FROM Reservation r
                      JOIN User u ON r.user_id = u.id
                      JOIN Room ro ON r.room_id = ro.id
                      ORDER BY r.created_at DESC
                      LIMIT :limit";
                      
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération des réservations récentes: ' . $e->getMessage());
            return [];
        }
    }
}
