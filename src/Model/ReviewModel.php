<?php
declare(strict_types=1);

namespace MyApp\Model;

use MyApp\Entity\Review;
use MyApp\Entity\User;
use MyApp\Entity\Room;
use MyApp\Entity\Reservation;
use PDO;

class ReviewModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllReviews(): array
    {
        $sql = "SELECT r.*, 
                       u.id as user_id, u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated,
                       rm.id as room_id, rm.name as room_name, rm.is_available, rm.price, rm.capacity, rm.description, rm.featured_image_id, rm.created_at as room_created, rm.updated_at as room_updated,
                       res.id as reservation_id, res.check_in, res.check_out, res.status as reservation_status, res.total_price, res.special_requests, res.created_at as reservation_created, res.updated_at as reservation_updated
                FROM Review r
                INNER JOIN User u ON r.user_id = u.id
                INNER JOIN Room rm ON r.room_id = rm.id
                INNER JOIN Reservation res ON r.reservation_id = res.id
                ORDER BY r.created_at DESC";

        $stmt = $this->db->query($sql);
        $reviews = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User(
                $row['user_id'], $row['first_name'], $row['last_name'], $row['email'],
                $row['phone'], $row['password'], $row['role'], $row['user_created'], $row['user_updated']
            );

            $room = new Room(
                $row['room_id'], $row['room_name'], (bool)$row['is_available'], (float)$row['price'],
                (int)$row['capacity'], $row['description'], null, $row['room_created'], $row['room_updated']
            );

            $reservation = new Reservation(
                $row['reservation_id'], $user, $room, $row['check_in'], $row['check_out'],
                $row['reservation_status'], $row['total_price'], $row['special_requests'], $row['reservation_created'], $row['reservation_updated']
            );

            $reviews[] = new Review(
                $row['id'], $user, $room, $reservation, $row['rating'], $row['comment'], $row['created_at']
            );
        }

        return $reviews;
    }

    public function getOneReview(int $id): ?Review
    {
        $sql = "SELECT r.*, 
                       u.id as user_id, u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated,
                       rm.id as room_id, rm.name as room_name, rm.is_available, rm.price, rm.capacity, rm.description, rm.featured_image_id, rm.created_at as room_created, rm.updated_at as room_updated,
                       res.id as reservation_id, res.check_in, res.check_out, res.status as reservation_status, res.total_price, res.special_requests, res.created_at as reservation_created, res.updated_at as reservation_updated
                FROM Review r
                INNER JOIN User u ON r.user_id = u.id
                INNER JOIN Room rm ON r.room_id = rm.id
                INNER JOIN Reservation res ON r.reservation_id = res.id
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

        $reservation = new Reservation(
            $row['reservation_id'], $user, $room, $row['check_in'], $row['check_out'],
            $row['reservation_status'], $row['total_price'], $row['special_requests'], $row['reservation_created'], $row['reservation_updated']
        );

        return new Review(
            $row['id'], $user, $room, $reservation, $row['rating'], $row['comment'], $row['created_at']
        );
    }

    public function getReviewsByUserId(int $userId): array
    {
        $sql = "SELECT r.*, 
            u.id as user_id, u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated,
            rm.id as room_id, rm.name as room_name, rm.is_available, rm.price, rm.capacity, rm.description, rm.featured_image_id, rm.created_at as room_created, rm.updated_at as room_updated,
            res.id as reservation_id, res.check_in, res.check_out, res.status as reservation_status, res.total_price, res.special_requests, res.created_at as reservation_created, res.updated_at as reservation_updated
        FROM Review r
        INNER JOIN User u ON r.user_id = u.id
        INNER JOIN Room rm ON r.room_id = rm.id
        INNER JOIN Reservation res ON r.reservation_id = res.id
        WHERE r.user_id = :user_id
        ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $reviews = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User(
                $row['user_id'], $row['first_name'], $row['last_name'], $row['email'],
                $row['phone'], $row['password'], $row['role'], $row['user_created'], $row['user_updated']
            );
            
            $room = new Room(
                $row['room_id'], $row['room_name'], (bool)$row['is_available'], (float)$row['price'],
                (int)$row['capacity'], $row['description'], null, $row['room_created'], $row['room_updated']
            );
            
            $reservation = new Reservation(
                $row['reservation_id'], $user, $room, $row['check_in'], $row['check_out'],
                $row['reservation_status'], (float)$row['total_price'], $row['special_requests'],
                $row['reservation_created'], $row['reservation_updated']
            );
            
            $reviews[] = new Review(
                $row['id'], $user, $room, $reservation, (float)$row['rating'],
                $row['comment'], $row['created_at']
            );
        }
        return $reviews;
    }

    public function createReview(Review $review): bool
    {
        $sql = "INSERT INTO Review (user_id, room_id, reservation_id, rating, comment)
                VALUES (:user_id, :room_id, :reservation_id, :rating, :comment)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $review->getUser()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':room_id', $review->getRoom()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':reservation_id', $review->getReservation()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':rating', $review->getRating());
        $stmt->bindValue(':comment', $review->getComment(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateReview(Review $review): bool
    {
        $sql = "UPDATE Review SET rating = :rating, comment = :comment WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':rating', $review->getRating());
        $stmt->bindValue(':comment', $review->getComment(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $review->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteReview(int $id): bool
    {
        $sql = "DELETE FROM Review WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
