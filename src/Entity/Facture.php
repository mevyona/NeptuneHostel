<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class Facture {
    private ?int $id_facture = null;
    private string $numero_cb;
    private string $date_facture;
    private string $chemin_pdf;
    private float $montant_total;

    public function __construct(?int $id_facture, string $numero_cb, string $date_facture, string $chemin_pdf, float $montant_total) {
        $this->id_facture = $id_facture;
        $this->numero_cb = $numero_cb;
        $this->date_facture = $date_facture;
        $this->chemin_pdf = $chemin_pdf;
        $this->montant_total = $montant_total;
    }

    public function getId_facture(): ?int { return $this->id_facture; }
    public function setId_facture(?int $id_facture): void { $this->id_facture = $id_facture; }

    public function getNumero_cb(): string { return $this->numero_cb; }
    public function setNumero_cb(string $numero_cb): void { $this->numero_cb = $numero_cb; }

    public function getDate_facture(): string { return $this->date_facture; }
    public function setDate_facture(string $date_facture): void { $this->date_facture = $date_facture; }

    public function getChemin_pdf(): string { return $this->chemin_pdf; }
    public function setChemin_pdf(string $chemin_pdf): void { $this->chemin_pdf = $chemin_pdf; }

    public function getMontant_total(): float { return $this->montant_total; }
    public function setMontant_total(float $montant_total): void { $this->montant_total = $montant_total; }
}