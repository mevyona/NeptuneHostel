<?php
declare (strict_types = 1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;

use MyApp\Entity\Admin;
use MyApp\Model\AdminModel;
use MyApp\Entity\Chambre;
use MyApp\Model\ChambreModel;


class DefaultController
{
    private $twig;
    private $adminModel;
    private $chambreModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->adminModel = $dependencyContainer->get('AdminModel');
        $this->chambreModel = $dependencyContainer->get('ChambreModel');
    }

    public function home()
    {
        echo $this->twig->render('defaultController/home.html.twig', []);
    }

    public function contact()
    {
        echo $this->twig->render('defaultController/contact.html.twig', []);
    }

    public function legals()
    {
        echo $this->twig->render('defaultController/legals.html.twig', []);
    }

    public function administrateurs()
    {
        $administrateurs = $this->adminModel->getAllAdmins();
        echo $this->twig->render('defaultController/administrateurs.html.twig', ['administrateurs' => $administrateurs]);
    }

    public function login()
    {
        echo $this->twig->render('defaultController/login.html.twig');
    }

    public function register()
    {
        echo $this->twig->render('defaultController/register.html.twig');
    }

    public function paiement()
    {
        echo $this->twig->render('defaultController/paiement.html.twig');
    }

    public function paiementfini()
    {
        echo $this->twig->render('defaultController/paiementfini.html.twig');
    }

    public function reservation()
    {
        echo $this->twig->render('defaultController/reservation.html.twig');
    }

    public function error404()
    {
        echo $this->twig->render('defaultController/error404.html.twig', []);
    }

    public function error500()
    {
        echo $this->twig->render('defaultController/error500.html.twig', []);
    }
    public function chambres()
    {
        $chambres = $this->chambreModel->getAllChambres();
        echo $this->twig->render('defaultController/chambres.html.twig', ['chambres' => $chambres]);
    }


    public function updateChambre()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $num_commande = filter_input(INPUT_POST, 'num_commande', FILTER_SANITIZE_NUMBER_INT);
            $disponibilite = filter_input(INPUT_POST, 'disponibilite', FILTER_SANITIZE_STRING);
            $id_photos = filter_input(INPUT_POST, 'id_photos', FILTER_SANITIZE_STRING);
            $prix = filter_input(INPUT_POST, 'prix', FILTER_SANITIZE_STRING);
        
            if (!empty($_POST['disponibilite'])) {
                $chambre = new Chambre(intVal($num_commande), $disponibilite, $id_photos, $prix);
                $success = $this->commandeModel->updateCommande($commande);
                if ($success) {
                    header('Location: index.php?page=chambres');
                }
            }
        } else {
            $num_chambre = filter_input(INPUT_GET, 'num_chambre', FILTER_SANITIZE_NUMBER_INT);
        }
        $chambre= $this->chambreModell->getOneChambre(intVal($num_chambre));
        echo $this->twig->render('defaultController/updateChambre.html.twig', ['chambre' => $chambre]);
    }

    public function addChambre()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $disponibilite = filter_input(INPUT_POST, 'disponibilite', FILTER_SANITIZE_STRING);
            $id_photos = filter_input(INPUT_POST, 'id_photos', FILTER_SANITIZE_STRING);
            $prix= filter_input(INPUT_POST, 'prix', FILTER_SANITIZE_STRING);
           
            
            if (!empty($_POST['disponibilite'])) {
                if (!empty($_POST['id_photos'])) 
                if (!empty($_POST['prix']))
                {
                    $chambre = new Chambre(null, $disponibilite, $disponibilite ,$prix );
                    $success = $this->chambreModel->createChambre($chambre);
                    if ($success) {
                        header('Location: index.php?page=chambres');
                    }
                }
            }

        }
        echo $this->twig->render('defaultController/addChambre.html.twig', []);
    }

    public function deleteChambre(){
        $num_chambre = filter_input(INPUT_GET, 'num_chambre', FILTER_SANITIZE_NUMBER_INT);
        $this->chambreModel->deleteChambre(intVal($num_chambre));
        header('Location: index.php?page=chambres');
    }
}