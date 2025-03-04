<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Annulation;
use MyApp\Model\AnnulationModel;

class AnnulationController
{
    private $twig;
    private $annulationModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->annulationModel = $dependencyContainer->get('AnnulationModel');
    }

    public function annulations()
    {
        $annulations = $this->annulationModel->getAllAnnulations();
        echo $this->twig->render('annulation/annulations.html.twig', ['annulations' => $annulations]);
    }

    public function addAnnulation()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $motif = filter_input(INPUT_POST, 'motif_annulation', FILTER_SANITIZE_STRING);
            $date = filter_input(INPUT_POST, 'date_annulation', FILTER_SANITIZE_STRING);
            $num_reservation = filter_input(INPUT_POST, 'num_reservation', FILTER_SANITIZE_NUMBER_INT);

            if ($motif && $date && $num_reservation) {
                $annulation = new Annulation(null, $motif, $date, $num_reservation);
                $success = $this->annulationModel->createAnnulation($annulation);
                if ($success) {
                    header('Location: index.php?page=annulations');
                }
            }
        }
        echo $this->twig->render('annulation/addAnnulation.html.twig', []);
    }

    public function deleteAnnulation()
    {
        $id_annulation = filter_input(INPUT_GET, 'id_annulation', FILTER_SANITIZE_NUMBER_INT);
        $this->annulationModel->deleteAnnulation(intval($id_annulation));
        header('Location: index.php?page=annulations');
    }
}
?>
