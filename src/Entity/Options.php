<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class Options {
    private ?int $id_option = null;
    private string $nom_option;
    private float $prix_supplementaire;

    public function __construct(?int $id_option, string $nom_option, float $prix_supplementaire) {
        $this->id_option = $id_option;
        $this->nom_option = $nom_option;
        $this->prix_supplementaire = $prix_supplementaire;
    }

    public function getId_option(): ?int { return $this->id_option; }
    public function setId_option(?int $id_option): void { $this->id_option = $id_option; }

    public function getNom_option(): string { return $this->nom_option; }
    public function setNom_option(string $nom_option): void { $this->nom_option = $nom_option; }

    public function getPrix_supplementaire(): float { return $this->prix_supplementaire; }
    public function setPrix_supplementaire(float $prix_supplementaire): void { $this->prix_supplementaire = $prix_supplementaire; }
}
