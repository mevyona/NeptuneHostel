<?php
declare(strict_types = 1);
namespace MyApp\Entity;

class Product{
    private ?int $id = null;
    private string $name;
    private float $price;
    public function __construct(?int $id, string $name, float $price ){
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }
    public function getId():?int{
        return $this->id;
    }
    public function setId(?int $id):void{
        $this->id = $id;
    }
    public function getName():string{
        return $this->name;
    }
    public function setName(string $name):void{
        $this->price = $name;
    }
    public function getPrice():float{
        return $this->price;
    }
    public function setPrice(float $price):void{
        $this->price = $price;
    }
}