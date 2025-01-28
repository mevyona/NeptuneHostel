<?php
declare (strict_types = 1);
namespace MyApp\Model;

use MyApp\Administrateur;
use PDO;

class AdministrateurModel
{
    private PDO $db;
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    public function getAllAdministrateurs(): array
    {
        $sql = "SELECT * FROM Administrateur";
        $stmt = $this->db->query($sql);
        $administrateurs = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $administrateurs[] = new Administrateur($row['id_admin'], $row['nom_admin'], $row['prenom_admin']);
        }
        return $administrateurs;

    }
    public function getOneAdministrateur(int $id_admin): ?Administrateur
    {
        $sql = "SELECT * from Administrateur where id_admin = :id_admin";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id_admin", $id_admin);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Administrateur($row['id_admin'], $row['nom_admin'], $row['prenom_admin']);
    }
    public function updateAdministrateur(Administrateur $administrateur): bool
    {
        $sql = "UPDATE Administrateur SET nom_admin = :nom_admin, prenom_admin = :prenom_admin WHERE id_admin = :id_admin";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom_admin', $administrateur->getNom(), PDO::PARAM_STR);
        $stmt->bindValue(':prenom_admin', $administrateur->getPrenom(), PDO::PARAM_STR);
        $stmt->bindValue(':id_admin', $administrateur->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function createAdministrateur(Administrateur $administrateur): bool
    {
        $sql = "INSERT INTO Administrateur (nom_admin, prenom_admin) VALUES (:nom_admin, :prenom_admin)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom_admin', $administrateur->getNom(), PDO::PARAM_STR);
        $stmt->bindValue(':prenom_admin', $administrateur->getPrenom(), PDO::PARAM_STR);
        return $stmt->execute();
    }
    public function deleteAdministrateur(int $id_admin): bool
    {
        $sql = "DELETE FROM Administrateur WHERE id_admin = :id_admin";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_admin', $id_admin, PDO::PARAM_INT);
        return $stmt->execute();
    }

}
