<?php
namespace MyApp\Service;

use MyApp\Model\UserModel;
use MyApp\Model\RoomModel;
use MyApp\Model\ReservationModel;
use MyApp\Model\ReviewModel;
use MyApp\Model\PaymentModel;
use MyApp\Model\InvoiceModel;
use MyApp\Model\MediaModel;
use MyApp\Model\CancellationModel;
use MyApp\Model\NotificationModel;
use MyApp\Model\RoomOptionModel;
use MyApp\Model\ContactMessageModel;

use PDO;

class DependencyContainer
{
    private $instances = [];

    public function __construct()
    {
    }

    public function get($key)
    {
        if (!isset($this->instances[$key])) {
            $this->instances[$key] = $this->createInstance($key);
        }

        return $this->instances[$key];
    }

    private function createInstance($key)
    {
        switch ($key) {
            case 'PDO':
                return $this->createPDOInstance();

            case 'UserModel':
                $pdo = $this->get('PDO');
                return new UserModel($pdo);
                
            case 'RoomModel':
                $pdo = $this->get('PDO');
                return new RoomModel($pdo);
                
            case 'ReservationModel':
                $pdo = $this->get('PDO');
                return new ReservationModel($pdo);
                
            case 'ReviewModel':
                $pdo = $this->get('PDO');
                return new ReviewModel($pdo);
                
            case 'PaymentModel':
                $pdo = $this->get('PDO');
                return new PaymentModel($pdo);
                
            case 'InvoiceModel':
                $pdo = $this->get('PDO');
                return new InvoiceModel($pdo);
                
            case 'MediaModel':
                $pdo = $this->get('PDO');
                return new MediaModel($pdo);
                
            case 'CancellationModel':
                $pdo = $this->get('PDO');
                return new CancellationModel($pdo);
                
            case 'NotificationModel':
                $pdo = $this->get('PDO');
                return new NotificationModel($pdo);
                
            case 'RoomOptionModel':
                $pdo = $this->get('PDO');
                $roomModel = $this->get('RoomModel');
                return new RoomOptionModel($pdo, $roomModel);
                
            case 'ContactMessageModel':
                $pdo = $this->get('PDO');
                return new ContactMessageModel($pdo);

            default:
                throw new \Exception("No service found for key: " . $key);
        }
    }

    private function createPDOInstance()
    {
        try {
            $pdo = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' .
                $_ENV['DB_NAME'] . ';charset=utf8', $_ENV['DB_USER'], $_ENV['DB_PASS']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (\PDOException $e) {
            throw new \Exception("PDO connection error: " . $e->getMessage());
        }
    }
}