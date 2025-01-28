<?php
declare(strict_types = 1);
namespace MyApp\Entity;

// Comment

class Administrateur{
    private ?int $id_admin_admin = null;
    private string $nom_admin;
    private string $prenom_admin;
    public function __construct(?int $id_admin, string $nom_admin, string $prenom_admin ){
        $this->id_admin = $id_admin;
        $this->nom_admin = $nom_admin;
        $this->prenom_admin = $prenom_admin;
    }
    public function getId():?int{
        return $this->id_admin;
    }
    public function setId(?int $id_admin):void{
        $this->id_admin = $id_admin;
    }
    public function getNom():string{
        return $this->nom_admin;
    }
    public function setNom(string $nom_admin):void{
        $this->nom_admin = $nom_admin;
    }
    public function getPrenom():string{
        return $this->prenom_admin;
    }
    public function setPrenom(string $prenom_admin):void{
        $this->prenom_admin = $prenom_admin;
    }
}