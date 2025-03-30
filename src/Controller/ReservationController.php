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
use DateTime;

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

    /**
     * Initialise le processus de paiement après la sélection des dates sur la page showRoom
     * Cette méthode fait le pont entre la page de chambre et la page de paiement
     */
    public function initializePayment()
    {
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vous devez être connecté pour effectuer une réservation.';
            $_SESSION['success'] = false;
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: index.php?page=login');
            exit();
        }

        // Récupérer les données du formulaire
        $roomId = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
        $checkIn = filter_input(INPUT_POST, 'check_in', FILTER_SANITIZE_STRING);
        $checkOut = filter_input(INPUT_POST, 'check_out', FILTER_SANITIZE_STRING);
        $specialRequests = filter_input(INPUT_POST, 'special_requests', FILTER_SANITIZE_STRING);

        // Validation plus explicite pour débugger
        if (!$roomId) {
            $_SESSION['message'] = 'ID de chambre manquant.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=rooms');
            exit();
        }
        
        if (!$checkIn || !$checkOut) {
            $_SESSION['message'] = 'Veuillez sélectionner les dates de séjour.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=showRoom&id=' . $roomId);
            exit();
        }

        // Suite du code...

        // Assurez-vous que cette redirection est atteinte
        $_SESSION['message'] = 'Redirection vers la page de paiement...';
        $_SESSION['success'] = true;
        header('Location: index.php?page=payment');
        exit();
    }

    /**
     * Vérifie si une chambre est déjà réservée pour les dates spécifiées
     */
    private function isRoomBooked(int $roomId, string $checkIn, string $checkOut): bool
    {
        // Utiliser la méthode existante dans le modèle de réservation
        return $this->reservationModel->isRoomBooked($roomId, $checkIn, $checkOut);
    }

    /**
     * Méthode pour afficher la page de confirmation après un paiement réussi
     */
    public function showConfirmation()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vous devez être connecté pour voir vos réservations.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=login');
            exit();
        }

        // Récupérer l'ID de la réservation depuis l'URL
        $reservationId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$reservationId) {
            $_SESSION['message'] = 'Numéro de réservation invalide.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Récupérer les détails de la réservation
        $reservation = $this->reservationModel->getReservationById((int)$reservationId);
        
        if (!$reservation) {
            $_SESSION['message'] = 'Réservation introuvable.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Vérifier que la réservation appartient bien à l'utilisateur connecté
        // Sauf si l'utilisateur est un admin
        if ($reservation['user_id'] != $_SESSION['user_id'] && $_SESSION['user_role'] != 'admin') {
            $_SESSION['message'] = 'Vous n\'êtes pas autorisé à voir cette réservation.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Afficher la page de confirmation
        echo $this->twig->render('reservationController/confirmation.html.twig', [
            'reservation' => $reservation,
            'session' => $_SESSION ?? []
        ]);
    }

    /**
     * Méthode pour afficher les détails d'une réservation
     */
    public function showReservation()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vous devez être connecté pour voir vos réservations.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=login');
            exit();
        }

        // Récupérer l'ID de la réservation depuis l'URL
        $reservationId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$reservationId) {
            $_SESSION['message'] = 'Numéro de réservation invalide.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Récupérer les détails de la réservation
        $reservation = $this->reservationModel->getReservationById((int)$reservationId);
        
        if (!$reservation) {
            $_SESSION['message'] = 'Réservation introuvable.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Vérifier que la réservation appartient bien à l'utilisateur connecté
        // Sauf si l'utilisateur est un admin ou un membre du personnel
        if ($reservation['user_id'] != $_SESSION['user_id'] && 
            $_SESSION['user_role'] != 'admin' && 
            $_SESSION['user_role'] != 'staff') {
            $_SESSION['message'] = 'Vous n\'êtes pas autorisé à voir cette réservation.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Récupérer la chambre associée pour afficher les détails
        $room = $this->roomModel->getOneRoom((int)$reservation['room_id']);

        // Afficher la page de détail de la réservation
        echo $this->twig->render('reservationController/showReservation.html.twig', [
            'reservation' => $reservation,
            'room' => $room,
            'session' => $_SESSION ?? []
        ]);
    }

    /**
     * Méthode pour annuler une réservation
     */
    public function cancelReservation()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vous devez être connecté pour annuler une réservation.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=login');
            exit();
        }

        // Récupérer l'ID de la réservation depuis l'URL
        $reservationId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$reservationId) {
            $_SESSION['message'] = 'Numéro de réservation invalide.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Récupérer les détails de la réservation
        $reservation = $this->reservationModel->getReservationById((int)$reservationId);
        
        if (!$reservation) {
            $_SESSION['message'] = 'Réservation introuvable.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Vérifier que la réservation appartient bien à l'utilisateur connecté
        // Sauf si l'utilisateur est un admin
        if ($reservation['user_id'] != $_SESSION['user_id'] && $_SESSION['user_role'] != 'admin') {
            $_SESSION['message'] = 'Vous n\'êtes pas autorisé à annuler cette réservation.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Vérifier que la réservation n'est pas déjà annulée
        if ($reservation['status'] === 'cancelled') {
            $_SESSION['message'] = 'Cette réservation est déjà annulée.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Annuler la réservation
        $success = $this->reservationModel->cancelReservation((int)$reservationId);
        
        if ($success) {
            $_SESSION['message'] = 'Votre réservation a été annulée avec succès.';
            $_SESSION['success'] = true;
        } else {
            $_SESSION['message'] = 'Une erreur s\'est produite lors de l\'annulation de la réservation.';
            $_SESSION['success'] = false;
        }
        
        header('Location: index.php?page=dashboard');
        exit();
    }

    /**
     * Méthode pour mettre à jour le statut d'une réservation
     * Utilisée par le personnel ou l'admin
     */
    public function updateReservationStatus()
    {
        // Vérifier si l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || 
            ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'staff')) {
            $_SESSION['message'] = 'Vous n\'êtes pas autorisé à effectuer cette action.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=dashboard');
            exit();
        }

        // Récupérer les données du formulaire
        $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_SANITIZE_NUMBER_INT);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        
        if (!$reservationId || !$status) {
            $_SESSION['message'] = 'Données invalides.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=reservations');
            exit();
        }

        // Mettre à jour le statut
        $success = $this->reservationModel->updateReservationStatus((int)$reservationId, $status);
        
        if ($success) {
            $_SESSION['message'] = 'Le statut de la réservation a été mis à jour avec succès.';
            $_SESSION['success'] = true;
        } else {
            $_SESSION['message'] = 'Une erreur s\'est produite lors de la mise à jour du statut.';
            $_SESSION['success'] = false;
        }
        
        header('Location: index.php?page=reservations');
        exit();
    }

    /**
     * Méthode pour mettre à jour les disponibilités des chambres
     * Cette méthode devrait être exécutée par une tâche cron quotidienne
     */
    public function updateRoomAvailability()
    {
        // 1. Récupérer toutes les réservations terminées (date de départ passée)
        $completedReservations = $this->reservationModel->getCompletedReservations();
        
        // 2. Pour chaque réservation terminée, vérifier s'il y a d'autres réservations à venir
        foreach ($completedReservations as $reservation) {
            $roomId = $reservation['room_id'];
            $futureReservations = $this->reservationModel->getFutureReservationsForRoom($roomId);
            
            // 3. Si aucune réservation future, rendre la chambre disponible
            if (empty($futureReservations)) {
                $this->roomModel->updateRoomAvailability($roomId, true);
            }
        }
        
        echo "Mise à jour des disponibilités des chambres effectuée avec succès.";
    }
}