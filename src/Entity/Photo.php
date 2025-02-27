<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class Photo {
    private ?int $id_photos = null;
    private string $nom_img;
    private string $taille_img;
    private string $chemin_fichier;

    public function __construct(?int $id_photos, string $nom_img, string $taille_img, string $chemin_fichier) {
        $this->id_photos = $id_photos;
        $this->nom_img = $nom_img;
        $this->taille_img = $taille_img;
        $this->chemin_fichier = $chemin_fichier;
    }

    public function getId_photos(): ?int { return $this->id_photos; }
    public function setId_photos(?int $id_photos): void { $this->id_photos = $id_photos; }

    public function getNom_img(): string { return $this->nom_img; }
    public function setNom_img(string $nom_img): void { $this->nom_img = $nom_img; }

    public function getTaille_img(): string { return $this->taille_img; }
    public function setTaille_img(string $taille_img): void { $this->taille_img = $taille_img; }

    public function getChemin_fichier(): string { return $this->chemin_fichier; }
    public function setChemin_fichier(string $chemin_fichier): void { $this->chemin_fichier = $chemin_fichier; }
}
