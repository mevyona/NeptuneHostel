<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Paiement;
use PDO;

class PaiementModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllPaiements(): array
    {
        $sql = "SELECT id_paiement, numero_cb, date_expiration, ccv_cb, montant, statut, id_facture, num_reservation FROM paiement ORDER BY id_paiement DESC";
        $stmt = $this->db->query($sql);
        $paiements = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $paiements[] = new Paiement($row['id_paiement'], $row['numero_cb'], $row['date_expiration'], $row['ccv_cb'], $row['montant'], $row['statut'], $row['id_facture'], $row['num_reservation']);
        }
        return $paiements;
    }

    public function updatePaiement(Paiement $paiement): bool
    {
        $sql = "UPDATE paiement SET numero_cb = :numero_cb, date_expiration = :date_expiration, ccv_cb = :ccv_cb, montant = :montant, statut = :statut, id_facture = :id_facture, num_reservation = :num_reservation WHERE id_paiement = :id_paiement";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':numero_cb', $paiement->getNumero_cb(), PDO::PARAM_STR);
        $stmt->bindValue(':date_expiration', $paiement->getDate_expiration(), PDO::PARAM_STR);
        $stmt->bindValue(':ccv_cb', $paiement->getCcv_cb(), PDO::PARAM_STR);
        $stmt->bindValue(':montant', $paiement->getMontant(), PDO::PARAM_STR);
        $stmt->bindValue(':statut', $paiement->getStatut(), PDO::PARAM_STR);
        $stmt->bindValue(':id_facture', $paiement->getId_facture(), PDO::PARAM_INT);
        $stmt->bindValue(':num_reservation', $paiement->getNum_reservation(), PDO::PARAM_INT);
        $stmt->bindValue(':id_paiement', $paiement->getId_paiement(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOnePaiement(int $id): ?Paiement
    {
        $sql = "SELECT id_paiement, numero_cb, date_expiration, ccv_cb, montant, statut, id_facture, num_reservation FROM paiement WHERE id_paiement = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Paiement($row['id_paiement'], $row['numero_cb'], $row['date_expiration'], $row['ccv_cb'], $row['montant'], $row['statut'], $row['id_facture'], $row['num_reservation']);
    }

    public function createPaiement(Paiement $paiement): bool
    {
        $sql = "INSERT INTO paiement (numero_cb, date_expiration, ccv_cb, montant, statut, id_facture, num_reservation) VALUES (:numero_cb, :date_expiration, :ccv_cb, :montant, :statut, :id_facture, :num_reservation)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':numero_cb', $paiement->getNumero_cb(), PDO::PARAM_STR);
        $stmt->bindValue(':date_expiration', $paiement->getDate_expiration(), PDO::PARAM_STR);
        $stmt->bindValue(':ccv_cb', $paiement->getCcv_cb(), PDO::PARAM_STR);
        $stmt->bindValue(':montant', $paiement->getMontant(), PDO::PARAM_STR);
        $stmt->bindValue(':statut', $paiement->getStatut(), PDO::PARAM_STR);
        $stmt->bindValue(':id_facture', $paiement->getId_facture(), PDO::PARAM_INT);
        $stmt->bindValue(':num_reservation', $paiement->getNum_reservation(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deletePaiement(int $id): bool
    {
        $sql = "DELETE FROM paiement WHERE id_paiement = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
