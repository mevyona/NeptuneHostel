<?php
declare(strict_types=1);
namespace MyApp\Model;

use MyApp\Entity\User;
use MyApp\Entity\Reservation;
use MyApp\Entity\Review;
use MyApp\Entity\Notification;
use MyApp\Entity\Cancellation;
use MyApp\Entity\Room;
use PDO;

class UserModel
{
    private PDO $db;
    private ?ReservationModel $reservationModel = null;
    private ?ReviewModel $reviewModel = null;
    private ?NotificationModel $notificationModel = null;
    private ?CancellationModel $cancellationModel = null;
    private ?RoomModel $roomModel = null;
    private $dependencyContainer;
    
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    
    // Méthodes d'accès aux autres modèles
    private function getReservationModel(): ReservationModel
    {
        if ($this->reservationModel === null) {
            $this->reservationModel = new ReservationModel($this->db);
        }
        return $this->reservationModel;
    }
    
    private function getReviewModel(): ReviewModel
    {
        if ($this->reviewModel === null) {
            $this->reviewModel = new ReviewModel($this->db);
        }
        return $this->reviewModel;
    }
    
    private function getNotificationModel(): NotificationModel
    {
        if ($this->notificationModel === null) {
            $this->notificationModel = new NotificationModel($this->db);
        }
        return $this->notificationModel;
    }
    
    private function getCancellationModel(): CancellationModel
    {
        if ($this->cancellationModel === null) {
            $this->cancellationModel = new CancellationModel($this->db);
        }
        return $this->cancellationModel;
    }
    
    private function getRoomModel(): RoomModel
    {
        if ($this->roomModel === null) {
            $this->roomModel = new RoomModel($this->db);
        }
        return $this->roomModel;
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
    
    /**
     * Récupère les réservations de l'utilisateur
     */
    public function getUserReservations(int $userId): array
    {
        // Vérifier si le modèle de réservation est accessible
        if (!isset($this->dependencyContainer) || !$this->dependencyContainer->has('ReservationModel')) {
            return [];
        }
        
        $reservationModel = $this->dependencyContainer->get('ReservationModel');
        
        // Appeler la méthode correcte "getUserReservations" au lieu de "getReservationsByUserId"
        return $reservationModel->getUserReservations($userId);
    }
    
    /**
     * Charge toutes les relations d'un utilisateur (réservations, avis, etc.)
     */
    public function getUserRelationsArray(int $userId): array
    {
        $relations = [];
        
        // Charger les réservations
        $relations['reservations'] = $this->getUserReservations($userId);
        
        // Autres relations...
        
        return $relations;
    }
    
    public function getUserReviews(int $userId): array
    {
        return $this->getReviewModel()->getReviewsByUserId($userId);
    }
    
    public function getUserNotifications(int $userId): array
    {
        return $this->getNotificationModel()->getNotificationsByUserId($userId);
    }
    
    public function getUserCancellations(int $userId): array
    {
        return $this->getCancellationModel()->getCancellationsByUserId($userId);
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