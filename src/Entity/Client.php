<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class Client {
    private ?int $id_client = null;
    private string $nom_client;
    private string $prenom_client;
    private string $numero_telephone;
    private string $email_client;
    private string $numero_chambre;
    private string $historique_reservation;
    private string $mot_de_passe;
    private string $adresse;

    public function __construct(?int $id_client, string $nom_client, string $prenom_client, string $numero_telephone, string $email_client, string $numero_chambre, string $historique_reservation, string $mot_de_passe, string $adresse) {
        $this->id_client = $id_client;
        $this->nom_client = $nom_client;
        $this->prenom_client = $prenom_client;
        $this->numero_telephone = $numero_telephone;
        $this->email_client = $email_client;
        $this->numero_chambre = $numero_chambre;
        $this->historique_reservation = $historique_reservation;
        $this->mot_de_passe = $mot_de_passe;
        $this->adresse = $adresse;
    }

    public function getId_client(): ?int { return $this->id_client; }
    public function setId_client(?int $id_client): void { $this->id_client = $id_client; }

    public function getNom_client(): string { return $this->nom_client; }
    public function setNom_client(string $nom_client): void { $this->nom_client = $nom_client; }

    public function getPrenom_client(): string { return $this->prenom_client; }
    public function setPrenom_client(string $prenom_client): void { $this->prenom_client = $prenom_client; }

    public function getNumero_telephone(): string { return $this->numero_telephone; }
    public function setNumero_telephone(string $numero_telephone): void { $this->numero_telephone = $numero_telephone; }

    public function getEmail_client(): string { return $this->email_client; }
    public function setEmail_client(string $email_client): void { $this->email_client = $email_client; }

    public function getNumero_chambre(): string { return $this->numero_chambre; }
    public function setNumero_chambre(string $numero_chambre): void { $this->numero_chambre = $numero_chambre; }

    public function getHistorique_reservation(): string { return $this->historique_reservation; }
    public function setHistorique_reservation(string $historique_reservation): void { $this->historique_reservation = $historique_reservation; }

    public function getMot_de_passe(): string { return $this->mot_de_passe; }
    public function setMot_de_passe(string $mot_de_passe): void { $this->mot_de_passe = $mot_de_passe; }

    public function getAdresse(): string { return $this->adresse; }
    public function setAdresse(string $adresse): void { $this->adresse = $adresse; }
}
