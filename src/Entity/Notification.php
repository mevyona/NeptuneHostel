<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class Notification {
    private ?int $id_notification = null;
    private string $message;
    private string $date_envoi;
    private string $statut;
    private int $id_client;

    public function __construct(?int $id_notification, string $message, string $date_envoi, string $statut, int $id_client) {
        $this->id_notification = $id_notification;
        $this->message = $message;
        $this->date_envoi = $date_envoi;
        $this->statut = $statut;
        $this->id_client = $id_client;
    }

    public function getId_notification(): ?int { return $this->id_notification; }
    public function setId_notification(?int $id_notification): void { $this->id_notification = $id_notification; }

    public function getMessage(): string { return $this->message; }
    public function setMessage(string $message): void { $this->message = $message; }

    public function getDate_envoi(): string { return $this->date_envoi; }
    public function setDate_envoi(string $date_envoi): void { $this->date_envoi = $date_envoi; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): void { $this->statut = $statut; }

    public function getId_client(): int { return $this->id_client; }
    public function setId_client(int $id_client): void { $this->id_client = $id_client; }
}
