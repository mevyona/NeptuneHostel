<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class Contact {
    private ?int $id_message = null;
    private string $nom;
    private string $prenom;
    private string $telephone;
    private string $message;

    public function __construct(?int $id_message, string $nom, string $prenom, string $telephone, string $message) {
        $this->id_message = $id_message;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->message = $message;
    }

    public function getId_message(): ?int { return $this->id_message; }
    public function setId_message(?int $id_message): void { $this->id_message = $id_message; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }

    public function getTelephone(): string { return $this->telephone; }
    public function setTelephone(string $telephone): void { $this->telephone = $telephone; }

    public function getMessage(): string { return $this->message; }
    public function setMessage(string $message): void { $this->message = $message; }
}