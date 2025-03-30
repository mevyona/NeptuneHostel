<?php

declare(strict_types=1);

namespace MyApp\Entity;

class ContactMessage
{
    private ?int $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private ?string $phone;
    private string $message;
    private string $status;
    private ?\DateTime $createdAt;
    
    public function __construct(
        ?int $id,
        string $firstName,
        string $lastName,
        string $email,
        ?string $phone,
        string $message,
        string $status = 'new',
        ?\DateTime $createdAt = null
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->message = $message;
        $this->status = $status;
        $this->createdAt = $createdAt ?? new \DateTime();
    }
    
    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    
    public function getLastName(): string
    {
        return $this->lastName;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    
    public function getMessage(): string
    {
        return $this->message;
    }
    
    public function getStatus(): string
    {
        return $this->status;
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
    
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }
    
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }
    
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }
    
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }
    
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
    
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}