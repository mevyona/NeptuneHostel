<?php
declare(strict_types=1);

namespace MyApp\Model;

use MyApp\Entity\ContactMessage;
use PDO;

class ContactMessageModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllContactMessages(): array
    {
        $sql = "SELECT * FROM ContactMessage ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        $messages = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = new ContactMessage(
                $row['id'],
                $row['first_name'],
                $row['last_name'],
                $row['email'],
                $row['phone'],
                $row['message'],
                $row['status'],
                $row['created_at']
            );
        }

        return $messages;
    }

    public function getOneContactMessage(int $id): ?ContactMessage
    {
        $sql = "SELECT * FROM ContactMessage WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new ContactMessage(
            $row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['phone'],
            $row['message'],
            $row['status'],
            $row['created_at']
        );
    }

    public function createContactMessage(ContactMessage $message): bool
    {
        $sql = "INSERT INTO ContactMessage (first_name, last_name, email, phone, message, status)
                VALUES (:first_name, :last_name, :email, :phone, :message, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':first_name', $message->getFirstName(), PDO::PARAM_STR);
        $stmt->bindValue(':last_name', $message->getLastName(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $message->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':phone', $message->getPhone(), PDO::PARAM_STR);
        $stmt->bindValue(':message', $message->getMessage(), PDO::PARAM_STR);
        $stmt->bindValue(':status', $message->getStatus(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateContactMessage(ContactMessage $message): bool
    {
        $sql = "UPDATE ContactMessage SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':status', $message->getStatus(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $message->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteContactMessage(int $id): bool
    {
        $sql = "DELETE FROM ContactMessage WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}