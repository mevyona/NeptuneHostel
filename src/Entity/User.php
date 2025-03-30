<?php
declare (strict_types = 1);
namespace MyApp\Entity;

use InvalidArgumentException;

class User
{
    private ?int $id = null;
    private string $first_name;
    private string $last_name;
    private string $email;
    private ?string $phone;
    private string $password;
    private string $role;
    private ?string $created_at;
    private ?string $updated_at;
    
        private ?array $reservations = null;
    private ?array $reviews = null;
    private ?array $notifications = null;
    private ?array $cancellations = null;

    public function __construct(
        ?int $id, 
        string $first_name, 
        string $last_name, 
        string $email, 
        ?string $phone, 
        string $password, 
        string $role = 'client',
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone = $phone;
        $this->password = $password;
        $this->role = $role;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): void
    {
        $this->first_name = $first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): void
    {
        $this->last_name = $last_name;
    }
    
    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email invalide.");
        }
        $this->email = $email;
    }
    
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
    
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
    
    public function getRole(): string
    {
        return $this->role;
    }
    
    public function setRole(string $role): void
    {
        if (!in_array($role, ['client', 'admin', 'staff'])) {
            throw new InvalidArgumentException("Rôle invalide. Les rôles autorisés sont: client, admin, staff");
        }
        $this->role = $role;
    }
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }
    
    public function isClient(): bool
    {
        return $this->role === 'client';
    }
    
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }
    
    public function setCreatedAt(?string $created_at): void
    {
        $this->created_at = $created_at;
    }
    
    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }
    
    public function setUpdatedAt(?string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
    
        public function setReservations(array $reservations): void
    {
        $this->reservations = $reservations;
    }
    
        public function getReservations(): ?array
    {
        return $this->reservations;
    }
    
        public function setReviews(array $reviews): void
    {
        $this->reviews = $reviews;
    }
    
        public function getReviews(): ?array
    {
        return $this->reviews;
    }
    
        public function setNotifications(array $notifications): void
    {
        $this->notifications = $notifications;
    }
    
        public function getNotifications(): ?array
    {
        return $this->notifications;
    }
    
        public function setCancellations(array $cancellations): void
    {
        $this->cancellations = $cancellations;
    }
    
        public function getCancellations(): ?array
    {
        return $this->cancellations;
    }
    
        public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}