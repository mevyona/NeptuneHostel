<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Chambre;
use PDO;

class ChambreModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllChambres(): array
    {
        $sql = "SELECT num_chambre, chambre_disponible, prix_chambre, capacite, description, nom_chambre, id_photos FROM chambre ORDER BY nom_chambre";
        $stmt = $this->db->query($sql);
        $chambres = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $chambres[] = new Chambre($row['num_chambre'], $row['chambre_disponible'], $row['prix_chambre'], $row['capacite'], $row['description'], $row['nom_chambre'], $row['id_photos']);
        }
        return $chambres;
    }

    public function updateChambre(Chambre $chambre): bool
    {
        $sql = "UPDATE chambre SET chambre_disponible = :chambre_disponible, prix_chambre = :prix_chambre, capacite = :capacite, description = :description, nom_chambre = :nom_chambre, id_photos = :id_photos WHERE num_chambre = :num_chambre";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':chambre_disponible', $chambre->getChambre_disponible(), PDO::PARAM_BOOL);
        $stmt->bindValue(':prix_chambre', $chambre->getPrix_chambre(), PDO::PARAM_STR);
        $stmt->bindValue(':capacite', $chambre->getCapacite(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $chambre->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':nom_chambre', $chambre->getNom_chambre(), PDO::PARAM_STR);
        $stmt->bindValue(':id_photos', $chambre->getId_photos(), PDO::PARAM_INT);
        $stmt->bindValue(':num_chambre', $chambre->getNum_chambre(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneChambre(int $id): ?Chambre
    {
        $sql = "SELECT num_chambre, chambre_disponible, prix_chambre, capacite, description, nom_chambre, id_photos FROM chambre WHERE num_chambre = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Chambre($row['num_chambre'], $row['chambre_disponible'], $row['prix_chambre'], $row['capacite'], $row['description'], $row['nom_chambre'], $row['id_photos']);
    }

    public function createChambre(Chambre $chambre): bool
    {
        $sql = "INSERT INTO chambre (chambre_disponible, prix_chambre, capacite, description, nom_chambre, id_photos) VALUES (:chambre_disponible, :prix_chambre, :capacite, :description, :nom_chambre, :id_photos)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':chambre_disponible', $chambre->getChambre_disponible(), PDO::PARAM_BOOL);
        $stmt->bindValue(':prix_chambre', $chambre->getPrix_chambre(), PDO::PARAM_STR);
        $stmt->bindValue(':capacite', $chambre->getCapacite(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $chambre->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':nom_chambre', $chambre->getNom_chambre(), PDO::PARAM_STR);
        $stmt->bindValue(':id_photos', $chambre->getId_photos(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteChambre(int $id): bool
    {
        $sql = "DELETE FROM chambre WHERE num_chambre = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
