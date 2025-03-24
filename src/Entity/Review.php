<?php

declare(strict_types=1);

namespace MyApp\Entity;
use MyApp\Entity\Reservation;
use MyApp\Entity\User;
use MyApp\Entity\Room;


class Review
{
    private ?int $id;
    private User $user;
    private Room $room;
    private Reservation $reservation;
    private float $rating;
    private ?string $comment;
    private string $created_at;

    public function __construct(
        ?int $id,
        User $user,
        Room $room,
        Reservation $reservation,
        float $rating,
        ?string $comment,
        string $created_at
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->room = $room;
        $this->reservation = $reservation;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUser(): User { return $this->user; }
    public function setUser(User $user): void { $this->user = $user; }

    public function getRoom(): Room { return $this->room; }
    public function setRoom(Room $room): void { $this->room = $room; }

    public function getReservation(): Reservation { return $this->reservation; }
    public function setReservation(Reservation $reservation): void { $this->reservation = $reservation; }

    public function getRating(): float { return $this->rating; }
    public function setRating(float $rating): void { $this->rating = $rating; }

    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $comment): void { $this->comment = $comment; }

    public function getCreatedAt(): string { return $this->created_at; }
    public function setCreatedAt(string $created_at): void { $this->created_at = $created_at; }
}