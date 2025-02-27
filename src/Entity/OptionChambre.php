<?php

declare (strict_types = 1);
namespace MyApp\Entity;

class OptionChambre {
    private int $num_chambre;
    private int $id_option;

    public function __construct(int $num_chambre, int $id_option) {
        $this->num_chambre = $num_chambre;
        $this->id_option = $id_option;
    }

    public function getNum_chambre(): int { return $this->num_chambre; }
    public function setNum_chambre(int $num_chambre): void { $this->num_chambre = $num_chambre; }

    public function getId_option(): int { return $this->id_option; }
    public function setId_option(int $id_option): void { $this->id_option = $id_option; }
}
