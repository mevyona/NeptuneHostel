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

    public function createNotification(Notification $notification): bool
    {
        $sql = "INSERT INTO Notification (user_id, title, message, is_read, notification_type)
                VALUES (:user_id, :title, :message, :is_read, :notification_type)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $notification->getUser()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':title', $notification->getTitle(), PDO::PARAM_STR);
        $stmt->bindValue(':message', $notification->getMessage(), PDO::PARAM_STR);
        $stmt->bindValue(':is_read', $notification->isRead(), PDO::PARAM_BOOL);
        $stmt->bindValue(':notification_type', $notification->getNotificationType(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateNotification(Notification $notification): bool
    {
        $sql = "UPDATE Notification SET title = :title, message = :message, is_read = :is_read, notification_type = :notification_type WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':title', $notification->getTitle(), PDO::PARAM_STR);
        $stmt->bindValue(':message', $notification->getMessage(), PDO::PARAM_STR);
        $stmt->bindValue(':is_read', $notification->isRead(), PDO::PARAM_BOOL);
        $stmt->bindValue(':notification_type', $notification->getNotificationType(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $notification->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteNotification(int $id): bool
    {
        $sql = "DELETE FROM Notification WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}