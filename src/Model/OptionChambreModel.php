<?php
declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\OptionChambre;
use PDO;

class OptionChambreModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllOptionChambres(): array
    {
        $sql = "SELECT num_chambre, id_option FROM option_chambre";
        $stmt = $this->db->query($sql);
        $optionChambres = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $optionChambres[] = new OptionChambre($row['num_chambre'], $row['id_option']);
        }
        return $optionChambres;
    }

    public function createOptionChambre(OptionChambre $optionChambre): bool
    {
        $sql = "INSERT INTO option_chambre (num_chambre, id_option) VALUES (:num_chambre, :id_option)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':num_chambre', $optionChambre->getNum_chambre(), PDO::PARAM_INT);
        $stmt->bindValue(':id_option', $optionChambre->getId_option(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteOptionChambre(int $num_chambre, int $id_option): bool
    {
        $sql = "DELETE FROM option_chambre WHERE num_chambre = :num_chambre AND id_option = :id_option";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':num_chambre', $num_chambre, PDO::PARAM_INT);
        $stmt->bindValue(':id_option', $id_option, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
