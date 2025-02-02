<?php
declare (strict_types = 1);
namespace MyApp\Model;

use PDO;
use MyApp\Entity\Chambre;

class ChambreModel
{
    private PDO $db;
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    public function getAllChambres(): array
    {
        $sql = "SELECT * FROM Chambre";
        $stmt = $this->db->query($sql);
        $chambres = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $chambres[] = new Chambre($row['num_chambre'], $row['disponibilite'], $row['id_photos'], $row['prix']);
        }
        return $chambres;

    }
    public function getOneChambre(int $num_chambre): ?Chambre
    {
        $sql = "SELECT * from Chambre where num_chambre= :num_chambre";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":num_chambre", $num_chambre);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Chambre($row['num_chambre'], $row['disponibilite'], $row['id_photos'], $row['prix']);
    }
    public function updateChambre(Chambre $chambre): bool
    {
        $sql = "UPDATE Chambre SET disponibilite = :disponibilte, id_photos = :id_photos, prix= :prix WHERE num_chambre = :num_chambre";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':disponibilite', $chambre->getDisponibilite(), PDO::PARAM_STR);
        $stmt->bindValue(':id_photos', $chambre->getId_photos(), PDO::PARAM_STR);
        $stmt->bindValue(':num_chambre', $chambre->getNum_chambre(), PDO::PARAM_INT);
        $stmt->bindValue(':prix', $chambre->getPrix(), PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function createChambre(Chambre $chambre): bool
    {
        $sql = "INSERT INTO Chambre (disponibilite, id_photos,prix) VALUES (:disponibilite, :id_photos, :prix)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':disponibilite', $chambre->getDisponibilite(), PDO::PARAM_STR);
        $stmt->bindValue(':id_photos', $chambre->getId_photos(), PDO::PARAM_STR);
        $stmt->bindValue(':prix', $chambre->getPrix(), PDO::PARAM_STR);
        return $stmt->execute();
    }
    public function deleteChambre(int $num_chambre): bool
    {
        $sql = "DELETE FROM Chambre WHERE num_chambre = :,num_chambre";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':num_chambre', $num_chambre, PDO::PARAM_INT);
        return $stmt->execute();
    }

}