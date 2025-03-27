<?php


namespace MyApp\Entity;

use MyApp\Entity\Room;

class RoomOption
{
    private ?int $id;
    private Room $room;
    private string $option_name;
    private ?string $option_value;
    private bool $is_highlighted;
    private float $additional_cost;
    private string $created_at;
    private string $updated_at;

    public function __construct(?int $id, Room $room, string $option_name, ?string $option_value, bool $is_highlighted, float $additional_cost, string $created_at, string $updated_at)
    {
        $this->id = $id;
        $this->room = $room;
        $this->option_name = $option_name;
        $this->option_value = $option_value;
        $this->is_highlighted = $is_highlighted;
        $this->additional_cost = $additional_cost;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int { return $this->id; }
    public function getRoom(): Room { return $this->room; }
    public function getOptionName(): string { return $this->option_name; }
    public function getOptionValue(): ?string { return $this->option_value; }
    public function isHighlighted(): bool { return $this->is_highlighted; }
    public function getAdditionalCost(): float { return $this->additional_cost; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function getUpdatedAt(): string { return $this->updated_at; }

    public function setRoom(Room $room): void { $this->room = $room; }
    public function setOptionName(string $option_name): void { $this->option_name = $option_name; }
    public function setOptionValue(?string $option_value): void { $this->option_value = $option_value; }
    public function setIsHighlighted(bool $highlighted): void { $this->is_highlighted = $highlighted; }
    public function setAdditionalCost(float $cost): void { $this->additional_cost = $cost; }
}
