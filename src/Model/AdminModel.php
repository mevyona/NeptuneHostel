<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Admin;
use PDO;

class AdminModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllAdmins(): array
    {
        $sql = "SELECT id_admin, nom_admin, prenom_admin, email_admin, mot_de_passe, super_admin FROM admin ORDER BY nom_admin";
        $stmt = $this->db->query($sql);
        $admins = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $admins[] = new Admin($row['id_admin'], $row['nom_admin'], $row['prenom_admin'], $row['email_admin'], $row['mot_de_passe'], $row['super_admin']);
        }
        return $admins;
    }

    public function updateAdmin(Admin $admin): bool
    {
        $sql = "UPDATE admin SET nom_admin = :nom_admin, prenom_admin = :prenom_admin, email_admin = :email_admin, mot_de_passe = :mot_de_passe, super_admin = :super_admin WHERE id_admin = :id_admin";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom_admin', $admin->getNom_admin(), PDO::PARAM_STR);
        $stmt->bindValue(':prenom_admin', $admin->getPrenom_admin(), PDO::PARAM_STR);
        $stmt->bindValue(':email_admin', $admin->getEmail_admin(), PDO::PARAM_STR);
        $stmt->bindValue(':mot_de_passe', $admin->getMot_de_passe(), PDO::PARAM_STR);
        $stmt->bindValue(':super_admin', $admin->getSuper_admin(), PDO::PARAM_INT);
        $stmt->bindValue(':id_admin', $admin->getId_admin(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneAdmin(int $id): ?Admin
    {
        $sql = "SELECT id_admin, nom_admin, prenom_admin, email_admin, mot_de_passe, super_admin FROM admin WHERE id_admin = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Admin($row['id_admin'], $row['nom_admin'], $row['prenom_admin'], $row['email_admin'], $row['mot_de_passe'], $row['super_admin']);
    }

    public function createAdmin(Admin $admin): bool
    {
        $sql = "INSERT INTO admin (nom_admin, prenom_admin, email_admin, mot_de_passe, super_admin) VALUES (:nom_admin, :prenom_admin, :email_admin, :mot_de_passe, :super_admin)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom_admin', $admin->getNom_admin(), PDO::PARAM_STR);
        $stmt->bindValue(':prenom_admin', $admin->getPrenom_admin(), PDO::PARAM_STR);
        $stmt->bindValue(':email_admin', $admin->getEmail_admin(), PDO::PARAM_STR);
        $stmt->bindValue(':mot_de_passe', $admin->getMot_de_passe(), PDO::PARAM_STR);
        $stmt->bindValue(':super_admin', $admin->getSuper_admin(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteAdmin(int $id): bool
    {
        $sql = "DELETE FROM admin WHERE id_admin = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

