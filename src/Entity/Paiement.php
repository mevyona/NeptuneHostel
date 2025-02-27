<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class Paiement {
    private ?int $id_paiement = null;
    private string $numero_cb;
    private string $date_expiration;
    private string $ccv_cb;
    private float $montant;
    private string $statut;
    private int $id_facture;
    private int $num_reservation;

    public function __construct(?int $id_paiement, string $numero_cb, string $date_expiration, string $ccv_cb, float $montant, string $statut, int $id_facture, int $num_reservation) {
        $this->id_paiement = $id_paiement;
        $this->numero_cb = $numero_cb;
        $this->date_expiration = $date_expiration;
        $this->ccv_cb = $ccv_cb;
        $this->montant = $montant;
        $this->statut = $statut;
        $this->id_facture = $id_facture;
        $this->num_reservation = $num_reservation;
    }

    public function getId_paiement(): ?int { return $this->id_paiement; }
    public function setId_paiement(?int $id_paiement): void { $this->id_paiement = $id_paiement; }

    public function getNumero_cb(): string { return $this->numero_cb; }
    public function setNumero_cb(string $numero_cb): void { $this->numero_cb = $numero_cb; }

    public function getDate_expiration(): string { return $this->date_expiration; }
    public function setDate_expiration(string $date_expiration): void { $this->date_expiration = $date_expiration; }

    public function getCcv_cb(): string { return $this->ccv_cb; }
    public function setCcv_cb(string $ccv_cb): void { $this->ccv_cb = $ccv_cb; }

    public function getMontant(): float { return $this->montant; }
    public function setMontant(float $montant): void { $this->montant = $montant; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): void { $this->statut = $statut; }

    public function getId_facture(): int { return $this->id_facture; }
    public function setId_facture(int $id_facture): void { $this->id_facture = $id_facture; }

    public function getNum_reservation(): int { return $this->num_reservation; }
    public function setNum_reservation(int $num_reservation): void { $this->num_reservation = $num_reservation; }
}
