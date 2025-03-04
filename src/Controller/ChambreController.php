<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Chambre;
use MyApp\Model\ChambreModel;

class ChambreController
{
    private $twig;
    private $chambreModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->chambreModel = $dependencyContainer->get('ChambreModel');
    }

    public function chambres()
    {
        $chambres = $this->chambreModel->getAllChambres();
        echo $this->twig->render('chambre/chambres.html.twig', ['chambres' => $chambres]);
    }

    public function addChambre()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $disponibilite = filter_input(INPUT_POST, 'disponibilite', FILTER_SANITIZE_STRING);
            $id_photos = filter_input(INPUT_POST, 'id_photos', FILTER_SANITIZE_STRING);
            $prix = filter_input(INPUT_POST, 'prix', FILTER_SANITIZE_STRING);

            if ($disponibilite && $id_photos && $prix) {
                $chambre = new Chambre(null, $disponibilite, $id_photos, $prix);
                $success = $this->chambreModel->createChambre($chambre);
                if ($success) {
                    header('Location: index.php?page=chambres');
                }
            }
        }
        echo $this->twig->render('chambre/addChambre.html.twig', []);
    }

    public function updateChambre()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $num_chambre = filter_input(INPUT_POST, 'num_chambre', FILTER_SANITIZE_NUMBER_INT);
            $disponibilite = filter_input(INPUT_POST, 'disponibilite', FILTER_SANITIZE_STRING);
            $id_photos = filter_input(INPUT_POST, 'id_photos', FILTER_SANITIZE_STRING);
            $prix = filter_input(INPUT_POST, 'prix', FILTER_SANITIZE_STRING);

            if ($num_chambre && $disponibilite && $id_photos && $prix) {
                $chambre = new Chambre($num_chambre, $disponibilite, $id_photos, $prix);
                $success = $this->chambreModel->updateChambre($chambre);
                if ($success) {
                    header('Location: index.php?page=chambres');
                }
            }
        } else {
            $num_chambre = filter_input(INPUT_GET, 'num_chambre', FILTER_SANITIZE_NUMBER_INT);
            $chambre = $this->chambreModel->getOneChambre(intval($num_chambre));
            echo $this->twig->render('chambre/updateChambre.html.twig', ['chambre' => $chambre]);
        }
    }

    public function deleteChambre()
    {
        $num_chambre = filter_input(INPUT_GET, 'num_chambre', FILTER_SANITIZE_NUMBER_INT);
        $this->chambreModel->deleteChambre(intval($num_chambre));
        header('Location: index.php?page=chambres');
    }
}
