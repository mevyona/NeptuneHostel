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

    /**
     * Crée un nouveau message de contact
     */
    public function createContactMessage(ContactMessage $contactMessage): bool
    {
        try {
            $query = "INSERT INTO contact_messages (first_name, last_name, email, phone, message, status, created_at) 
                      VALUES (:first_name, :last_name, :email, :phone, :message, :status, :created_at)";
                      
            $stmt = $this->db->prepare($query);
            
            $firstName = $contactMessage->getFirstName();
            $lastName = $contactMessage->getLastName();
            $email = $contactMessage->getEmail();
            $phone = $contactMessage->getPhone();
            $message = $contactMessage->getMessage();
            $status = $contactMessage->getStatus();
            $createdAt = $contactMessage->getCreatedAt()->format('Y-m-d H:i:s');
            
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':created_at', $createdAt);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erreur lors de la création du message de contact: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les messages de contact
     */
    public function getAllContactMessages(): array
    {
        try {
            $query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
            $stmt = $this->db->query($query);
            
            $contactMessages = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $contactMessages[] = $this->createContactMessageFromRow($row);
            }
            
            return $contactMessages;
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération des messages de contact: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère un message de contact par son ID
     */
    public function getContactMessageById(int $id): ?ContactMessage
    {
        try {
            $query = "SELECT * FROM contact_messages WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return null;
            }
            
            return $this->createContactMessageFromRow($row);
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération du message de contact: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Met à jour le statut d'un message de contact
     */
    public function updateContactMessageStatus(int $id, string $status): bool
    {
        try {
            $query = "UPDATE contact_messages SET status = :status WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erreur lors de la mise à jour du statut du message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime un message de contact
     */
    public function deleteContactMessage(int $id): bool
    {
        try {
            $query = "DELETE FROM contact_messages WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erreur lors de la suppression du message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Crée un objet ContactMessage à partir d'une ligne de base de données
     */
    private function createContactMessageFromRow(array $row): ContactMessage
    {
        return new ContactMessage(
            (int) $row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['phone'],
            $row['message'],
            $row['status'],
            new \DateTime($row['created_at'])
        );
    }
}