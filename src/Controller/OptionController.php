<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Option;
use MyApp\Model\OptionModel;

class OptionController
{
    private $twig;
    private $optionModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->optionModel = $dependencyContainer->get('OptionModel');
    }

    public function options()
    {
        $options = $this->optionModel->getAllOptions();
        echo $this->twig->render('option/options.html.twig', ['options' => $options]);
    }

    public function addOption()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom_option = filter_input(INPUT_POST, 'nom_option', FILTER_SANITIZE_STRING);
            $prix_supplementaire = filter_input(INPUT_POST, 'prix_supplementaire', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            if ($nom_option && $prix_supplementaire) {
                $option = new Option(null, $nom_option, $prix_supplementaire);
                $success = $this->optionModel->createOption($option);
                if ($success) {
                    header('Location: index.php?page=options');
                }
            }
        }
        echo $this->twig->render('option/addOption.html.twig', []);
    }

    public function deleteOption()
    {
        $id_option = filter_input(INPUT_GET, 'id_option', FILTER_SANITIZE_NUMBER_INT);
        $this->optionModel->deleteOption(intval($id_option));
        header('Location: index.php?page=options');
    }
}
?>
