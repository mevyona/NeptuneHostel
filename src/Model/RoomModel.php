<?php

declare(strict_types=1);

namespace MyApp\Model;

use MyApp\Entity\Media;
use MyApp\Entity\Room;
use PDO;

class RoomModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllRooms(): array
    {
        $sql = "SELECT r.*, m.id as media_id, m.file_name, m.file_path, m.file_type, m.file_size, m.created_at as media_created
                FROM Room r
                LEFT JOIN Media m ON r.featured_image_id = m.id
                ORDER BY r.name";
        $stmt = $this->db->query($sql);
        $rooms = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $media = null;
            if ($row['media_id']) {
                $media = new Media(
                    $row['media_id'],
                    $row['file_name'],
                    $row['file_path'],
                    $row['file_type'],
                    $row['file_size'],
                    $row['media_created']
                );
            }
            $rooms[] = new Room(
                $row['id'],
                $row['name'],
                (bool)$row['is_available'],
                (float)$row['price'],
                (int)$row['capacity'],
                $row['description'],
                $media,
                $row['created_at'],
                $row['updated_at']
            );
        }
        return $rooms;
    }

    public function getOneRoom(int $id): ?Room
    {
        $sql = "SELECT r.*, m.id as media_id, m.file_name, m.file_path, m.file_type, m.file_size, m.created_at as media_created
                FROM Room r
                LEFT JOIN Media m ON r.featured_image_id = m.id
                WHERE r.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        $media = null;
        if ($row['media_id']) {
            $media = new Media(
                $row['media_id'],
                $row['file_name'],
                $row['file_path'],
                $row['file_type'],
                $row['file_size'],
                $row['media_created']
            );
        }

        return new Room(
            $row['id'],
            $row['name'],
            (bool)$row['is_available'],
            (float)$row['price'],
            (int)$row['capacity'],
            $row['description'],
            $media,
            $row['created_at'],
            $row['updated_at']
        );
    }

    public function getSimilarRooms(int $capacity, int $currentRoomId, int $limit = 3): array
    {
                $sql = "SELECT r.*, m.id as media_id, m.file_name, m.file_path, m.file_type, m.file_size, m.created_at as media_created
                FROM Room r
                LEFT JOIN Media m ON r.featured_image_id = m.id
                WHERE r.id != :current_room_id 
                AND (r.capacity BETWEEN :min_capacity AND :max_capacity)
                ORDER BY r.is_available DESC, ABS(r.capacity - :capacity) ASC
                LIMIT :limit";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':current_room_id', $currentRoomId, PDO::PARAM_INT);
        $stmt->bindValue(':min_capacity', $capacity - 1, PDO::PARAM_INT);
        $stmt->bindValue(':max_capacity', $capacity + 1, PDO::PARAM_INT);
        $stmt->bindValue(':capacity', $capacity, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $rooms = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $media = null;
            if ($row['media_id']) {
                $media = new Media(
                    $row['media_id'],
                    $row['file_name'],
                    $row['file_path'],
                    $row['file_type'],
                    $row['file_size'],
                    $row['media_created']
                );
            }
            $rooms[] = new Room(
                $row['id'],
                $row['name'],
                (bool)$row['is_available'],
                (float)$row['price'],
                (int)$row['capacity'],
                $row['description'],
                $media,
                $row['created_at'],
                $row['updated_at']
            );
        }
        
                if (count($rooms) < $limit) {
            $additionalLimit = $limit - count($rooms);
            $existingIds = array_merge([$currentRoomId], array_map(function($room) {
                return $room->getId();
            }, $rooms));
            
            $placeholders = implode(',', array_fill(0, count($existingIds), '?'));
            
            $sql = "SELECT r.*, m.id as media_id, m.file_name, m.file_path, m.file_type, m.file_size, m.created_at as media_created
                    FROM Room r
                    LEFT JOIN Media m ON r.featured_image_id = m.id
                    WHERE r.id NOT IN ($placeholders)
                    ORDER BY r.is_available DESC, r.price
                    LIMIT ?";
                    
            $stmt = $this->db->prepare($sql);
            
                        $paramIndex = 1;
            foreach ($existingIds as $id) {
                $stmt->bindValue($paramIndex++, $id, PDO::PARAM_INT);
            }
            
            $stmt->bindValue($paramIndex, $additionalLimit, PDO::PARAM_INT);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $media = null;
                if ($row['media_id']) {
                    $media = new Media(
                        $row['media_id'],
                        $row['file_name'],
                        $row['file_path'],
                        $row['file_type'],
                        $row['file_size'],
                        $row['media_created']
                    );
                }
                $rooms[] = new Room(
                    $row['id'],
                    $row['name'],
                    (bool)$row['is_available'],
                    (float)$row['price'],
                    (int)$row['capacity'],
                    $row['description'],
                    $media,
                    $row['created_at'],
                    $row['updated_at']
                );
            }
        }
        
        return $rooms;
    }

    public function createRoom(Room $room): bool
    {
        $sql = "INSERT INTO Room (name, is_available, price, capacity, description, featured_image_id)
                VALUES (:name, :is_available, :price, :capacity, :description, :featured_image_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $room->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':is_available', $room->isAvailable(), PDO::PARAM_BOOL);
        $stmt->bindValue(':price', $room->getPrice());
        $stmt->bindValue(':capacity', $room->getCapacity(), PDO::PARAM_INT);
        $stmt->bindValue(':description', $room->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':featured_image_id', $room->getFeaturedImage()?->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateRoom(Room $room): bool
    {
        $sql = "UPDATE Room SET name = :name, is_available = :is_available, price = :price, capacity = :capacity,
                description = :description, featured_image_id = :featured_image_id WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $room->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':is_available', $room->isAvailable(), PDO::PARAM_BOOL);
        $stmt->bindValue(':price', $room->getPrice());
        $stmt->bindValue(':capacity', $room->getCapacity(), PDO::PARAM_INT);
        $stmt->bindValue(':description', $room->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':featured_image_id', $room->getFeaturedImage()?->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':id', $room->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

        public function updateRoomAvailability(int $roomId, bool $isAvailable): bool
    {
        $sql = "UPDATE Room SET is_available = :is_available WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $roomId, PDO::PARAM_INT);
        $stmt->bindValue(':is_available', $isAvailable, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function deleteRoom(int $id): bool
    {
        $sql = "DELETE FROM Room WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
