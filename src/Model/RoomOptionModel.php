<?php
namespace MyApp\Model;

use MyApp\Entity\RoomOption;
use MyApp\Entity\Room;
use PDO;

class RoomOptionModel
{
    private PDO $db;
    private RoomModel $roomModel;

    public function __construct(PDO $db, RoomModel $roomModel)
    {
        $this->db = $db;
        $this->roomModel = $roomModel;
    }

    public function getAllOptions(): array
    {
        $stmt = $this->db->query("SELECT * FROM RoomOption");
        $options = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $room = $this->roomModel->getOneRoom((int)$row['room_id']);
            $options[] = new RoomOption($row['id'], $room, $row['option_name'], $row['option_value'], (bool)$row['is_highlighted'], (float)$row['additional_cost'], $row['created_at'], $row['updated_at']);
        }
        return $options;
    }

    public function getOneOption(int $id): ?RoomOption
    {
        $stmt = $this->db->prepare("SELECT * FROM RoomOption WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        $room = $this->roomModel->getOneRoom((int)$row['room_id']);
        return new RoomOption($row['id'], $room, $row['option_name'], $row['option_value'], (bool)$row['is_highlighted'], (float)$row['additional_cost'], $row['created_at'], $row['updated_at']);
    }

    public function createOption(RoomOption $option): bool
    {
        $stmt = $this->db->prepare("INSERT INTO RoomOption (room_id, option_name, option_value, is_highlighted, additional_cost) VALUES (:room_id, :option_name, :option_value, :is_highlighted, :additional_cost)");
        $stmt->bindValue(':room_id', $option->getRoom()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':option_name', $option->getOptionName());
        $stmt->bindValue(':option_value', $option->getOptionValue());
        $stmt->bindValue(':is_highlighted', $option->isHighlighted(), PDO::PARAM_BOOL);
        $stmt->bindValue(':additional_cost', $option->getAdditionalCost());
        return $stmt->execute();
    }

    public function updateOption(RoomOption $option): bool
    {
        $stmt = $this->db->prepare("UPDATE RoomOption SET option_name = :option_name, option_value = :option_value, is_highlighted = :is_highlighted, additional_cost = :additional_cost WHERE id = :id");
        $stmt->bindValue(':option_name', $option->getOptionName());
        $stmt->bindValue(':option_value', $option->getOptionValue());
        $stmt->bindValue(':is_highlighted', $option->isHighlighted(), PDO::PARAM_BOOL);
        $stmt->bindValue(':additional_cost', $option->getAdditionalCost());
        $stmt->bindValue(':id', $option->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteOption(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM RoomOption WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
