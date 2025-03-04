<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Paiement;
use MyApp\Model\PaiementModel;

class PaiementController
{
    private $twig;
    private $paiementModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->paiementModel = $dependencyContainer->get('PaiementModel');
    }

    public function paiements()
    {
        $paiements = $this->paiementModel->getAllPaiements();
        echo $this->twig->render('paiement/paiements.html.twig', ['paiements' => $paiements]);
    }

    public function addPaiement()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $numero_cb = filter_input(INPUT_POST, 'numero_cb', FILTER_SANITIZE_STRING);
            $date_expiration = filter_input(INPUT_POST, 'date_expiration', FILTER_SANITIZE_STRING);
            $ccv_cb = filter_input(INPUT_POST, 'ccv_cb', FILTER_SANITIZE_STRING);
            $montant = filter_input(INPUT_POST, 'montant', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $statut = filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_STRING);
            $id_facture = filter_input(INPUT_POST, 'id_facture', FILTER_SANITIZE_NUMBER_INT);

            if ($numero_cb && $date_expiration && $ccv_cb && $montant && $statut && $id_facture) {
                $paiement = new Paiement(null, $numero_cb, $date_expiration, $ccv_cb, $montant, $statut, $id_facture);
                $success = $this->paiementModel->createPaiement($paiement);
                if ($success) {
                    header('Location: index.php?page=paiements');
                }
            }
        }
        echo $this->twig->render('paiement/addPaiement.html.twig', []);
    }

    public function deletePaiement()
    {
        $id_paiement = filter_input(INPUT_GET, 'id_paiement', FILTER_SANITIZE_NUMBER_INT);
        $this->paiementModel->deletePaiement(intval($id_paiement));
        header('Location: index.php?page=paiements');
    }
}
?>
