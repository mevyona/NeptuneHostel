<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Reservation;
use MyApp\Model\ReservationModel;

class ReservationController
{
    private $twig;
    private $reservationModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->reservationModel = $dependencyContainer->get('ReservationModel');
    }

    public function reservations()
    {
        $reservations = $this->reservationModel->getAllReservations();
        echo $this->twig->render('reservation/reservations.html.twig', ['reservations' => $reservations]);
    }

    public function addReservation()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date_debut = filter_input(INPUT_POST, 'date_debut', FILTER_SANITIZE_STRING);
            $date_fin = filter_input(INPUT_POST, 'date_fin', FILTER_SANITIZE_STRING);
            $id_client = filter_input(INPUT_POST, 'id_client', FILTER_SANITIZE_NUMBER_INT);
            $num_chambre = filter_input(INPUT_POST, 'num_chambre', FILTER_SANITIZE_NUMBER_INT);

            if ($date_debut && $date_fin && $id_client && $num_chambre) {
                $reservation = new Reservation(null, $date_debut, $date_fin, $id_client, $num_chambre);
                $success = $this->reservationModel->createReservation($reservation);
                if ($success) {
                    header('Location: index.php?page=reserations');
                }
            }
        }
        echo $this->twig->render('reservation/addReservation.html.twig', []);
    }

    public function deleteReservation()
    {
        $id_reservation = filter_input(INPUT_GET, 'id_reservation', FILTER_SANITIZE_NUMBER_INT);
        $this->reservationModel->deleteReservation(intval($id_reservation));
        header('Location: index.php?page=reservations');
    }
}
?>
