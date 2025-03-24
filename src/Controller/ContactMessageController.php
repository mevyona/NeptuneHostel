<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Model\ContactMessageModel;
use MyApp\Entity\ContactMessage;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class ContactMessageController
{
    private Environment $twig;
    private ContactMessageModel $contactMessageModel;

    public function __construct(Environment $twig, DependencyContainer $container)
    {
        $this->twig = $twig;
        $this->contactMessageModel = $container->get('ContactMessageModel');
    }

    public function listMessages()
    {
        $messages = $this->contactMessageModel->getAllContactMessages();
        echo $this->twig->render('contactMessageController/listMessages.html.twig', [
            'messages' => $messages
        ]);
    }

    public function showMessage()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $message = $this->contactMessageModel->getOneContactMessage((int)$id);

        if (!$message) {
            $_SESSION['message'] = 'Message introuvable';
            header('Location: index.php?page=list-messages');
            exit();
        }

        echo $this->twig->render('contactMessageController/showMessage.html.twig', [
            'message' => $message
        ]);
    }

    public function updateMessage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

            $message = $this->contactMessageModel->getOneContactMessage((int)$id);
            if ($message) {
                $updated = new ContactMessage(
                    $id,
                    $message->getFirstName(),
                    $message->getLastName(),
                    $message->getEmail(),
                    $message->getPhone(),
                    $message->getMessage(),
                    $status,
                    $message->getCreatedAt()
                );
                $this->contactMessageModel->updateContactMessage($updated);
                $_SESSION['message'] = 'Statut mis à jour';
                header('Location: index.php?page=list-messages');
                exit();
            } else {
                $_SESSION['message'] = 'Message introuvable';
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $message = $this->contactMessageModel->getOneContactMessage((int)$id);

            if (!$message) {
                $_SESSION['message'] = 'Message introuvable';
                header('Location: index.php?page=list-messages');
                exit();
            }

            echo $this->twig->render('contactMessageController/updateMessage.html.twig', [
                'message' => $message
            ]);
        }
    }

    public function deleteMessage()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->contactMessageModel->deleteContactMessage((int)$id);
        $_SESSION['message'] = 'Message supprimé';
        header('Location: index.php?page=list-messages');
    }
}