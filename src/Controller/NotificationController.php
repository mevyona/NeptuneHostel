<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Notification;
use MyApp\Model\NotificationModel;

class NotificationController
{
    private $twig;
    private $notificationModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->notificationModel = $dependencyContainer->get('NotificationModel');
    }

    public function notifications()
    {
        $notifications = $this->notificationModel->getAllNotifications();
        echo $this->twig->render('notification/notifications.html.twig', ['notifications' => $notifications]);
    }

    public function addNotification()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
            $date_envoi = filter_input(INPUT_POST, 'date_envoi', FILTER_SANITIZE_STRING);
            $statut = filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_STRING);
            $id_client = filter_input(INPUT_POST, 'id_client', FILTER_SANITIZE_NUMBER_INT);

            if ($message && $date_envoi && $statut && $id_client) {
                $notification = new Notification(null, $message, $date_envoi, $statut, $id_client);
                $success = $this->notificationModel->createNotification($notification);
                if ($success) {
                    header('Location: index.php?page=notifications');
                }
            }
        }
        echo $this->twig->render('notification/addNotification.html.twig', []);
    }

    public function deleteNotification()
    {
        $id_notification = filter_input(INPUT_GET, 'id_notification', FILTER_SANITIZE_NUMBER_INT);
        $this->notificationModel->deleteNotification(intval($id_notification));
        header('Location: index.php?page=notifications');
    }
}
?>
