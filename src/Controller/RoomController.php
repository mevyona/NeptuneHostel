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
            'rooms' => $rooms,
            'session' => $_SESSION ?? []
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
            
            $media = null;
            
                        if (!empty($_FILES['image_file']['name'])) {
                                $uploadDir = 'uploads/rooms/';
                
                                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = $_FILES['image_file']['name'];
                $fileType = $_FILES['image_file']['type'];
                $fileSize = $_FILES['image_file']['size'];
                $fileTmpName = $_FILES['image_file']['tmp_name'];
                
                                $uniqueName = uniqid('room_') . '_' . $fileName;
                $filePath = $uploadDir . $uniqueName;
                
                                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($fileType, $allowedTypes)) {
                    $_SESSION['message'] = 'Type de fichier non autorisé. Seuls JPG, PNG et GIF sont acceptés.';
                    $_SESSION['success'] = false;
                } 
                                elseif ($fileSize > 5 * 1024 * 1024) {
                    $_SESSION['message'] = 'Le fichier est trop volumineux. La taille maximale est de 5MB.';
                    $_SESSION['success'] = false;
                } 
                                elseif (move_uploaded_file($fileTmpName, $filePath)) {
                                        $media = new Media(null, $fileName, $filePath, $fileType, $fileSize, '');
                    $this->mediaModel->createMedia($media);
                } else {
                    $_SESSION['message'] = "Erreur lors de l'upload du fichier.";
                    $_SESSION['success'] = false;
                }
            } 
                        elseif (!empty($_POST['featured_image_id'])) {
                $image_id = filter_input(INPUT_POST, 'featured_image_id', FILTER_SANITIZE_NUMBER_INT);
                $media = $this->mediaModel->getOneMedia((int)$image_id);
            }
            
                        if ($name && $price && $capacity && $media) {
                $room = new Room(null, $name, $is_available, (float)$price, (int)$capacity, $description, $media, '', '');
                if ($this->roomModel->createRoom($room)) {
                    $_SESSION['message'] = 'Chambre ajoutée avec succès';
                    $_SESSION['success'] = true;
                    header('Location: index.php?page=rooms');
                    exit();
                } else {
                    $_SESSION['message'] = "Erreur lors de l'ajout de la chambre";
                    $_SESSION['success'] = false;
                }
            } else {
                if (!isset($_SESSION['message'])) {
                    $_SESSION['message'] = 'Veuillez remplir tous les champs obligatoires et fournir une image';
                    $_SESSION['success'] = false;
                }
            }
        }

        echo $this->twig->render('roomController/addRoom.html.twig', [
            'mediaList' => $mediaList,
            'session' => $_SESSION ?? []
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
                header('Location: index.php?page=rooms');
                exit();
            } else {
                $_SESSION['message'] = 'Erreur sur la chambre.';
                header('Location: index.php?page=rooms');
                exit();
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $room = $this->roomModel->getOneRoom((int)$id);

            if (!$room) {
                $_SESSION['message'] = 'Chambre introuvable';
                header('Location: index.php?page=rooms');
                exit();
            }
        }

        echo $this->twig->render('roomController/updateRoom.html.twig', [
            'room' => $room,
            'mediaList' => $mediaList,
            'session' => $_SESSION ?? []
        ]);
    }

    public function showRoom()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $room = $this->roomModel->getOneRoom((int)$id);

        if (!$room) {
            $_SESSION['message'] = 'Chambre introuvable';
            header('Location: index.php?page=rooms');
            exit();
        }

                $similarRooms = $this->roomModel->getSimilarRooms($room->getCapacity(), $room->getId());

        echo $this->twig->render('roomController/showRoom.html.twig', [
            'room' => $room,
            'similarRooms' => $similarRooms, 
            'session' => $_SESSION ?? []
        ]);
    }

    public function deleteRoom()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->roomModel->deleteRoom((int)$id);
        $_SESSION['message'] = 'Chambre supprimée';
        header('Location: index.php?page=rooms');
    }

        public function bookRoomDirectly()
    {
                if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vous devez être connecté pour effectuer une réservation.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=login');
            exit();
        }

                $roomId = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
        $checkIn = filter_input(INPUT_POST, 'check_in', FILTER_SANITIZE_STRING);
        $checkOut = filter_input(INPUT_POST, 'check_out', FILTER_SANITIZE_STRING);
        $specialRequests = filter_input(INPUT_POST, 'special_requests', FILTER_SANITIZE_STRING);

                $room = $this->roomModel->getOneRoom((int)$roomId);

                $checkInDate = new \DateTime($checkIn);
        $checkOutDate = new \DateTime($checkOut);
        $interval = $checkInDate->diff($checkOutDate);
        $numberOfNights = $interval->days;

                $roomTotal = $room->getPrice() * $numberOfNights;
        $taxAmount = $roomTotal * 0.10;         $totalAmount = $roomTotal + $taxAmount;

                $_SESSION['reservation_data'] = [
            'room_id' => $roomId,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'number_of_nights' => $numberOfNights,
            'room_total' => $roomTotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'special_requests' => $specialRequests
        ];

                header('Location: index.php?page=payment');
        exit();
    }
}