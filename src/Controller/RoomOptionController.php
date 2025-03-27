<?php
namespace MyApp\Controller;

use MyApp\Model\RoomOptionModel;
use MyApp\Model\RoomModel;
use MyApp\Entity\RoomOption;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class RoomOptionController
{
    private Environment $twig;
    private RoomOptionModel $roomOptionModel;
    private RoomModel $roomModel;

    public function __construct(Environment $twig, DependencyContainer $container)
    {
        $this->twig = $twig;
        $this->roomOptionModel = $container->get('RoomOptionModel');
        $this->roomModel = $container->get('RoomModel');
    }

    public function listOptions()
    {
        $options = $this->roomOptionModel->getAllOptions();
        echo $this->twig->render('roomOptionController/listOptions.html.twig', ['options' => $options]);
    }

    public function addOption()
    {
        $rooms = $this->roomModel->getAllRooms();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_id = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
            $name = filter_input(INPUT_POST, 'option_name', FILTER_SANITIZE_STRING);
            $value = filter_input(INPUT_POST, 'option_value', FILTER_SANITIZE_STRING);
            $highlighted = isset($_POST['is_highlighted']);
            $cost = filter_input(INPUT_POST, 'additional_cost', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $room = $this->roomModel->getOneRoom((int)$room_id);
            $option = new RoomOption(null, $room, $name, $value, $highlighted, (float)$cost, '', '');
            $this->roomOptionModel->createOption($option);
            $_SESSION['message'] = 'Option ajoutée';
            header('Location: index.php?page=list-room-options');
            exit();
        }
        echo $this->twig->render('roomOptionController/addOption.html.twig', ['rooms' => $rooms]);
    }

    public function updateOption()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $option = $this->roomOptionModel->getOneOption((int)$id);
        if (!$option) {
            $_SESSION['message'] = 'Option introuvable';
            header('Location: index.php?page=list-room-options');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = filter_input(INPUT_POST, 'option_name', FILTER_SANITIZE_STRING);
            $value = filter_input(INPUT_POST, 'option_value', FILTER_SANITIZE_STRING);
            $highlighted = isset($_POST['is_highlighted']);
            $cost = filter_input(INPUT_POST, 'additional_cost', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $updated = new RoomOption($id, $option->getRoom(), $name, $value, $highlighted, $cost, $option->getCreatedAt(), $option->getUpdatedAt());
            $this->roomOptionModel->updateOption($updated);
            $_SESSION['message'] = 'Option modifiée';
            header('Location: index.php?page=list-room-options');
            exit();
        }
        echo $this->twig->render('roomOptionController/updateOption.html.twig', ['option' => $option]);
    }

    public function deleteOption()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->roomOptionModel->deleteOption((int)$id);
        $_SESSION['message'] = 'Option supprimée';
        header('Location: index.php?page=list-room-options');
    }
}
