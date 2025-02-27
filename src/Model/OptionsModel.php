<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Options;
use PDO;

class OptionsModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllOptions(): array
    {
        $sql = "SELECT id_option, nom_option, prix_supplementaire FROM options ORDER BY nom_option";
        $stmt = $this->db->query($sql);
        $options = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $options[] = new Options($row['id_option'], $row['nom_option'], $row['prix_supplementaire']);
        }
        return $options;
    }

    public function updateOption(Options $option): bool
    {
        $sql = "UPDATE options SET nom_option = :nom_option, prix_supplementaire = :prix_supplementaire WHERE id_option = :id_option";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom_option', $option->getNom_option(), PDO::PARAM_STR);
        $stmt->bindValue(':prix_supplementaire', $option->getPrix_supplementaire(), PDO::PARAM_STR);
        $stmt->bindValue(':id_option', $option->getId_option(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneOption(int $id): ?Options
    {
        $sql = "SELECT id_option, nom_option, prix_supplementaire FROM options WHERE id_option = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Options($row['id_option'], $row['nom_option'], $row['prix_supplementaire']);
    }

    public function createOption(Options $option): bool
    {
        $sql = "INSERT INTO options (nom_option, prix_supplementaire) VALUES (:nom_option, :prix_supplementaire)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom_option', $option->getNom_option(), PDO::PARAM_STR);
        $stmt->bindValue(':prix_supplementaire', $option->getPrix_supplementaire(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteOption(int $id): bool
    {
        $sql = "DELETE FROM options WHERE id_option = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
