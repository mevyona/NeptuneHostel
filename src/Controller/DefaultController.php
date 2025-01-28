<?php
declare (strict_types = 1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;

use MyApp\Entity\Administrateurs;
use MyApp\Model\AdministrateurModel;


class DefaultController
{
    private $twig;
    private $administrateurModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->administrateurModel = $dependencyContainer->get('AdministrateurModel');
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
        $administrateurs = $this->administrateurModel->getAllAdministrateurs();
        echo $this->twig->render('defaultController/administrateurs.html.twig', ['administrateurs' => $administrateurs]);
    }

    public function error404()
    {
        echo $this->twig->render('defaultController/error404.html.twig', []);
    }

    public function error500()
    {
        echo $this->twig->render('defaultController/error500.html.twig', []);
    }
}
