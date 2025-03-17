<?php
declare(strict_types=1);
namespace MyApp\Model;

use MyApp\Entity\User;
use PDO;

class UserModel
{
    private PDO $db;
    
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function createUser(User $user): bool
    {
        $sql = "INSERT INTO User (first_name, last_name, email, phone, password, role) VALUES
                (:first_name, :last_name, :email, :phone, :password, :role)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':first_name', $user->getFirstName(), PDO::PARAM_STR);
        $stmt->bindValue(':last_name', $user->getLastName(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':phone', $user->getPhone(), PDO::PARAM_STR);
        $stmt->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
        $stmt->bindValue(':role', $user->getRole(), PDO::PARAM_STR);
        
        $result = $stmt->execute();
        
        if ($result) {
            $user->setId((int)$this->db->lastInsertId());
        }
        
        return $result;
    }

    public function getAllUsers(): array
    {
        $sql = "SELECT id, first_name, last_name, email, phone, password, role, created_at, updated_at 
                FROM User ORDER BY last_name, first_name";
        $stmt = $this->db->query($sql);
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                $row['id'], 
                $row['first_name'], 
                $row['last_name'], 
                $row['email'], 
                $row['phone'], 
                $row['password'], 
                $row['role'],
                $row['created_at'],
                $row['updated_at']
            );
        }
        return $users;
    }

    public function getUserByEmail(string $email): ?User
    {
        $sql = "SELECT id, first_name, last_name, email, phone, password, role, created_at, updated_at 
                FROM User WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new User(
            $row['id'], 
            $row['first_name'], 
            $row['last_name'], 
            $row['email'], 
            $row['phone'], 
            $row['password'], 
            $row['role'],
            $row['created_at'],
            $row['updated_at']
        );
    }
    
    public function getOneUser(int $id): ?User
    {
        $sql = "SELECT id, first_name, last_name, email, phone, password, role, created_at, updated_at 
                FROM User WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new User(
            $row['id'], 
            $row['first_name'], 
            $row['last_name'], 
            $row['email'], 
            $row['phone'], 
            $row['password'], 
            $row['role'],
            $row['created_at'],
            $row['updated_at']
        );
    }
    
    public function updateUser(User $user): bool
    {
        $sql = "UPDATE User SET 
                first_name = :first_name, 
                last_name = :last_name, 
                email = :email, 
                phone = :phone, 
                role = :role 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':first_name', $user->getFirstName(), PDO::PARAM_STR);
        $stmt->bindValue(':last_name', $user->getLastName(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':phone', $user->getPhone(), PDO::PARAM_STR);
        $stmt->bindValue(':role', $user->getRole(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $user->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function updateUserPassword(int $id, string $passwordHash): bool
    {
        $sql = "UPDATE User SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':password', $passwordHash, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function deleteUser(int $id): bool
    {
        $sql = "DELETE FROM User WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function getTotalUsers(): int
    {
        $sql = "SELECT COUNT(*) FROM User";
        return (int)$this->db->query($sql)->fetchColumn();
    }
    
    public function getRecentUsers(int $limit = 5): array
    {
        $sql = "SELECT id, first_name, last_name, email, phone, password, role, created_at, updated_at 
                FROM User ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                $row['id'], 
                $row['first_name'], 
                $row['last_name'], 
                $row['email'], 
                $row['phone'], 
                $row['password'], 
                $row['role'],
                $row['created_at'],
                $row['updated_at']
            );
        }
        return $users;
    }
    
    public function getUsersByRole(string $role): array
    {
        $sql = "SELECT id, first_name, last_name, email, phone, password, role, created_at, updated_at 
                FROM User WHERE role = :role ORDER BY last_name, first_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        $stmt->execute();
        
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                $row['id'], 
                $row['first_name'], 
                $row['last_name'], 
                $row['email'], 
                $row['phone'], 
                $row['password'], 
                $row['role'],
                $row['created_at'],
                $row['updated_at']
            );
        }
        return $users;
    }
    
    // Relations - récupération des entités liées
    
    public function getUserReservations(int $userId): array
    {
        $sql = "SELECT r.* FROM Reservation r 
                WHERE r.user_id = :user_id 
                ORDER BY r.check_in DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $reservations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Nécessite une classe ReservationModel pour construire les objets
            // $reservations[] = new Reservation(...);
            $reservations[] = $row; // Retourne les données brutes en attendant
        }
        return $reservations;
    }
    
    public function getUserReviews(int $userId): array
    {
        $sql = "SELECT r.* FROM Review r 
                WHERE r.user_id = :user_id 
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $reviews = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Nécessite une classe ReviewModel pour construire les objets
            // $reviews[] = new Review(...);
            $reviews[] = $row; // Retourne les données brutes en attendant
        }
        return $reviews;
    }
    
    public function getUserNotifications(int $userId): array
    {
        $sql = "SELECT n.* FROM Notification n 
                WHERE n.user_id = :user_id 
                ORDER BY n.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $notifications = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Nécessite une classe NotificationModel pour construire les objets
            // $notifications[] = new Notification(...);
            $notifications[] = $row; // Retourne les données brutes en attendant
        }
        return $notifications;
    }
    
    public function getUserCancellations(int $userId): array
    {
        $sql = "SELECT c.* FROM Cancellation c 
                WHERE c.cancelled_by_id = :user_id 
                ORDER BY c.cancellation_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $cancellations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Nécessite une classe CancellationModel pour construire les objets
            // $cancellations[] = new Cancellation(...);
            $cancellations[] = $row; // Retourne les données brutes en attendant
        }
        return $cancellations;
    }
    
    // Méthode pour charger toutes les relations d'un utilisateur
    public function loadUserRelations(User $user): void
    {
        if ($user->getId() === null) {
            return;
        }
        
        $userId = $user->getId();
        $reservations = $this->getUserReservations($userId);
        $reviews = $this->getUserReviews($userId);
        $notifications = $this->getUserNotifications($userId);
        $cancellations = $this->getUserCancellations($userId);
        
        $user->setReservations($reservations);
        $user->setReviews($reviews);
        $user->setNotifications($notifications);
        $user->setCancellations($cancellations);
    }
}