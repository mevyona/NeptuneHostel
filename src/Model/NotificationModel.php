<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Notification;
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
        $sql = "SELECT id_notification, message, date_envoi, statut, id_client FROM notification ORDER BY date_envoi DESC";
        $stmt = $this->db->query($sql);
        $notifications = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $notifications[] = new Notification($row['id_notification'], $row['message'], $row['date_envoi'], $row['statut'], $row['id_client']);
        }
        return $notifications;
    }

    public function updateNotification(Notification $notification): bool
    {
        $sql = "UPDATE notification SET message = :message, date_envoi = :date_envoi, statut = :statut WHERE id_notification = :id_notification";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':message', $notification->getMessage(), PDO::PARAM_STR);
        $stmt->bindValue(':date_envoi', $notification->getDate_envoi(), PDO::PARAM_STR);
        $stmt->bindValue(':statut', $notification->getStatut(), PDO::PARAM_STR);
        $stmt->bindValue(':id_notification', $notification->getId_notification(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneNotification(int $id): ?Notification
    {
        $sql = "SELECT id_notification, message, date_envoi, statut, id_client FROM notification WHERE id_notification = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Notification($row['id_notification'], $row['message'], $row['date_envoi'], $row['statut'], $row['id_client']);
    }

    public function createNotification(Notification $notification): bool
    {
        $sql = "INSERT INTO notification (message, date_envoi, statut, id_client) VALUES (:message, :date_envoi, :statut, :id_client)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':message', $notification->getMessage(), PDO::PARAM_STR);
        $stmt->bindValue(':date_envoi', $notification->getDate_envoi(), PDO::PARAM_STR);
        $stmt->bindValue(':statut', $notification->getStatut(), PDO::PARAM_STR);
        $stmt->bindValue(':id_client', $notification->getId_client(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteNotification(int $id): bool
    {
        $sql = "DELETE FROM notification WHERE id_notification = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
