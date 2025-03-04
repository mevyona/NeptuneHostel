<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Facture;
use MyApp\Model\FactureModel;

class FactureController
{
    private $twig;
    private $factureModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->factureModel = $dependencyContainer->get('FactureModel');
    }

    public function factures()
    {
        $factures = $this->factureModel->getAllFactures();
        echo $this->twig->render('facture/factures.html.twig', ['factures' => $factures]);
    }

    public function addFacture()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $numero_cb = filter_input(INPUT_POST, 'numero_cb', FILTER_SANITIZE_STRING);
            $date_facture = filter_input(INPUT_POST, 'date_facture', FILTER_SANITIZE_STRING);
            $chemin_pdf = filter_input(INPUT_POST, 'chemin_pdf', FILTER_SANITIZE_STRING);
            $montant_total = filter_input(INPUT_POST, 'montant_total', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            if ($numero_cb && $date_facture && $chemin_pdf && $montant_total) {
                $facture = new Facture(null, $numero_cb, $date_facture, $chemin_pdf, $montant_total);
                $success = $this->factureModel->createFacture($facture);
                if ($success) {
                    header('Location: index.php?page=factures');
                }
            }
        }
        echo $this->twig->render('facture/addFacture.html.twig', []);
    }

    public function deleteFacture()
    {
        $id_facture = filter_input(INPUT_GET, 'id_facture', FILTER_SANITIZE_NUMBER_INT);
        $this->factureModel->deleteFacture(intval($id_facture));
        header('Location: index.php?page=factures');
    }
}
?>
