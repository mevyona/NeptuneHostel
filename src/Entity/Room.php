<?php

declare(strict_types=1);

namespace MyApp\Entity;

class Room
{
    private ?int $id;
    private string $name;
    private bool $is_available;
    private float $price;
    private int $capacity;
    private ?string $description;
    private ?Media $featured_image;
    private string $created_at;
    private string $updated_at;

    public function __construct(?int $id, string $name, bool $is_available, float $price, int $capacity, ?string $description, ?Media $featured_image, string $created_at, string $updated_at)
    {
        $this->id = $id;
        $this->name = $name;
        $this->is_available = $is_available;
        $this->price = $price;
        $this->capacity = $capacity;
        $this->description = $description;
        $this->featured_image = $featured_image;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function isAvailable(): bool { return $this->is_available; }
    public function setIsAvailable(bool $is_available): void { $this->is_available = $is_available; }
    public function getPrice(): float { return $this->price; }
    public function setPrice(float $price): void { $this->price = $price; }
    public function getCapacity(): int { return $this->capacity; }
    public function setCapacity(int $capacity): void { $this->capacity = $capacity; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function getFeaturedImage(): ?Media { return $this->featured_image; }
    public function setFeaturedImage(?Media $featured_image): void { $this->featured_image = $featured_image; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function setCreatedAt(string $created_at): void { $this->created_at = $created_at; }
    public function getUpdatedAt(): string { return $this->updated_at; }
    public function setUpdatedAt(string $updated_at): void { $this->updated_at = $updated_at; }
}
