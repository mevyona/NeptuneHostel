<?php
declare(strict_types = 1);
namespace MyApp\Model;

use MyApp\Entity\User;
use PDO;

class UserModel{
    private PDO $db;
    public function __construct(PDO $db){
    $this->db = $db;
    }
    public function getAllUsers():array{
        $sql = "SELECT * FROM User";
        $stmt = $this->db->query($sql);
        $users=[];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $users[] = new User($row['id'],$row['lastname'],$row['firstname'],$row['birthdate'],$row['street'],$row['city'],$row['postalcode'],$row['phone'],$row['email']);
        }
        return $users;
    }
}