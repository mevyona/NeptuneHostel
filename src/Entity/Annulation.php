<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class Annulation {
    private ?int $id_annulation = null;
    private string $motif_annulation;
    private string $date_annulation;
    private int $num_reservation;

    public function __construct(?int $id_annulation, string $motif_annulation, string $date_annulation, int $num_reservation) {
        $this->id_annulation = $id_annulation;
        $this->motif_annulation = $motif_annulation;
        $this->date_annulation = $date_annulation;
        $this->num_reservation = $num_reservation;
    }

    public function getId_annulation(): ?int { return $this->id_annulation; }
    public function setId_annulation(?int $id_annulation): void { $this->id_annulation = $id_annulation; }

    public function getMotif_annulation(): string { return $this->motif_annulation; }
    public function setMotif_annulation(string $motif_annulation): void { $this->motif_annulation = $motif_annulation; }

    public function getDate_annulation(): string { return $this->date_annulation; }
    public function setDate_annulation(string $date_annulation): void { $this->date_annulation = $date_annulation; }

    public function getNum_reservation(): int { return $this->num_reservation; }
    public function setNum_reservation(int $num_reservation): void { $this->num_reservation = $num_reservation; }
}
