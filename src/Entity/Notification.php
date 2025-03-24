<?php

declare(strict_types=1);

namespace MyApp\Entity;
use MyApp\Entity\User;


class Notification
{
    private ?int $id;
    private User $user;
    private string $title;
    private string $message;
    private bool $is_read;
    private string $notification_type;
    private string $created_at;

    public function __construct(
        ?int $id,
        User $user,
        string $title,
        string $message,
        bool $is_read,
        string $notification_type,
        string $created_at
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->title = $title;
        $this->message = $message;
        $this->is_read = $is_read;
        $this->notification_type = $notification_type;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUser(): User { return $this->user; }
    public function setUser(User $user): void { $this->user = $user; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function getMessage(): string { return $this->message; }
    public function setMessage(string $message): void { $this->message = $message; }

    public function isRead(): bool { return $this->is_read; }
    public function setIsRead(bool $is_read): void { $this->is_read = $is_read; }

    public function getNotificationType(): string { return $this->notification_type; }
    public function setNotificationType(string $notification_type): void { $this->notification_type = $notification_type; }

    public function getCreatedAt(): string { return $this->created_at; }
    public function setCreatedAt(string $created_at): void { $this->created_at = $created_at; }
}