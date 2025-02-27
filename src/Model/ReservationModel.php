<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Reservation;
use PDO;

class ReservationModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllReservations(): array
    {
        $sql = "SELECT num_reservation, date_debut, date_fin, facture_reservation, date_reservation, statut, id_client, num_chambre FROM reservation ORDER BY date_reservation DESC";
        $stmt = $this->db->query($sql);
        $reservations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reservations[] = new Reservation($row['num_reservation'], $row['date_debut'], $row['date_fin'], $row['facture_reservation'], $row['date_reservation'], $row['statut'], $row['id_client'], $row['num_chambre']);
        }
        return $reservations;
    }

    public function updateReservation(Reservation $reservation): bool
    {
        $sql = "UPDATE reservation SET date_debut = :date_debut, date_fin = :date_fin, facture_reservation = :facture_reservation, date_reservation = :date_reservation, statut = :statut, id_client = :id_client, num_chambre = :num_chambre WHERE num_reservation = :num_reservation";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':date_debut', $reservation->getDate_debut(), PDO::PARAM_STR);
        $stmt->bindValue(':date_fin', $reservation->getDate_fin(), PDO::PARAM_STR);
        $stmt->bindValue(':facture_reservation', $reservation->getFacture_reservation(), PDO::PARAM_STR);
        $stmt->bindValue(':date_reservation', $reservation->getDate_reservation(), PDO::PARAM_STR);
        $stmt->bindValue(':statut', $reservation->getStatut(), PDO::PARAM_STR);
        $stmt->bindValue(':id_client', $reservation->getId_client(), PDO::PARAM_INT);
        $stmt->bindValue(':num_chambre', $reservation->getNum_chambre(), PDO::PARAM_INT);
        $stmt->bindValue(':num_reservation', $reservation->getNum_reservation(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneReservation(int $id): ?Reservation
    {
        $sql = "SELECT num_reservation, date_debut, date_fin, facture_reservation, date_reservation, statut, id_client, num_chambre FROM reservation WHERE num_reservation = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Reservation($row['num_reservation'], $row['date_debut'], $row['date_fin'], $row['facture_reservation'], $row['date_reservation'], $row['statut'], $row['id_client'], $row['num_chambre']);
    }

    public function createReservation(Reservation $reservation): bool
    {
        $sql = "INSERT INTO reservation (date_debut, date_fin, facture_reservation, date_reservation, statut, id_client, num_chambre) VALUES (:date_debut, :date_fin, :facture_reservation, :date_reservation, :statut, :id_client, :num_chambre)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':date_debut', $reservation->getDate_debut(), PDO::PARAM_STR);
        $stmt->bindValue(':date_fin', $reservation->getDate_fin(), PDO::PARAM_STR);
        $stmt->bindValue(':facture_reservation', $reservation->getFacture_reservation(), PDO::PARAM_STR);
        $stmt->bindValue(':date_reservation', $reservation->getDate_reservation(), PDO::PARAM_STR);
        $stmt->bindValue(':statut', $reservation->getStatut(), PDO::PARAM_STR);
        $stmt->bindValue(':id_client', $reservation->getId_client(), PDO::PARAM_INT);
        $stmt->bindValue(':num_chambre', $reservation->getNum_chambre(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteReservation(int $id): bool
    {
        $sql = "DELETE FROM reservation WHERE num_reservation = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
