<?php
declare(strict_types=1);

namespace MyApp\Model;

use MyApp\Entity\Notification;
use MyApp\Entity\User;
use PDO;

class NotificationModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Récupère toutes les notifications
     */
    public function getAllNotifications(): array
    {
        $sql = "SELECT n.*, u.id as user_id, u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated
                FROM Notification n
                INNER JOIN User u ON n.user_id = u.id
                ORDER BY n.created_at DESC";

        $stmt = $this->db->query($sql);
        $notifications = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User(
                $row['user_id'], $row['first_name'], $row['last_name'], $row['email'],
                $row['phone'], $row['password'], $row['role'], $row['user_created'], $row['user_updated']
            );

            $notifications[] = new Notification(
                $row['id'], $user, $row['title'], $row['message'], (bool)$row['is_read'], $row['notification_type'], $row['created_at']
            );
        }

        return $notifications;
    }

    /**
     * Récupère une notification par son ID
     */
    public function getOneNotification(int $id): ?Notification
    {
        $sql = "SELECT n.*, u.id as user_id, u.first_name, u.last_name, u.email, u.phone, u.password, u.role, u.created_at as user_created, u.updated_at as user_updated
                FROM Notification n
                INNER JOIN User u ON n.user_id = u.id
                WHERE n.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        $user = new User(
            $row['user_id'], $row['first_name'], $row['last_name'], $row['email'],
            $row['phone'], $row['password'], $row['role'], $row['user_created'], $row['user_updated']
        );

        return new Notification(
            $row['id'], $user, $row['title'], $row['message'], (bool)$row['is_read'], $row['notification_type'], $row['created_at']
        );
    }

    /**
     * Récupère toutes les notifications d'un utilisateur
     */
    public function getNotificationsByUserId(int $userId): array
    {
        try {
            $query = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            $notifications = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $notifications[] = $this->createNotificationFromRow($row);
            }
            
            return $notifications;
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération des notifications: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Crée une nouvelle notification
     */
    public function createNotification(Notification $notification): bool
    {
        try {
            $query = "INSERT INTO notifications (user_id, title, message, is_read, created_at) 
                      VALUES (:user_id, :title, :message, :is_read, :created_at)";
                      
            $stmt = $this->db->prepare($query);
            
            $userId = $notification->getUserId();
            $title = $notification->getTitle();
            $message = $notification->getMessage();
            $isRead = $notification->isRead() ? 1 : 0;
            $createdAt = $notification->getCreatedAt()->format('Y-m-d H:i:s');
            
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':is_read', $isRead, PDO::PARAM_INT);
            $stmt->bindParam(':created_at', $createdAt);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erreur lors de la création de la notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Marque une notification comme lue
     */
    public function markAsRead(int $id): bool
    {
        try {
            $query = "UPDATE notifications SET is_read = 1 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erreur lors de la mise à jour de la notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime une notification
     */
    public function deleteNotification(int $id): bool
    {
        try {
            $query = "DELETE FROM notifications WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erreur lors de la suppression de la notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Crée un objet Notification à partir d'une ligne de base de données
     */
    private function createNotificationFromRow(array $row): Notification
    {
        return new Notification(
            (int) $row['id'],
            (int) $row['user_id'],
            $row['title'],
            $row['message'],
            (bool) $row['is_read'],
            new \DateTime($row['created_at'])
        );
    }
}