<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Client;
use PDO;

class ClientModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllClients(): array
    {
        $sql = "SELECT id_client, nom_client, prenom_client, numero_telephone, email_client, numero_chambre, historique_reservation, mot_de_passe, adresse FROM client ORDER BY nom_client";
        $stmt = $this->db->query($sql);
        $clients = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $clients[] = new Client($row['id_client'], $row['nom_client'], $row['prenom_client'], $row['numero_telephone'], $row['email_client'], $row['numero_chambre'], $row['historique_reservation'], $row['mot_de_passe'], $row['adresse']);
        }
        return $clients;
    }

    public function updateClient(Client $client): bool
    {
        $sql = "UPDATE client SET nom_client = :nom_client, prenom_client = :prenom_client, numero_telephone = :numero_telephone, email_client = :email_client, numero_chambre = :numero_chambre, historique_reservation = :historique_reservation, mot_de_passe = :mot_de_passe, adresse = :adresse WHERE id_client = :id_client";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom_client', $client->getNom_client(), PDO::PARAM_STR);
        $stmt->bindValue(':prenom_client', $client->getPrenom_client(), PDO::PARAM_STR);
        $stmt->bindValue(':numero_telephone', $client->getNumero_telephone(), PDO::PARAM_STR);
        $stmt->bindValue(':email_client', $client->getEmail_client(), PDO::PARAM_STR);
        $stmt->bindValue(':numero_chambre', $client->getNumero_chambre(), PDO::PARAM_STR);
        $stmt->bindValue(':historique_reservation', $client->getHistorique_reservation(), PDO::PARAM_STR);
        $stmt->bindValue(':mot_de_passe', $client->getMot_de_passe(), PDO::PARAM_STR);
        $stmt->bindValue(':adresse', $client->getAdresse(), PDO::PARAM_STR);
        $stmt->bindValue(':id_client', $client->getId_client(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneClient(int $id): ?Client
    {
        $sql = "SELECT id_client, nom_client, prenom_client, numero_telephone, email_client, numero_chambre, historique_reservation, mot_de_passe, adresse FROM client WHERE id_client = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Client($row['id_client'], $row['nom_client'], $row['prenom_client'], $row['numero_telephone'], $row['email_client'], $row['numero_chambre'], $row['historique_reservation'], $row['mot_de_passe'], $row['adresse']);
    }

    public function createClient(Client $client): bool
    {
        $sql = "INSERT INTO client (nom_client, prenom_client, numero_telephone, email_client, numero_chambre, historique_reservation, mot_de_passe, adresse) VALUES (:nom_client, :prenom_client, :numero_telephone, :email_client, :numero_chambre, :historique_reservation, :mot_de_passe, :adresse)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom_client', $client->getNom_client(), PDO::PARAM_STR);
        $stmt->bindValue(':prenom_client', $client->getPrenom_client(), PDO::PARAM_STR);
        $stmt->bindValue(':numero_telephone', $client->getNumero_telephone(), PDO::PARAM_STR);
        $stmt->bindValue(':email_client', $client->getEmail_client(), PDO::PARAM_STR);
        $stmt->bindValue(':numero_chambre', $client->getNumero_chambre(), PDO::PARAM_STR);
        $stmt->bindValue(':historique_reservation', $client->getHistorique_reservation(), PDO::PARAM_STR);
        $stmt->bindValue(':mot_de_passe', $client->getMot_de_passe(), PDO::PARAM_STR);
        $stmt->bindValue(':adresse', $client->getAdresse(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteClient(int $id): bool
    {
        $sql = "DELETE FROM client WHERE id_client = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
