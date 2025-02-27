<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class Chambre {
    private ?int $num_chambre = null;
    private bool $chambre_disponible;
    private float $prix_chambre;
    private float $capacite;
    private string $description;
    private string $nom_chambre;
    private int $id_photos;

    public function __construct(?int $num_chambre, bool $chambre_disponible, float $prix_chambre, float $capacite, string $description, string $nom_chambre, int $id_photos) {
        $this->num_chambre = $num_chambre;
        $this->chambre_disponible = $chambre_disponible;
        $this->prix_chambre = $prix_chambre;
        $this->capacite = $capacite;
        $this->description = $description;
        $this->nom_chambre = $nom_chambre;
        $this->id_photos = $id_photos;
    }

    public function getNum_chambre(): ?int { return $this->num_chambre; }
    public function setNum_chambre(?int $num_chambre): void { $this->num_chambre = $num_chambre; }

    public function getChambre_disponible(): bool { return $this->chambre_disponible; }
    public function setChambre_disponible(bool $chambre_disponible): void { $this->chambre_disponible = $chambre_disponible; }

    public function getPrix_chambre(): float { return $this->prix_chambre; }
    public function setPrix_chambre(float $prix_chambre): void { $this->prix_chambre = $prix_chambre; }

    public function getCapacite(): float { return $this->capacite; }
    public function setCapacite(float $capacite): void { $this->capacite = $capacite; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): void { $this->description = $description; }

    public function getNom_chambre(): string { return $this->nom_chambre; }
    public function setNom_chambre(string $nom_chambre): void { $this->nom_chambre = $nom_chambre; }

    public function getId_photos(): int { return $this->id_photos; }
    public function setId_photos(int $id_photos): void { $this->id_photos = $id_photos; }
}
