<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Model\ContactMessageModel;
use MyApp\Entity\ContactMessage;
use MyApp\Entity\Notification;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class ContactMessageController
{
    private Environment $twig;
    private $contactMessageModel;
    private $dependencyContainer;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->dependencyContainer = $dependencyContainer;
        try {
            $this->contactMessageModel = $dependencyContainer->get('ContactMessageModel');
        } catch (\Exception $e) {
            // Gérer l'erreur ou laisser l'initialisation pour contact()
        }
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

    /**
     * Traite le formulaire de contact du pied de page
     */
    public function contact()
    {
        try {
            if (!$this->contactMessageModel) {
                $this->contactMessageModel = $this->dependencyContainer->get('ContactMessageModel');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                session_start();

                // Récupérer et nettoyer les données du formulaire
                $firstName = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
                $lastName = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $phone = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_STRING);
                $messageContent = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

                // Vérifier que les champs obligatoires sont remplis
                if (!$firstName || !$lastName || !$email || !$messageContent) {
                    $_SESSION['contact_message'] = 'Tous les champs obligatoires doivent être remplis.';
                    $_SESSION['contact_success'] = false;

                    // Rediriger vers la page précédente ou l'accueil
                    $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
                    header('Location: ' . $referer);
                    exit;
                }

                // Créer un nouveau message de contact
                $contactMessage = new ContactMessage(
                    null,
                    $firstName,
                    $lastName,
                    $email,
                    $phone,
                    $messageContent,
                    'new',
                    new \DateTime()
                );

                // Enregistrer le message dans la base de données
                $success = $this->contactMessageModel->createContactMessage($contactMessage);

                if ($success) {
                    // Créer une notification pour tous les administrateurs
                    try {
                        $userModel = $this->dependencyContainer->get('UserModel');
                        $notificationModel = $this->dependencyContainer->get('NotificationModel');

                        // Récupérer tous les administrateurs
                        $admins = $userModel->getUsersByRole('admin');

                        // Créer des notifications pour chaque admin
                        foreach ($admins as $admin) {
                            $notification = new Notification(
                                null,
                                $admin->getId(),
                                'Nouveau message de contact',
                                "De: $firstName $lastName\nEmail: $email\nTéléphone: $phone\n\nMessage: $messageContent",
                                false,
                                new \DateTime()
                            );

                            $notificationModel->createNotification($notification);
                        }
                    } catch (\Exception $e) {
                        // Continuer même si la création de notification échoue
                        error_log('Erreur lors de la création des notifications: ' . $e->getMessage());
                    }

                    $_SESSION['contact_message'] = 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.';
                    $_SESSION['contact_success'] = true;
                } else {
                    $_SESSION['contact_message'] = 'Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer.';
                    $_SESSION['contact_success'] = false;
                }

                // Rediriger vers la page précédente ou l'accueil
                $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
                header('Location: ' . $referer);
                exit;
            }

            // Si ce n'est pas une requête POST, rediriger vers l'accueil
            header('Location: index.php');
            exit;
        } catch (\Exception $e) {
            // Gérer l'erreur et rediriger
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['contact_message'] = 'Une erreur est survenue lors du traitement de votre message: ' . $e->getMessage();
            $_SESSION['contact_success'] = false;

            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
            header('Location: ' . $referer);
            exit;
        }
    }
}