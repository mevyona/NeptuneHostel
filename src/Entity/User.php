<?php
declare(strict_types = 1);
namespace MyApp\Entity;

class User{
    private ?int $id = null;
    private string $lastname;
    private string $firstname;
    private string $birthdate;
    private string $street;
    private string $city;
    private string $postalcode;
    private string $phone;
    private string $email;
    public function __construct(?int $id, string $lastname, string $firstname, 
        string $birthdate, string $street, string $city, string $postalcode, 
        string $phone, string $email) {
        $this->id = $id;
        $this->lastname = $lastname;
        $this->firstname = $firstname;
        $this->birthdate = $birthdate;
        $this->street = $street;
        $this->city = $city;
        $this->postalcode = $postalcode;
        $this->phone = $phone;
        $this->email = $email;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void {
        $this->lastname = $lastname;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void {
        $this->firstname = $firstname;
    }

    public function getBirthdate(): string {
        return $this->birthdate;
    }

    public function setBirthdate(string $birthdate): void {
        $this->birthdate = $birthdate;
    }

    public function getStreet(): string {
        return $this->street;
    }

    public function setStreet(string $street): void {
        $this->street = $street;
    }

    public function getCity(): string {
        return $this->city;
    }

    public function setCity(string $city): void {
        $this->city = $city;
    }

    public function getPostalcode(): string {
        return $this->postalcode;
    }

    public function setPostalcode(string $postalcode): void {
        $this->postalcode = $postalcode;
    }

    public function getPhone(): string {
        return $this->phone;
    }

    public function setPhone(string $phone): void {
        $this->phone = $phone;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }
}