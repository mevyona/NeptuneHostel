<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Annulation;
use PDO;

class AnnulationModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllAnnulations(): array
    {
        $sql = "SELECT id_annulation, motif_annulation, date_annulation, num_reservation FROM annulation ORDER BY date_annulation DESC";
        $stmt = $this->db->query($sql);
        $annulations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $annulations[] = new Annulation($row['id_annulation'], $row['motif_annulation'], $row['date_annulation'], $row['num_reservation']);
        }
        return $annulations;
    }

    public function updateAnnulation(Annulation $annulation): bool
    {
        $sql = "UPDATE annulation SET motif_annulation = :motif_annulation, date_annulation = :date_annulation, num_reservation = :num_reservation WHERE id_annulation = :id_annulation";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':motif_annulation', $annulation->getMotif_annulation(), PDO::PARAM_STR);
        $stmt->bindValue(':date_annulation', $annulation->getDate_annulation(), PDO::PARAM_STR);
        $stmt->bindValue(':num_reservation', $annulation->getNum_reservation(), PDO::PARAM_INT);
        $stmt->bindValue(':id_annulation', $annulation->getId_annulation(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneAnnulation(int $id): ?Annulation
    {
        $sql = "SELECT id_annulation, motif_annulation, date_annulation, num_reservation FROM annulation WHERE id_annulation = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Annulation($row['id_annulation'], $row['motif_annulation'], $row['date_annulation'], $row['num_reservation']);
    }

    public function createAnnulation(Annulation $annulation): bool
    {
        $sql = "INSERT INTO annulation (motif_annulation, date_annulation, num_reservation) VALUES (:motif_annulation, :date_annulation, :num_reservation)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':motif_annulation', $annulation->getMotif_annulation(), PDO::PARAM_STR);
        $stmt->bindValue(':date_annulation', $annulation->getDate_annulation(), PDO::PARAM_STR);
        $stmt->bindValue(':num_reservation', $annulation->getNum_reservation(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteAnnulation(int $id): bool
    {
        $sql = "DELETE FROM annulation WHERE id_annulation = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
