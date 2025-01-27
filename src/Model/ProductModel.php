<?php
declare (strict_types = 1);
namespace MyApp\Model;

use MyApp\Entity\Product;
use PDO;

class ProductModel
{
    private PDO $db;
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    public function getAllProducts(): array
    {
        $sql = "SELECT * FROM Product";
        $stmt = $this->db->query($sql);
        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product($row['id'], $row['name'], $row['price']);
        }
        return $products;
    }

    public function getOneProduct(int $id): ?Product
    {
        $sql = "SELECT * from Product where id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Product($row['id'], $row['name'], $row['price']);
    }

    public function createProduct(Product $product): bool 
    {
        $sql = "INSERT INTO Product (name, price) VALUES (:name, :price)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $product->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':price', $product->getPrice(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateProduct(Product $product): bool
    {
        $sql = "UPDATE Product SET name = :name, price = :price WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $product->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':price', $product->getPrice(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $product->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteProduct(int $id): bool 
    {
        $sql = "DELETE FROM Product WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
