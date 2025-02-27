<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class Reservation {
    private ?int $num_reservation = null;
    private string $date_debut;
    private string $date_fin;
    private string $facture_reservation;
    private string $date_reservation;
    private string $statut;
    private int $id_client;
    private int $num_chambre;

    public function __construct(?int $num_reservation, string $date_debut, string $date_fin, string $facture_reservation, string $date_reservation, string $statut, int $id_client, int $num_chambre) {
        $this->num_reservation = $num_reservation;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->facture_reservation = $facture_reservation;
        $this->date_reservation = $date_reservation;
        $this->statut = $statut;
        $this->id_client = $id_client;
        $this->num_chambre = $num_chambre;
    }

    public function getNum_reservation(): ?int { return $this->num_reservation; }
    public function setNum_reservation(?int $num_reservation): void { $this->num_reservation = $num_reservation; }

    public function getDate_debut(): string { return $this->date_debut; }
    public function setDate_debut(string $date_debut): void { $this->date_debut = $date_debut; }

    public function getDate_fin(): string { return $this->date_fin; }
    public function setDate_fin(string $date_fin): void { $this->date_fin = $date_fin; }

    public function getFacture_reservation(): string { return $this->facture_reservation; }
    public function setFacture_reservation(string $facture_reservation): void { $this->facture_reservation = $facture_reservation; }

    public function getDate_reservation(): string { return $this->date_reservation; }
    public function setDate_reservation(string $date_reservation): void { $this->date_reservation = $date_reservation; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): void { $this->statut = $statut; }

    public function getId_client(): int { return $this->id_client; }
    public function setId_client(int $id_client): void { $this->id_client = $id_client; }

    public function getNum_chambre(): int { return $this->num_chambre; }
    public function setNum_chambre(int $num_chambre): void { $this->num_chambre = $num_chambre; }
}
