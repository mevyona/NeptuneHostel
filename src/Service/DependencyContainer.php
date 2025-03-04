<?php
namespace MyApp\Service;

use MyApp\Model\AdminModel;
use MyApp\Model\AnnulationModel;
use MyApp\Model\AvisModel;
use MyApp\Model\ChambreModel;
use MyApp\Model\ContactModel;
use MyApp\Model\FactureModel;
use MyApp\Model\NotificationModel;
use MyApp\Model\OptionModel;
use MyApp\Model\PaiementModel;
use MyApp\Model\ReservationModel;
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

            case 'AdminModel':
                $pdo = $this->get('PDO');
                return new AdminModel($pdo);

            case 'ChambreModel':
                $pdo = $this->get('PDO');
                return new ChambreModel($pdo);

            case 'ReservationModel':
                $pdo = $this->get('PDO');
                return new ReservationModel($pdo);

            case 'PaiementModel':
                $pdo = $this->get('PDO');
                return new PaiementModel($pdo);

            case 'OptionModel':
                $pdo = $this->get('PDO');
                return new OptionModel($pdo);

            case 'FactureModel':
                $pdo = $this->get('PDO');
                return new FactureModel($pdo);

            case 'AnnulationModel':
                $pdo = $this->get('PDO');
                return new AnnulationModel($pdo);

            case 'AvisModel':
                $pdo = $this->get('PDO');
                return new AvisModel($pdo);

            case 'ContactModel':
                $pdo = $this->get('PDO');
                return new ContactModel($pdo);

            case 'NotificationModel':
                $pdo = $this->get('PDO');
                return new NotificationModel($pdo);
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
        } catch (PDOException $e) {
            throw new \Exception("PDO connection error: " . $e->getMessage());
        }
    }

}
