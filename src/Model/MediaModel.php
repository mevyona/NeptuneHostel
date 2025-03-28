<?php

declare(strict_types=1);

namespace MyApp\Model;

use MyApp\Entity\Media;
use PDO;

class MediaModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllMedia(): array
    {
        $sql = "SELECT * FROM Media ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        $mediaList = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mediaList[] = new Media(
                $row['id'],
                $row['file_name'],
                $row['file_path'],
                $row['file_type'],
                $row['file_size'],
                $row['created_at']
            );
        }
        return $mediaList;
    }

    public function getOneMedia(int $id): ?Media
    {
        $sql = "SELECT * FROM Media WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Media(
            $row['id'],
            $row['file_name'],
            $row['file_path'],
            $row['file_type'],
            $row['file_size'],
            $row['created_at']
        );
    }

    public function createMedia(Media $media): bool
    {
        $sql = "INSERT INTO Media (file_name, file_path, file_type, file_size) 
                VALUES (:file_name, :file_path, :file_type, :file_size)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':file_name', $media->getFileName(), PDO::PARAM_STR);
        $stmt->bindValue(':file_path', $media->getFilePath(), PDO::PARAM_STR);
        $stmt->bindValue(':file_type', $media->getFileType(), PDO::PARAM_STR);
        $stmt->bindValue(':file_size', $media->getFileSize(), PDO::PARAM_INT);
        
        $result = $stmt->execute();
        
        if ($result) {
            $media->setId((int)$this->db->lastInsertId());
        }
        
        return $result;
    }

    public function updateMedia(Media $media): bool
    {
        $sql = "UPDATE Media SET file_name = :file_name, file_path = :file_path, file_type = :file_type, file_size = :file_size WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':file_name', $media->getFileName(), PDO::PARAM_STR);
        $stmt->bindValue(':file_path', $media->getFilePath(), PDO::PARAM_STR);
        $stmt->bindValue(':file_type', $media->getFileType(), PDO::PARAM_STR);
        $stmt->bindValue(':file_size', $media->getFileSize(), PDO::PARAM_INT);
        $stmt->bindValue(':id', $media->getId(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteMedia(int $id): bool
    {
        $sql = "DELETE FROM Media WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}