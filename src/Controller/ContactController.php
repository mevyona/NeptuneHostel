<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Contact;
use MyApp\Model\ContactModel;

class ContactController
{
    private $twig;
    private $contactModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->contactModel = $dependencyContainer->get('ContactModel');
    }

    public function messages()
    {
        $messages = $this->contactModel->getAllMessages();
        echo $this->twig->render('contact/messages.html.twig', ['messages' => $messages]);
    }

    public function addMessage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
            $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
            $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

            if ($nom && $prenom && $telephone && $message) {
                $contact = new Contact(null, $nom, $prenom, $telephone, $message);
                $success = $this->contactModel->createMessage($contact);
                if ($success) {
                    header('Location: index.php?page=messages');
                }
            }
        }
        echo $this->twig->render('contact/addMessage.html.twig', []);
    }

    public function deleteMessage()
    {
        $id_message = filter_input(INPUT_GET, 'id_message', FILTER_SANITIZE_NUMBER_INT);
        $this->contactModel->deleteMessage(intval($id_message));
        header('Location: index.php?page=messages');
    }
}
?>
