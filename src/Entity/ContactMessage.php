<?php

declare(strict_types=1);

namespace MyApp\Entity;

class ContactMessage
{
    private ?int $id;
    private string $first_name;
    private string $last_name;
    private string $email;
    private ?string $phone;
    private string $message;
    private string $status;
    private string $created_at;

    public function __construct(
        ?int $id,
        string $first_name,
        string $last_name,
        string $email,
        ?string $phone,
        string $message,
        string $status,
        string $created_at
    ) {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone = $phone;
        $this->message = $message;
        $this->status = $status;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }
    public function getFirstName(): string { return $this->first_name; }
    public function setFirstName(string $first_name): void { $this->first_name = $first_name; }
    public function getLastName(): string { return $this->last_name; }
    public function setLastName(string $last_name): void { $this->last_name = $last_name; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): void { $this->phone = $phone; }
    public function getMessage(): string { return $this->message; }
    public function setMessage(string $message): void { $this->message = $message; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function setCreatedAt(string $created_at): void { $this->created_at = $created_at; }
}