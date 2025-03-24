<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Model\NotificationModel;
use MyApp\Model\UserModel;
use MyApp\Entity\Notification;
use MyApp\Entity\User;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class NotificationController
{
    private Environment $twig;
    private NotificationModel $notificationModel;
    private UserModel $userModel;

    public function __construct(Environment $twig, DependencyContainer $container)
    {
        $this->twig = $twig;
        $this->notificationModel = $container->get('NotificationModel');
        $this->userModel = $container->get('UserModel');
    }

    public function listNotifications()
    {
        $notifications = $this->notificationModel->getAllNotifications();
        echo $this->twig->render('notificationController/listNotifications.html.twig', [
            'notifications' => $notifications
        ]);
    }

    public function addNotification()
    {
        $users = $this->userModel->getAllUsers();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
            $notification_type = filter_input(INPUT_POST, 'notification_type', FILTER_SANITIZE_STRING);
            $is_read = isset($_POST['is_read']) ? true : false;

            $user = $this->userModel->getOneUser((int)$user_id);

            if ($user && $title && $message && $notification_type) {
                $notification = new Notification(null, $user, $title, $message, $is_read, $notification_type, '');
                $this->notificationModel->createNotification($notification);
                $_SESSION['message'] = 'Notification ajoutée';
                header('Location: index.php?page=list-notifications');
                exit();
            } else {
                $_SESSION['message'] = 'Erreur lors de la création de la notification';
            }
        }

        echo $this->twig->render('notificationController/addNotification.html.twig', [
            'users' => $users
        ]);
    }

    public function updateNotification()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
            $notification_type = filter_input(INPUT_POST, 'notification_type', FILTER_SANITIZE_STRING);
            $is_read = isset($_POST['is_read']) ? true : false;

            $notification = $this->notificationModel->getOneNotification((int)$id);

            if ($notification) {
                $updated = new Notification(
                    $notification->getId(),
                    $notification->getUser(),
                    $title,
                    $message,
                    $is_read,
                    $notification_type,
                    $notification->getCreatedAt()
                );
                $this->notificationModel->updateNotification($updated);
                $_SESSION['message'] = 'Notification mise à jour';
                header('Location: index.php?page=list-notifications');
                exit();
            } else {
                $_SESSION['message'] = 'Notification introuvable';
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $notification = $this->notificationModel->getOneNotification((int)$id);

            if (!$notification) {
                $_SESSION['message'] = 'Notification introuvable';
                header('Location: index.php?page=list-notifications');
                exit();
            }

            echo $this->twig->render('notificationController/updateNotification.html.twig', [
                'notification' => $notification
            ]);
        }
    }

    public function showNotification()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $notification = $this->notificationModel->getOneNotification((int)$id);

        if (!$notification) {
            $_SESSION['message'] = 'Notification introuvable';
            header('Location: index.php?page=list-notifications');
            exit();
        }

        echo $this->twig->render('notificationController/showNotification.html.twig', [
            'notification' => $notification
        ]);
    }

    public function deleteNotification()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->notificationModel->deleteNotification((int)$id);
        $_SESSION['message'] = 'Notification supprimée';
        header('Location: index.php?page=list-notifications');
    }
}