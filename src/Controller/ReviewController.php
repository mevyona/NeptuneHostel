<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Model\ReviewModel;
use MyApp\Model\UserModel;
use MyApp\Model\RoomModel;
use MyApp\Model\ReservationModel;
use MyApp\Entity\Review;
use MyApp\Entity\User;
use MyApp\Entity\Room;
use MyApp\Entity\Reservation;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class ReviewController
{
    private Environment $twig;
    private ReviewModel $reviewModel;
    private UserModel $userModel;
    private RoomModel $roomModel;
    private ReservationModel $reservationModel;

    public function __construct(Environment $twig, DependencyContainer $container)
    {
        $this->twig = $twig;
        $this->reviewModel = $container->get('ReviewModel');
        $this->userModel = $container->get('UserModel');
        $this->roomModel = $container->get('RoomModel');
        $this->reservationModel = $container->get('ReservationModel');
    }

    public function listReviews()
    {
        $reviews = $this->reviewModel->getAllReviews();
        echo $this->twig->render('reviewController/listReviews.html.twig', [
            'reviews' => $reviews
        ]);
    }

    public function addReview()
    {
        $users = $this->userModel->getAllUsers();
        $rooms = $this->roomModel->getAllRooms();
        $reservations = $this->reservationModel->getAllReservations();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
            $room_id = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
            $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_SANITIZE_NUMBER_INT);
            $rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

            $user = $this->userModel->getOneUser((int)$user_id);
            $room = $this->roomModel->getOneRoom((int)$room_id);
            $reservation = $this->reservationModel->getOneReservation((int)$reservation_id);

            if ($user && $room && $reservation && $rating) {
                $review = new Review(null, $user, $room, $reservation, $rating, $comment, '');
                $this->reviewModel->createReview($review);
                $_SESSION['message'] = 'Avis enregistré';
                header('Location: index.php?page=list-reviews');
                exit();
            } else {
                $_SESSION['message'] = 'Erreur lors de l\'ajout de l\'avis';
            }
        }

        echo $this->twig->render('reviewController/addReview.html.twig', [
            'users' => $users,
            'rooms' => $rooms,
            'reservations' => $reservations
        ]);
    }

    public function showReview()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $review = $this->reviewModel->getOneReview((int)$id);

        if (!$review) {
            $_SESSION['message'] = 'Avis introuvable';
            header('Location: index.php?page=list-reviews');
            exit();
        }

        echo $this->twig->render('reviewController/showReview.html.twig', [
            'review' => $review
        ]);
    }

    public function updateReview()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

            $review = $this->reviewModel->getOneReview((int)$id);

            if ($review) {
                $updated = new Review($id, $review->getUser(), $review->getRoom(), $review->getReservation(), $rating, $comment, $review->getCreatedAt());
                $this->reviewModel->updateReview($updated);
                $_SESSION['message'] = 'Avis mis à jour';
                header('Location: index.php?page=list-reviews');
                exit();
            } else {
                $_SESSION['message'] = 'Erreur sur l\'avis';
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $review = $this->reviewModel->getOneReview((int)$id);

            if (!$review) {
                $_SESSION['message'] = 'Avis introuvable';
                header('Location: index.php?page=list-reviews');
                exit();
            }

            echo $this->twig->render('reviewController/updateReview.html.twig', [
                'review' => $review
            ]);
        }
    }

    public function deleteReview()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->reviewModel->deleteReview((int)$id);
        $_SESSION['message'] = 'Avis supprimé';
        header('Location: index.php?page=list-reviews');
    }
}
