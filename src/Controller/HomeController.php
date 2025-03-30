<?php

class HomeController
{
    private $roomModel;
    private $twig;

    public function __construct($roomModel, $twig)
    {
        $this->roomModel = $roomModel;
        $this->twig = $twig;
    }

    public function index()
    {
        $rooms = $this->roomModel->getAllRooms();
        
        $featuredRooms = array_slice($rooms, 0, 4);
        
        $roomStats = [
            'total' => count($rooms),
            'yearsOfExperience' => 10,
            'averageRating' => 4.8
        ];

        $viewData = [
            'featuredRooms' => $featuredRooms,
            'roomStats' => $roomStats,
            'session' => $_SESSION ?? []
        ];
        
        if (isset($_SESSION['contact_message'])) {
            $viewData['contact_message'] = $_SESSION['contact_message'];
            $viewData['contact_success'] = $_SESSION['contact_success'];
            unset($_SESSION['contact_message']);
            unset($_SESSION['contact_success']);
        }
        
        echo $this->twig->render('default/home.html.twig', $viewData);
    }
}
