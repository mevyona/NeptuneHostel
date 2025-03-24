<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Model\CancellationModel;
use MyApp\Model\ReservationModel;
use MyApp\Model\UserModel;
use MyApp\Entity\Cancellation;
use MyApp\Entity\Reservation;
use MyApp\Entity\User;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class CancellationController
{
    private Environment $twig;
    private CancellationModel $cancellationModel;
    private ReservationModel $reservationModel;
    private UserModel $userModel;

    public function __construct(Environment $twig, DependencyContainer $container)
    {
        $this->twig = $twig;
        $this->cancellationModel = $container->get('CancellationModel');
        $this->reservationModel = $container->get('ReservationModel');
        $this->userModel = $container->get('UserModel');
    }

    public function listCancellations()
    {
        $cancellations = $this->cancellationModel->getAllCancellations();
        echo $this->twig->render('cancellationController/listCancellations.html.twig', [
            'cancellations' => $cancellations
        ]);
    }

    public function addCancellation()
    {
        $reservations = $this->reservationModel->getAllReservations();
        $users = $this->userModel->getAllUsers();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_SANITIZE_NUMBER_INT);
            $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING);
            $refund_amount = filter_input(INPUT_POST, 'refund_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $cancellation_date = $_POST['cancellation_date'] ?? '';
            $cancelled_by_id = filter_input(INPUT_POST, 'cancelled_by_id', FILTER_SANITIZE_NUMBER_INT);

            $reservation = $this->reservationModel->getOneReservation((int)$reservation_id);
            $cancelled_by = $this->userModel->getOneUser((int)$cancelled_by_id);

            if ($reservation && $cancelled_by) {
                $cancellation = new Cancellation(null, $reservation, $reason, $refund_amount, $cancellation_date, $cancelled_by);
                $this->cancellationModel->createCancellation($cancellation);
                $_SESSION['message'] = 'Annulation enregistrée';
                header('Location: index.php?page=list-cancellations');
                exit();
            } else {
                $_SESSION['message'] = 'Erreur lors de l\'ajout de l\'annulation';
            }
        }

        echo $this->twig->render('cancellationController/addCancellation.html.twig', [
            'reservations' => $reservations,
            'users' => $users
        ]);
    }

    public function showCancellation()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $cancellation = $this->cancellationModel->getOneCancellation((int)$id);

        if (!$cancellation) {
            $_SESSION['message'] = 'Annulation introuvable';
            header('Location: index.php?page=list-cancellations');
            exit();
        }

        echo $this->twig->render('cancellationController/showCancellation.html.twig', [
            'cancellation' => $cancellation
        ]);
    }

    public function updateCancellation()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING);
            $refund_amount = filter_input(INPUT_POST, 'refund_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            $cancellation = $this->cancellationModel->getOneCancellation((int)$id);

            if ($cancellation) {
                $updated = new Cancellation(
                    $id,
                    $cancellation->getReservation(),
                    $reason,
                    $refund_amount,
                    $cancellation->getCancellationDate(),
                    $cancellation->getCancelledBy()
                );
                $this->cancellationModel->updateCancellation($updated);
                $_SESSION['message'] = 'Annulation mise à jour';
                header('Location: index.php?page=list-cancellations');
                exit();
            } else {
                $_SESSION['message'] = 'Annulation introuvable';
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $cancellation = $this->cancellationModel->getOneCancellation((int)$id);

            if (!$cancellation) {
                $_SESSION['message'] = 'Annulation introuvable';
                header('Location: index.php?page=list-cancellations');
                exit();
            }

            echo $this->twig->render('cancellationController/updateCancellation.html.twig', [
                'cancellation' => $cancellation
            ]);
        }
    }

    public function deleteCancellation()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->cancellationModel->deleteCancellation((int)$id);
        $_SESSION['message'] = 'Annulation supprimée';
        header('Location: index.php?page=list-cancellations');
    }
}