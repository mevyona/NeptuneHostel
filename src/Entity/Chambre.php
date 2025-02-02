<?php
declare(strict_types = 1);
namespace MyApp\Entity;

class Chambre
{
    private ?int $num_chambre = null;
    private string $disponibilite;
    private string $id_photos;
    private string $prix;
    public function __construct(?int $num_chambre, string $disponibilite, string $id_photos, string $prix ){
        $this->num_chambre = $num_chambre;
        $this->disponibilite = $disponibilite;
        $this->id_photos = $id_photos;
        $this->prix = $prix;
    }
    public function getNum_chambre():?int{
        return $this->num_chambre;
    }
    public function setNum_chambre(?int $num_chambre):void{
        $this->num_chambre = $num_chambre;
    }
    public function getDisponibilite():string{
        return $this->getDisponibilite;
    }
    public function setDisponibilite(string $disponibilite):void{
        $this->disponibilite = $disponibilite;
    }
    public function getId_photos():string{
        return $this->id_photos;
    }
    public function setId_photos(string $id_photos):void{
        $this->id_photos = $id_photos;
    }
    public function getPrix():string{
        return $this->prix;
    }
    public function setPrix(string $prix):void{
        $this->prix = $prix;
    }
}