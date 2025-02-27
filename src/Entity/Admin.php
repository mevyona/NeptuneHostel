<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class Admin {
    private ?int $id_admin = null;
    private string $nom_admin;
    private string $prenom_admin;
    private string $email_admin;
    private string $mot_de_passe;
    private int $super_admin;

    public function __construct(?int $id_admin, string $nom_admin, string $prenom_admin, string $email_admin, string $mot_de_passe, int $super_admin) {
        $this->id_admin = $id_admin;
        $this->nom_admin = $nom_admin;
        $this->prenom_admin = $prenom_admin;
        $this->email_admin = $email_admin;
        $this->mot_de_passe = $mot_de_passe;
        $this->super_admin = $super_admin;
    }

    public function getId_admin(): ?int { return $this->id_admin; }
    public function setId_admin(?int $id_admin): void { $this->id_admin = $id_admin; }

    public function getNom_admin(): string { return $this->nom_admin; }
    public function setNom_admin(string $nom_admin): void { $this->nom_admin = $nom_admin; }

    public function getPrenom_admin(): string { return $this->prenom_admin; }
    public function setPrenom_admin(string $prenom_admin): void { $this->prenom_admin = $prenom_admin; }

    public function getEmail_admin(): string { return $this->email_admin; }
    public function setEmail_admin(string $email_admin): void { $this->email_admin = $email_admin; }

    public function getMot_de_passe(): string { return $this->mot_de_passe; }
    public function setMot_de_passe(string $mot_de_passe): void { $this->mot_de_passe = $mot_de_passe; }

    public function getSuper_admin(): int { return $this->super_admin; }
    public function setSuper_admin(int $super_admin): void { $this->super_admin = $super_admin; }
}