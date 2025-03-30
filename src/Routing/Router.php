<?php

declare(strict_types=1);

namespace MyApp\Routing;

use MyApp\Controller\DefaultController;
use MyApp\Controller\UserController;
use MyApp\Controller\ReservationController;
use MyApp\Controller\RoomController;
use MyApp\Controller\PaymentController;
use MyApp\Controller\InvoiceController;
use MyApp\Service\DependencyContainer;

class Router
{
    private $dependencyContainer;
    private $pageMappings;
    private $defaultPage;
    private $errorPage;
    private $unauthorizedPage;

    public function __construct(DependencyContainer $dependencyContainer)
    {
        $this->dependencyContainer = $dependencyContainer;
        // Tableau contenant l'ensemble des pages (controller) de votre site
        // La clé est le mot qui sera récupéré dans la variable page de l'url
        // La valeur est un tableau composé de 3 colonnes
        // Colonne 1 : classe du contrôleur
        // Colonne 2 : nom de la méthode à appeler
        // Colonne 3 : rôle requis (null si accessible à tous)

        // Organize routes by controller for better readability
        $this->pageMappings = [
            'home' => [DefaultController::class, 'home', null],
            '404' => [DefaultController::class, 'error404', null],
            '500' => [DefaultController::class, 'error500', null],
            '403' => [DefaultController::class, 'error403', null],
            
            'users' => [UserController::class, 'listUsers', 'admin'],
            'listUsers' => [UserController::class, 'listUsers', 'admin'],
            'showUser' => [UserController::class, 'showUser', 'admin'],
            'addUser' => [UserController::class, 'addUser', 'admin'],
            'updateUser' => [UserController::class, 'updateUser', 'admin'],
            'deleteUser' => [UserController::class, 'deleteUser', 'admin'],
            'changePassword' => [UserController::class, 'changePassword', 'client'],
            'dashboard' => [UserController::class, 'dashboard', 'client'],
            'login' => [UserController::class, 'login', null],
            'register' => [UserController::class, 'register', null],
            'logout' => [UserController::class, 'logout', null],
            
            'reservations' => [ReservationController::class, 'listReservations', 'staff'],
            'showReservation' => [ReservationController::class, 'showReservation', 'client'],
            'addReservation' => [ReservationController::class, 'addReservation', 'client'],
            'updateReservation' => [ReservationController::class, 'updateReservation', 'staff'],
            'deleteReservation' => [ReservationController::class, 'deleteReservation', 'staff'],
            'initializePayment' => [ReservationController::class, 'initializePayment', 'client'],
            'confirmationReservation' => [ReservationController::class, 'showConfirmation', null],
            'update-room-availability' => [ReservationController::class, 'updateRoomAvailability', 'system'],
            
            'cancellations' => [CancellationController::class, 'listCancellations', 'staff'],
            'showCancellation' => [CancellationController::class, 'showCancellation', 'client'],
            'addCancellation' => [CancellationController::class, 'addCancellation', 'client'],

            'payment' => [PaymentController::class, 'showPaymentPage', 'client'],
            'processPayment' => [PaymentController::class, 'processPayment', 'client'],

            'reservationConfirmed' => [InvoiceController::class, 'reservationConfirmed', null],
            'downloadInvoice' => [InvoiceController::class, 'downloadInvoice', 'client'],
            
            'rooms' => [RoomController::class, 'listRooms', null],
            'showRoom' => [RoomController::class, 'showRoom', null],
            'addRoom' => [RoomController::class, 'addRoom', 'admin'],
            'updateRoom' => [RoomController::class, 'updateRoom', 'admin'],
            'deleteRoom' => [RoomController::class, 'deleteRoom', 'admin'],
            'bookRoomDirectly' => [RoomController::class, 'bookRoomDirectly', 'client'],

        ];
        $this->defaultPage = 'home';
        $this->errorPage = '404';
        $this->unauthorizedPage = '403';
    }

    public function route($twig)
    {
        // Start the session to access $_SESSION variables
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $requestedPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

        // Si l'url ne contient pas la variable page, redirection vers la page d'accueil
        if (!$requestedPage) {
            $requestedPage = $this->defaultPage;
        } else {
            // Si la valeur de la page ne correspond pas à une clé du tableau associatif, redirection vers une page d'erreur
            if (!array_key_exists($requestedPage, $this->pageMappings)) {
                $requestedPage = $this->errorPage;
            }
        }

        // Récupère la ligne qui correspond à la clé comprise dans page
        $controllerInfo = $this->pageMappings[$requestedPage];
        /* Destructuration du tableau en récupérant les informations du contrôleur */
        [$controllerClass, $method, $requiredRole] = $controllerInfo;

        // Vérifier les permissions si un rôle est requis
        if ($requiredRole !== null) {
            $userRole = $this->getUserRole();
            
            // Vérifier si l'utilisateur est connecté et a le rôle requis
            if (!$this->hasPermission($userRole, $requiredRole)) {
                // Rediriger vers la page non autorisée
                $unauthorizedInfo = $this->pageMappings[$this->unauthorizedPage];
                [$errorControllerClass, $errorMethod] = $unauthorizedInfo;
                $errorController = new $errorControllerClass($twig, $this->dependencyContainer);
                call_user_func([$errorController, $errorMethod]);
                return;
            }
        }

        // Vérification de l'existence de la classe et de la méthode du contrôleur a appeler
        if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
            // Instancie la classe récupérée
            $controller = new $controllerClass($twig, $this->dependencyContainer);
            //la fonction call_user_func appelle une méthode sur un objet
            call_user_func([$controller, $method]);
        } else {
            // Si la classe ou la méthode n'existe pas, utilisez le contrôleur d'erreur 500
            $error500Info = $this->pageMappings['500'];
            [$errorControllerClass, $errorMethod] = $error500Info;
            $errorController = new $errorControllerClass($twig, $this->dependencyContainer);
            call_user_func([$errorController, $errorMethod]);
        }
    }
    
    private function getUserRole(): ?string
    {
        // Vérifier si l'utilisateur est connecté via la session
        if (isset($_SESSION['user_role'])) {
            return $_SESSION['user_role'];
        }
        
        return null;
    }
    
    private function hasPermission(?string $userRole, string $requiredRole): bool
    {
        if ($userRole === null) {
            return false;
        }
        
        $roleHierarchy = [
            'client' => 0,
            'staff' => 1,
            'admin' => 2
        ];
        
        if (!isset($roleHierarchy[$userRole])) {
            return false;
        }
        
        if (!isset($roleHierarchy[$requiredRole])) {
            return false;
        }
        
        return $roleHierarchy[$userRole] >= $roleHierarchy[$requiredRole];
    }
}