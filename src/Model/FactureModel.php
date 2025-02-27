<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Facture;
use PDO;

class FactureModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllFactures(): array
    {
        $sql = "SELECT id_facture, numero_cb, date_facture, chemin_pdf, montant_total FROM facture ORDER BY date_facture";
        $stmt = $this->db->query($sql);
        $factures = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $factures[] = new Facture($row['id_facture'], $row['numero_cb'], $row['date_facture'], $row['chemin_pdf'], $row['montant_total']);
        }
        return $factures;
    }

    public function updateFacture(Facture $facture): bool
    {
        $sql = "UPDATE facture SET numero_cb = :numero_cb, date_facture = :date_facture, chemin_pdf = :chemin_pdf, montant_total = :montant_total WHERE id_facture = :id_facture";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':numero_cb', $facture->getNumero_cb(), PDO::PARAM_STR);
        $stmt->bindValue(':date_facture', $facture->getDate_facture(), PDO::PARAM_STR);
        $stmt->bindValue(':chemin_pdf', $facture->getChemin_pdf(), PDO::PARAM_STR);
        $stmt->bindValue(':montant_total', $facture->getMontant_total(), PDO::PARAM_STR);
        $stmt->bindValue(':id_facture', $facture->getId_facture(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneFacture(int $id): ?Facture
    {
        $sql = "SELECT id_facture, numero_cb, date_facture, chemin_pdf, montant_total FROM facture WHERE id_facture = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Facture($row['id_facture'], $row['numero_cb'], $row['date_facture'], $row['chemin_pdf'], $row['montant_total']);
    }

    public function createFacture(Facture $facture): bool
    {
        $sql = "INSERT INTO facture (numero_cb, date_facture, chemin_pdf, montant_total) VALUES (:numero_cb, :date_facture, :chemin_pdf, :montant_total)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':numero_cb', $facture->getNumero_cb(), PDO::PARAM_STR);
        $stmt->bindValue(':date_facture', $facture->getDate_facture(), PDO::PARAM_STR);
        $stmt->bindValue(':chemin_pdf', $facture->getChemin_pdf(), PDO::PARAM_STR);
        $stmt->bindValue(':montant_total', $facture->getMontant_total(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteFacture(int $id): bool
    {
        $sql = "DELETE FROM facture WHERE id_facture = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
