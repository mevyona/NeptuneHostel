<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Client;
use MyApp\Model\ClientModel;

class ClientController
{
    private $twig;
    private $clientModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->clientModel = $dependencyContainer->get('ClientModel');
    }

    public function clients()
    {
        $clients = $this->clientModel->getAllClients();
        echo $this->twig->render('client/clients.html.twig', ['clients' => $clients]);
    }

    public function addClient()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
            $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
            $mot_de_passe = filter_input(INPUT_POST, 'mot_de_passe', FILTER_SANITIZE_STRING);
            $adresse = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING);

            if ($nom && $prenom && $email && $telephone && $mot_de_passe) {
                $client = new Client(null, $nom, $prenom, $telephone, $email, '', '', $mot_de_passe, $adresse);
                $success = $this->clientModel->createClient($client);
                if ($success) {
                    header('Location: index.php?page=clients');
                }
            }
        }
        echo $this->twig->render('client/addClient.html.twig', []);
    }

    public function updateClient()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_client = filter_input(INPUT_POST, 'id_client', FILTER_SANITIZE_NUMBER_INT);
            $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
            $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
            $adresse = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING);

            if ($id_client && $nom && $prenom && $email && $telephone && $adresse) {
                $client = new Client($id_client, $nom, $prenom, $telephone, $email, '', '', '', $adresse);
                $success = $this->clientModel->updateClient($client);
                if ($success) {
                    header('Location: index.php?page=clients');
                }
            }
        } else {
            $id_client = filter_input(INPUT_GET, 'id_client', FILTER_SANITIZE_NUMBER_INT);
            $client = $this->clientModel->getOneClient(intval($id_client));
            echo $this->twig->render('client/updateClient.html.twig', ['client' => $client]);
        }
    }

    public function deleteClient()
    {
        $id_client = filter_input(INPUT_GET, 'id_client', FILTER_SANITIZE_NUMBER_INT);
        $this->clientModel->deleteClient(intval($id_client));
        header('Location: index.php?page=clients');
    }
}
?>
