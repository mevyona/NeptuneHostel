<?php

declare(strict_types=1);

namespace MyApp\Entity;

class Notification
{
    private ?int $id;
    private int $userId;
    private string $title;
    private string $message;
    private bool $isRead;
    private \DateTime $createdAt;
    
    public function __construct(
        ?int $id,
        int $userId,
        string $title,
        string $message,
        bool $isRead = false,
        ?\DateTime $createdAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->message = $message;
        $this->isRead = $isRead;
        $this->createdAt = $createdAt ?? new \DateTime();
    }
    
    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    public function getTitle(): string
    {
        return $this->title;
    }
    
    public function getMessage(): string
    {
        return $this->message;
    }
    
    public function isRead(): bool
    {
        return $this->isRead;
    }
    
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    // Setters
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
    
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }
    
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
    
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }
    
    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;
        return $this;
    }
    
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}