<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Contact;
use PDO;

class ContactModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllContacts(): array
    {
        $sql = "SELECT id_message, nom, prenom, telephone, message FROM contact ORDER BY id_message";
        $stmt = $this->db->query($sql);
        $contacts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $contacts[] = new Contact($row['id_message'], $row['nom'], $row['prenom'], $row['telephone'], $row['message']);
        }
        return $contacts;
    }

    public function updateContact(Contact $contact): bool
    {
        $sql = "UPDATE contact SET nom = :nom, prenom = :prenom, telephone = :telephone, message = :message WHERE id_message = :id_message";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom', $contact->getNom(), PDO::PARAM_STR);
        $stmt->bindValue(':prenom', $contact->getPrenom(), PDO::PARAM_STR);
        $stmt->bindValue(':telephone', $contact->getTelephone(), PDO::PARAM_STR);
        $stmt->bindValue(':message', $contact->getMessage(), PDO::PARAM_STR);
        $stmt->bindValue(':id_message', $contact->getId_message(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneContact(int $id): ?Contact
    {
        $sql = "SELECT id_message, nom, prenom, telephone, message FROM contact WHERE id_message = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Contact($row['id_message'], $row['nom'], $row['prenom'], $row['telephone'], $row['message']);
    }

    public function createContact(Contact $contact): bool
    {
        $sql = "INSERT INTO contact (nom, prenom, telephone, message) VALUES (:nom, :prenom, :telephone, :message)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom', $contact->getNom(), PDO::PARAM_STR);
        $stmt->bindValue(':prenom', $contact->getPrenom(), PDO::PARAM_STR);
        $stmt->bindValue(':telephone', $contact->getTelephone(), PDO::PARAM_STR);
        $stmt->bindValue(':message', $contact->getMessage(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteContact(int $id): bool
    {
        $sql = "DELETE FROM contact WHERE id_message = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
