<?php

declare(strict_types=1);

namespace MyApp\Entity;
use MyApp\Entity\User;
use MyApp\Entity\Room;


class Reservation
{
    private ?int $id;
    private User $user;
    private Room $room;
    private string $check_in;
    private string $check_out;
    private string $status;
    private float $total_price;
    private ?string $special_requests;
    private string $created_at;
    private string $updated_at;

    public function __construct(
        ?int $id,
        User $user,
        Room $room,
        string $check_in,
        string $check_out,
        string $status,
        float $total_price,
        ?string $special_requests,
        string $created_at,
        string $updated_at
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->room = $room;
        $this->check_in = $check_in;
        $this->check_out = $check_out;
        $this->status = $status;
        $this->total_price = $total_price;
        $this->special_requests = $special_requests;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUser(): User { return $this->user; }
    public function setUser(User $user): void { $this->user = $user; }

    public function getRoom(): Room { return $this->room; }
    public function setRoom(Room $room): void { $this->room = $room; }

    public function getCheckIn(): string { return $this->check_in; }
    public function setCheckIn(string $check_in): void { $this->check_in = $check_in; }

    public function getCheckOut(): string { return $this->check_out; }
    public function setCheckOut(string $check_out): void { $this->check_out = $check_out; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function getTotalPrice(): float { return $this->total_price; }
    public function setTotalPrice(float $total_price): void { $this->total_price = $total_price; }

    public function getSpecialRequests(): ?string { return $this->special_requests; }
    public function setSpecialRequests(?string $special_requests): void { $this->special_requests = $special_requests; }

    public function getCreatedAt(): string { return $this->created_at; }
    public function setCreatedAt(string $created_at): void { $this->created_at = $created_at; }

    public function getUpdatedAt(): string { return $this->updated_at; }
    public function setUpdatedAt(string $updated_at): void { $this->updated_at = $updated_at; }
}
