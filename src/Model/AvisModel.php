<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Avis;
use PDO;

class AvisModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllAvis(): array
    {
        $sql = "SELECT id_avis, note, commentaire, date_avis, id_client, num_chambre FROM avis ORDER BY date_avis DESC";
        $stmt = $this->db->query($sql);
        $avis = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $avis[] = new Avis($row['id_avis'], $row['note'], $row['commentaire'], $row['date_avis'], $row['id_client'], $row['num_chambre']);
        }
        return $avis;
    }

    public function updateAvis(Avis $avis): bool
    {
        $sql = "UPDATE avis SET note = :note, commentaire = :commentaire, date_avis = :date_avis, id_client = :id_client, num_chambre = :num_chambre WHERE id_avis = :id_avis";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':note', $avis->getNote(), PDO::PARAM_STR);
        $stmt->bindValue(':commentaire', $avis->getCommentaire(), PDO::PARAM_STR);
        $stmt->bindValue(':date_avis', $avis->getDate_avis(), PDO::PARAM_STR);
        $stmt->bindValue(':id_client', $avis->getId_client(), PDO::PARAM_INT);
        $stmt->bindValue(':num_chambre', $avis->getNum_chambre(), PDO::PARAM_INT);
        $stmt->bindValue(':id_avis', $avis->getId_avis(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneAvis(int $id): ?Avis
    {
        $sql = "SELECT id_avis, note, commentaire, date_avis, id_client, num_chambre FROM avis WHERE id_avis = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Avis($row['id_avis'], $row['note'], $row['commentaire'], $row['date_avis'], $row['id_client'], $row['num_chambre']);
    }

    public function createAvis(Avis $avis): bool
    {
        $sql = "INSERT INTO avis (note, commentaire, date_avis, id_client, num_chambre) VALUES (:note, :commentaire, :date_avis, :id_client, :num_chambre)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':note', $avis->getNote(), PDO::PARAM_STR);
        $stmt->bindValue(':commentaire', $avis->getCommentaire(), PDO::PARAM_STR);
        $stmt->bindValue(':date_avis', $avis->getDate_avis(), PDO::PARAM_STR);
        $stmt->bindValue(':id_client', $avis->getId_client(), PDO::PARAM_INT);
        $stmt->bindValue(':num_chambre', $avis->getNum_chambre(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteAvis(int $id): bool
    {
        $sql = "DELETE FROM avis WHERE id_avis = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
