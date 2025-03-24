<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Model\ReservationModel;
use MyApp\Model\UserModel;
use MyApp\Model\RoomModel;
use MyApp\Entity\Reservation;
use MyApp\Entity\User;
use MyApp\Entity\Room;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class ReservationController
{
    private Environment $twig;
    private ReservationModel $reservationModel;
    private UserModel $userModel;
    private RoomModel $roomModel;

    public function __construct(Environment $twig, DependencyContainer $container)
    {
        $this->twig = $twig;
        $this->reservationModel = $container->get('ReservationModel');
        $this->userModel = $container->get('UserModel');
        $this->roomModel = $container->get('RoomModel');
    }

    public function listReservations()
    {
        $reservations = $this->reservationModel->getAllReservations();
        echo $this->twig->render('reservationController/listReservations.html.twig', [
            'reservations' => $reservations
        ]);
    }

    public function addReservation()
    {
        $users = $this->userModel->getAllUsers();
        $rooms = $this->roomModel->getAllRooms();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
            $room_id = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
            $check_in = $_POST['check_in'] ?? '';
            $check_out = $_POST['check_out'] ?? '';
            $status = $_POST['status'] ?? 'pending';
            $total_price = filter_input(INPUT_POST, 'total_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $special_requests = filter_input(INPUT_POST, 'special_requests', FILTER_SANITIZE_STRING);

            if ($user_id && $room_id && $check_in && $check_out && $total_price) {
                $user = $this->userModel->getOneUser((int)$user_id);
                $room = $this->roomModel->getOneRoom((int)$room_id);
                if ($user && $room) {
                    $reservation = new Reservation(null, $user, $room, $check_in, $check_out, $status, $total_price, $special_requests, '', '');
                    $this->reservationModel->createReservation($reservation);
                    $_SESSION['message'] = 'Réservation ajoutée';
                    header('Location: index.php?page=list-reservations');
                    exit();
                }
            }

            $_SESSION['message'] = 'Erreur lors de l\'ajout de la réservation';
        }

        echo $this->twig->render('reservationController/addReservation.html.twig', [
            'users' => $users,
            'rooms' => $rooms
        ]);
    }

    public function deleteReservation()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($id) {
            $this->reservationModel->deleteReservation((int)$id);
        }
        header('Location: index.php?page=list-reservations');
    }
}