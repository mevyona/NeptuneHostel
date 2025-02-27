<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Photo;
use PDO;

class PhotoModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllPhotos(): array
    {
        $sql = "SELECT id_photos, nom_img, taille_img, chemin_fichier FROM photo ORDER BY nom_img";
        $stmt = $this->db->query($sql);
        $photos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $photos[] = new Photo($row['id_photos'], $row['nom_img'], $row['taille_img'], $row['chemin_fichier']);
        }
        return $photos;
    }

    public function updatePhoto(Photo $photo): bool
    {
        $sql = "UPDATE photo SET nom_img = :nom_img, taille_img = :taille_img, chemin_fichier = :chemin_fichier WHERE id_photos = :id_photos";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom_img', $photo->getNom_img(), PDO::PARAM_STR);
        $stmt->bindValue(':taille_img', $photo->getTaille_img(), PDO::PARAM_STR);
        $stmt->bindValue(':chemin_fichier', $photo->getChemin_fichier(), PDO::PARAM_STR);
        $stmt->bindValue(':id_photos', $photo->getId_photos(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOnePhoto(int $id): ?Photo
    {
        $sql = "SELECT id_photos, nom_img, taille_img, chemin_fichier FROM photo WHERE id_photos = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Photo($row['id_photos'], $row['nom_img'], $row['taille_img'], $row['chemin_fichier']);
    }

    public function createPhoto(Photo $photo): bool
    {
        $sql = "INSERT INTO photo (nom_img, taille_img, chemin_fichier) VALUES (:nom_img, :taille_img, :chemin_fichier)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom_img', $photo->getNom_img(), PDO::PARAM_STR);
        $stmt->bindValue(':taille_img', $photo->getTaille_img(), PDO::PARAM_STR);
        $stmt->bindValue(':chemin_fichier', $photo->getChemin_fichier(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deletePhoto(int $id): bool
    {
        $sql = "DELETE FROM photo WHERE id_photos = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
