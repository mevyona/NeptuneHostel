<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Avis;
use MyApp\Model\AvisModel;

class AvisController
{
    private $twig;
    private $avisModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->avisModel = $dependencyContainer->get('AvisModel');
    }

    public function avis()
    {
        $avis = $this->avisModel->getAllAvis();
        echo $this->twig->render('avis/avis.html.twig', ['avis' => $avis]);
    }

    public function addAvis()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $note = filter_input(INPUT_POST, 'note', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $commentaire = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);
            $date_avis = filter_input(INPUT_POST, 'date_avis', FILTER_SANITIZE_STRING);
            $id_client = filter_input(INPUT_POST, 'id_client', FILTER_SANITIZE_NUMBER_INT);
            $num_chambre = filter_input(INPUT_POST, 'num_chambre', FILTER_SANITIZE_NUMBER_INT);

            if ($note && $commentaire && $date_avis && $id_client && $num_chambre) {
                $avis = new Avis(null, $note, $commentaire, $date_avis, $id_client, $num_chambre);
                $success = $this->avisModel->createAvis($avis);
                if ($success) {
                    header('Location: index.php?page=avis');
                }
            }
        }
        echo $this->twig->render('avis/addAvis.html.twig', []);
    }

    public function deleteAvis()
    {
        $id_avis = filter_input(INPUT_GET, 'id_avis', FILTER_SANITIZE_NUMBER_INT);
        $this->avisModel->deleteAvis(intval($id_avis));
        header('Location: index.php?page=avis');
    }
}
?>
