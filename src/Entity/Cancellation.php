<?php

declare(strict_types=1);

namespace MyApp\Entity;
use MyApp\Entity\User;
use MyApp\Entity\Reservation;


class Cancellation
{
    private ?int $id;
    private Reservation $reservation;
    private ?string $reason;
    private ?float $refund_amount;
    private string $cancellation_date;
    private User $cancelled_by;

    public function __construct(
        ?int $id,
        Reservation $reservation,
        ?string $reason,
        ?float $refund_amount,
        string $cancellation_date,
        User $cancelled_by
    ) {
        $this->id = $id;
        $this->reservation = $reservation;
        $this->reason = $reason;
        $this->refund_amount = $refund_amount;
        $this->cancellation_date = $cancellation_date;
        $this->cancelled_by = $cancelled_by;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }
    public function getReservation(): Reservation { return $this->reservation; }
    public function setReservation(Reservation $reservation): void { $this->reservation = $reservation; }
    public function getReason(): ?string { return $this->reason; }
    public function setReason(?string $reason): void { $this->reason = $reason; }
    public function getRefundAmount(): ?float { return $this->refund_amount; }
    public function setRefundAmount(?float $refund_amount): void { $this->refund_amount = $refund_amount; }
    public function getCancellationDate(): string { return $this->cancellation_date; }
    public function setCancellationDate(string $cancellation_date): void { $this->cancellation_date = $cancellation_date; }
    public function getCancelledBy(): User { return $this->cancelled_by; }
    public function setCancelledBy(User $cancelled_by): void { $this->cancelled_by = $cancelled_by; }
}