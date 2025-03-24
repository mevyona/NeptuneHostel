<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Model\RoomModel;
use MyApp\Model\MediaModel;
use MyApp\Entity\Room;
use MyApp\Entity\Media;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class RoomController
{
    private Environment $twig;
    private RoomModel $roomModel;
    private MediaModel $mediaModel;

    public function __construct(Environment $twig, DependencyContainer $container)
    {
        $this->twig = $twig;
        $this->roomModel = $container->get('RoomModel');
        $this->mediaModel = $container->get('MediaModel');
    }

    public function listRooms()
    {
        $rooms = $this->roomModel->getAllRooms();
        echo $this->twig->render('roomController/listRooms.html.twig', [
            'rooms' => $rooms
        ]);
    }

    public function addRoom()
    {
        $mediaList = $this->mediaModel->getAllMedia();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $is_available = isset($_POST['is_available']) ? true : false;
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_NUMBER_INT);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $image_id = filter_input(INPUT_POST, 'featured_image_id', FILTER_SANITIZE_NUMBER_INT);

            if ($name && $price && $capacity) {
                $media = $this->mediaModel->getOneMedia((int)$image_id);
                $room = new Room(null, $name, $is_available, (float)$price, (int)$capacity, $description, $media, '', '');
                $this->roomModel->createRoom($room);
                $_SESSION['message'] = 'Chambre ajoutée avec succès';
                header('Location: index.php?page=list-rooms');
                exit();
            } else {
                $_SESSION['message'] = 'Veuillez remplir tous les champs obligatoires';
            }
        }

        echo $this->twig->render('roomController/addRoom.html.twig', [
            'mediaList' => $mediaList
        ]);
    }

    public function updateRoom()
    {
        $mediaList = $this->mediaModel->getAllMedia();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $is_available = isset($_POST['is_available']) ? true : false;
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_NUMBER_INT);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $image_id = filter_input(INPUT_POST, 'featured_image_id', FILTER_SANITIZE_NUMBER_INT);

            $room = $this->roomModel->getOneRoom((int)$id);
            if ($room) {
                $media = $this->mediaModel->getOneMedia((int)$image_id);
                $updatedRoom = new Room((int)$id, $name, $is_available, (float)$price, (int)$capacity, $description, $media, '', '');
                $this->roomModel->updateRoom($updatedRoom);
                $_SESSION['message'] = 'Chambre modifiée avec succès';
                header('Location: index.php?page=list-rooms');
                exit();
            } else {
                $_SESSION['message'] = 'Erreur sur la chambre.';
                header('Location: index.php?page=list-rooms');
                exit();
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $room = $this->roomModel->getOneRoom((int)$id);

            if (!$room) {
                $_SESSION['message'] = 'Chambre introuvable';
                header('Location: index.php?page=list-rooms');
                exit();
            }
        }

        echo $this->twig->render('roomController/updateRoom.html.twig', [
            'room' => $room,
            'mediaList' => $mediaList
        ]);
    }

    public function showRoom()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $room = $this->roomModel->getOneRoom((int)$id);

        if (!$room) {
            $_SESSION['message'] = 'Chambre introuvable';
            header('Location: index.php?page=list-rooms');
            exit();
        }

        echo $this->twig->render('roomController/showRoom.html.twig', [
            'room' => $room
        ]);
    }

    public function deleteRoom()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->roomModel->deleteRoom((int)$id);
        $_SESSION['message'] = 'Chambre supprimée';
        header('Location: index.php?page=list-rooms');
    }
}
