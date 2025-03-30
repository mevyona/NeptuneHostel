<?php

declare(strict_types=1);

namespace MyApp\Routing;

use MyApp\Controller\DefaultController;
use MyApp\Controller\UserController;
use MyApp\Controller\ReservationController;
use MyApp\Controller\RoomController;
use MyApp\Controller\PaymentController;
use MyApp\Controller\InvoiceController;
use MyApp\Controller\ContactMessageController;
use MyApp\Service\DependencyContainer;

class Router
{
    private array $routes = [];
    private DependencyContainer $container;
    private $dependencyContainer;
    private $pageMappings;
    private $defaultPage;
    private $errorPage;
    private $unauthorizedPage;

    public function __construct(DependencyContainer $dependencyContainer)
    {
        $this->dependencyContainer = $dependencyContainer;
        $this->container = $dependencyContainer;

        $this->pageMappings = [
            'home' => [DefaultController::class, 'home', null],
            'legals' => [DefaultController::class, 'legals', null],
            'cgv' => [DefaultController::class, 'cgv', null],
            '403' => [DefaultController::class, 'error403', null],
            '404' => [DefaultController::class, 'error404', null],
            '500' => [DefaultController::class, 'error500', null],
            
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

            'reservationConfirmed' => [InvoiceController::class, 'reservationConfirmed', 'client'],
            'downloadInvoice' => [InvoiceController::class, 'downloadInvoice', 'client'],
            
            'rooms' => [RoomController::class, 'listRooms', null],
            'showRoom' => [RoomController::class, 'showRoom', null],
            'addRoom' => [RoomController::class, 'addRoom', 'admin'],
            'updateRoom' => [RoomController::class, 'updateRoom', 'admin'],
            'deleteRoom' => [RoomController::class, 'deleteRoom', 'admin'],
            'bookRoomDirectly' => [RoomController::class, 'bookRoomDirectly', 'client'],

            'contact' => [ContactMessageController::class, 'contact', null],
        ];
        $this->defaultPage = 'home';
        $this->errorPage = '404';
        $this->unauthorizedPage = '403';

        $this->initializeRoutes();
    }

    public function route($twig)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $requestedPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

        if (!$requestedPage) {
            $requestedPage = $this->defaultPage;
        } else {
            if (!array_key_exists($requestedPage, $this->pageMappings)) {
                $requestedPage = $this->errorPage;
            }
        }

        $controllerInfo = $this->pageMappings[$requestedPage];
        [$controllerClass, $method, $requiredRole] = $controllerInfo;

        if ($requiredRole !== null) {
            $userRole = $this->getUserRole();
            
            if (!$this->hasPermission($userRole, $requiredRole)) {
                $unauthorizedInfo = $this->pageMappings[$this->unauthorizedPage];
                [$errorControllerClass, $errorMethod] = $unauthorizedInfo;
                $errorController = new $errorControllerClass($twig, $this->dependencyContainer);
                call_user_func([$errorController, $errorMethod]);
                return;
            }
        }

        if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
            $controller = new $controllerClass($twig, $this->dependencyContainer);
            call_user_func([$controller, $method]);
        } else {
            $error500Info = $this->pageMappings['500'];
            [$errorControllerClass, $errorMethod] = $error500Info;
            $errorController = new $errorControllerClass($twig, $this->dependencyContainer);
            call_user_func([$errorController, $errorMethod]);
        }
    }
    
    private function getUserRole(): ?string
    {
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

    /**
     * Initialise les routes de l'application
     */
    private function initializeRoutes(): void
    {
        $this->routes = [
            // Autres routes existantes...
            
            // Routes pour les réservations
            'reservations' => [
                'controller' => 'ReservationController',
                'method' => 'listReservations'
            ],
            'showReservation' => [
                'controller' => 'ReservationController',
                'method' => 'showReservation'
            ],
            'addReservation' => [
                'controller' => 'ReservationController',
                'method' => 'addReservation'
            ],
            'updateReservationStatus' => [
                'controller' => 'ReservationController',
                'method' => 'updateReservationStatus'
            ],
            'deleteReservation' => [
                'controller' => 'ReservationController',
                'method' => 'deleteReservation'
            ],
            'downloadInvoice' => [
                'controller' => 'ReservationController',
                'method' => 'downloadInvoice'
            ],
            
            // Autres routes existantes...
        ];
    }
}