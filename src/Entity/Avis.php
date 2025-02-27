<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class Avis {
    private ?int $id_avis = null;
    private float $note;
    private string $commentaire;
    private string $date_avis;
    private int $id_client;
    private int $num_chambre;

    public function __construct(?int $id_avis, float $note, string $commentaire, string $date_avis, int $id_client, int $num_chambre) {
        $this->id_avis = $id_avis;
        $this->note = $note;
        $this->commentaire = $commentaire;
        $this->date_avis = $date_avis;
        $this->id_client = $id_client;
        $this->num_chambre = $num_chambre;
    }

    public function getId_avis(): ?int { return $this->id_avis; }
    public function setId_avis(?int $id_avis): void { $this->id_avis = $id_avis; }

    public function getNote(): float { return $this->note; }
    public function setNote(float $note): void { $this->note = $note; }

    public function getCommentaire(): string { return $this->commentaire; }
    public function setCommentaire(string $commentaire): void { $this->commentaire = $commentaire; }

    public function getDate_avis(): string { return $this->date_avis; }
    public function setDate_avis(string $date_avis): void { $this->date_avis = $date_avis; }

    public function getId_client(): int { return $this->id_client; }
    public function setId_client(int $id_client): void { $this->id_client = $id_client; }

    public function getNum_chambre(): int { return $this->num_chambre; }
    public function setNum_chambre(int $num_chambre): void { $this->num_chambre = $num_chambre; }
}
