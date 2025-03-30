<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use MyApp\Model\RoomModel;
use Twig\Environment;

class DefaultController
{
    private Environment $twig;
    private $dependencyContainer;
    private RoomModel $roomModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer) 
    {
        $this->twig = $twig;
        $this->dependencyContainer = $dependencyContainer;
        $this->roomModel = $dependencyContainer->get('RoomModel');
    }

        public function home()
    {
                $allRooms = $this->roomModel->getAllRooms();
        $totalRooms = count($allRooms);
        
                $roomStats = [
            'total' => $totalRooms,
            'yearsOfExperience' => 12,             'averageRating' => 4.8         ];
        
                        $featuredRooms = [];
        $availableRooms = array_filter($allRooms, function($room) {
            return $room->isAvailable();
        });
        
                if (!empty($availableRooms)) {
                        usort($availableRooms, function($a, $b) {
                return $a->getPrice() <=> $b->getPrice();
            });
            
                        $featuredRooms = array_slice($availableRooms, 0, min(4, count($availableRooms)));
        } else {
                        $featuredRooms = array_slice($allRooms, 0, min(4, count($allRooms)));
        }

        echo $this->twig->render('default/home.html.twig', [
            'roomStats' => $roomStats,
            'featuredRooms' => $featuredRooms,
            'session' => $_SESSION ?? []
        ]);
    }

        public function error404()
    {
        http_response_code(404);
        echo $this->twig->render('error/404.html.twig');
    }

        public function error500()
    {
        http_response_code(500);
        echo $this->twig->render('error/500.html.twig');
    }

        public function error403()
    {
        http_response_code(403);
        echo $this->twig->render('error/403.html.twig');
    }

        public function login()
    {
                if (isset($_SESSION['user_id'])) {
            header('Location: index.php?page=home');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (!empty($email) && !empty($password)) {
                                $userModel = $this->dependencyContainer->get('UserModel');
                $user = $userModel->getUserByEmail($email);

                if ($user !== null && password_verify($password, $user->getPassword())) {
                                        $_SESSION['user_id'] = $user->getId();
                    $_SESSION['user_name'] = $user->getFullName();
                    $_SESSION['user_role'] = $user->getRole();
                    $_SESSION['user_email'] = $user->getEmail();
                    
                                        if ($user->isAdmin()) {
                        header('Location: index.php?page=users');
                    } else {
                        header('Location: index.php?page=profile');
                    }
                    exit;
                } else {
                    $_SESSION['message'] = 'Invalid email or password.';
                }
            } else {
                $_SESSION['message'] = 'Please fill in all fields.';
            }
        }
        
        echo $this->twig->render('auth/login.html.twig');
    }

        public function register()
    {
                if (isset($_SESSION['user_id'])) {
            header('Location: index.php?page=home');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($password) && !empty($confirmPassword)) {
                if ($password !== $confirmPassword) {
                    $_SESSION['message'] = 'Passwords do not match.';
                } else {
                                        $userModel = $this->dependencyContainer->get('UserModel');
                    $existingUser = $userModel->getUserByEmail($email);
                    
                    if ($existingUser !== null) {
                        $_SESSION['message'] = 'Email already exists.';
                    } else {
                                                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        
                                                $user = new \MyApp\Entity\User(
                            null, 
                            $firstName, 
                            $lastName, 
                            $email, 
                            $phone, 
                            $hashedPassword, 
                            'client'
                        );
                        
                        $success = $userModel->createUser($user);
                        
                        if ($success) {
                            $_SESSION['message'] = 'Account created successfully! You can now login.';
                            header('Location: index.php?page=login');
                            exit;
                        } else {
                            $_SESSION['message'] = 'Error creating account.';
                        }
                    }
                }
            } else {
                $_SESSION['message'] = 'Please fill in all required fields.';
            }
        }
        
        echo $this->twig->render('auth/register.html.twig');
    }

        public function logout()
    {
                $_SESSION = [];

                if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        
                header('Location: index.php?page=login');
        exit;
    }

        public function contact()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
            
            if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($message)) {
                                                
                                                                
                                $success = true;
                
                if ($success) {
                    $_SESSION['message'] = 'Your message has been sent. We will contact you shortly.';
                    header('Location: index.php?page=contact');
                    exit;
                } else {
                    $_SESSION['message'] = 'Error sending message.';
                }
            } else {
                $_SESSION['message'] = 'Please fill in all required fields.';
            }
        }
        
        echo $this->twig->render('default/contact.html.twig');
    }

        public function about()
    {
        echo $this->twig->render('default/about.html.twig');
    }
}
