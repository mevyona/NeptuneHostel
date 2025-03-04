<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Admin;
use MyApp\Model\AdminModel;

class AdminController
{
    private $twig;
    private $adminModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->adminModel = $dependencyContainer->get('AdminModel');
    }

    public function administrateurs()
    {
        $administrateurs = $this->adminModel->getAllAdministrateurs();
        echo $this->twig->render('admin/administrateurs.html.twig', ['administrateurs' => $administrateurs]);
    }

    public function addAdmin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = filter_input(INPUT_POST, 'nom_admin', FILTER_SANITIZE_STRING);
            $prenom = filter_input(INPUT_POST, 'prenom_admin', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email_admin', FILTER_SANITIZE_EMAIL);
            $motDePasse = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);
            $superAdmin = filter_input(INPUT_POST, 'super_admin', FILTER_SANITIZE_NUMBER_INT);

            if ($nom && $prenom && $email && $motDePasse) {
                $admin = new Admin(null, $nom, $prenom, $email, $motDePasse, $superAdmin);
                $success = $this->adminModel->createAdmin($admin);
                if ($success) {
                    header('Location: index.php?page=administrateurs');
                }
            }
        }
        echo $this->twig->render('admin/addAdmin.html.twig', []);
    }

    public function updateAdmin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_admin = filter_input(INPUT_POST, 'id_admin', FILTER_SANITIZE_NUMBER_INT);
            $nom = filter_input(INPUT_POST, 'nom_admin', FILTER_SANITIZE_STRING);
            $prenom = filter_input(INPUT_POST, 'prenom_admin', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email_admin', FILTER_SANITIZE_EMAIL);
            $superAdmin = filter_input(INPUT_POST, 'super_admin', FILTER_SANITIZE_NUMBER_INT);

            if ($id_admin && $nom && $prenom && $email) {
                $admin = new Admin($id_admin, $nom, $prenom, $email, null, $superAdmin);
                $success = $this->adminModel->updateAdmin($admin);
                if ($success) {
                    header('Location: index.php?page=administrateurs');
                }
            }
        } else {
            $id_admin = filter_input(INPUT_GET, 'id_admin', FILTER_SANITIZE_NUMBER_INT);
            $admin = $this->adminModel->getOneAdmin(intval($id_admin));
            echo $this->twig->render('admin/updateAdmin.html.twig', ['admin' => $admin]);
        }
    }

    public function deleteAdmin()
    {
        $id_admin = filter_input(INPUT_GET, 'id_admin', FILTER_SANITIZE_NUMBER_INT);
        $this->adminModel->deleteAdmin(intval($id_admin));
        header('Location: index.php?page=administrateurs');
    }
}
