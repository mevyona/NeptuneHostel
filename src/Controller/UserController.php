<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Entity\User;
use MyApp\Model\UserModel;
use MyApp\Model\ReservationModel; // Ajout de l'import manquant
use MyApp\Model\RoomModel; // Ajout de l'import manquant
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class UserController
{
    private Environment $twig;
    private UserModel $userModel;
    private DependencyContainer $dependencyContainer; // Ajout du conteneur de dépendances

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer) {
        $this->twig = $twig;
        $this->userModel = $dependencyContainer->get('UserModel');
        $this->dependencyContainer = $dependencyContainer; // Sauvegarde du conteneur pour usage ultérieur
    }

    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?page=dashboard');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            
            if (!empty($email) && !empty($password)) {
                $user = $this->userModel->getUserByEmail($email);
                
                if ($user !== null && password_verify($password, $user->getPassword())) {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['user_name'] = $user->getFirstName() . ' ' . $user->getLastName();
                    $_SESSION['user_role'] = $user->getRole();                     
                    $_SESSION['message'] = 'Bienvenue, ' . $user->getFirstName() . ' !';
                    $_SESSION['success'] = true;
                    
                    ob_clean();                     header('Location: index.php?page=dashboard');
                    exit();
                } else {
                    $_SESSION['message'] = 'Email ou mot de passe invalide.';
                    $_SESSION['success'] = false;
                }
            } else {
                $_SESSION['message'] = "Veuillez renseigner à la fois l'email et le mot de passe.";
                $_SESSION['success'] = false;
            }
        }
        
        $message = $_SESSION['message'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['message'], $_SESSION['success']);
        
        echo $this->twig->render('userController/login.html.twig', [
            'session' => [
                'message' => $message,
                'success' => $success
            ]
        ]);
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
                $_SESSION = [];
        
                if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
        
        header('Location: index.php');
        exit();
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($password)) {
                if ($password !== $confirmPassword) {
                    $_SESSION['message'] = 'Les mots de passe ne correspondent pas.';
                    $_SESSION['success'] = false;
                } else {
                    $existingUser = $this->userModel->getUserByEmail($email);
                    
                    if ($existingUser !== null) {
                        $_SESSION['message'] = 'Cet email existe déjà. Veuillez vous connecter.';
                        $_SESSION['success'] = false;
                    } else {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        
                        $user = new User(
                            null, 
                            $firstName, 
                            $lastName, 
                            $email, 
                            $phone, 
                            $hashedPassword, 
                            'client'
                        );
                        
                        $success = $this->userModel->createUser($user);
                        
                        if ($success) {
                            $_SESSION['message'] = 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.';
                            $_SESSION['success'] = true;
                            header('Location: index.php?page=login');
                            exit;
                        } else {
                            $_SESSION['message'] = 'Erreur lors de l\'inscription.';
                            $_SESSION['success'] = false;
                        }
                    }
                }
            } else {
                $_SESSION['message'] = 'Veuillez remplir tous les champs obligatoires.';
                $_SESSION['success'] = false;
            }
        }
        
        echo $this->twig->render('userController/register.html.twig', ['session' => $_SESSION ?? []]);
    }

    public function listUsers()
    {
        $users = $this->userModel->getAllUsers();
        echo $this->twig->render('userController/listUsers.html.twig', [
            'users' => $users,
            'session' => $_SESSION ?? []
        ]);
    }

    public function showUser()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $user = $this->userModel->getOneUser(intval($id));
        
        if ($user === null) {
            $_SESSION['message'] = 'Utilisateur non trouvé.';
            $_SESSION['success'] = false;
            header('Location: index.php?page=404');
            exit;
        }
        
                $this->userModel->loadUserRelations($user);
        
        echo $this->twig->render('userController/showUser.html.twig', [
            'user' => $user,
            'session' => $_SESSION ?? []
        ]);
    }

    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $password = $_POST['password'] ?? '';
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
            
            if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($password)) {
                                $existingUser = $this->userModel->getUserByEmail($email);
                
                if ($existingUser !== null) {
                    $_SESSION['message'] = 'Cet email existe déjà.';
                    $_SESSION['success'] = false;
                } else {
                                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                                        $user = new User(
                        null, 
                        $firstName, 
                        $lastName, 
                        $email, 
                        $phone, 
                        $hashedPassword, 
                        $role ?? 'client'
                    );
                    
                    $success = $this->userModel->createUser($user);
                    
                    if ($success) {
                        $_SESSION['message'] = 'Utilisateur ajouté avec succès !';
                        $_SESSION['success'] = true;
                        header('Location: index.php?page=listUsers');
                        exit;
                    } else {
                        $_SESSION['message'] = 'Erreur lors de la création de l\'utilisateur.';
                        $_SESSION['success'] = false;
                    }
                }
            } else {
                $_SESSION['message'] = 'Veuillez remplir tous les champs obligatoires.';
                $_SESSION['success'] = false;
            }
        }
        
        echo $this->twig->render('userController/addUser.html.twig', [
            'session' => $_SESSION ?? []
        ]);
    }

    public function updateUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
            
            if (!empty($id) && !empty($firstName) && !empty($lastName) && !empty($email)) {
                $existingUser = $this->userModel->getOneUser(intval($id));
                
                if ($existingUser === null) {
                    $_SESSION['message'] = 'Utilisateur non trouvé.';
                    $_SESSION['success'] = false;
                    header('Location: index.php?page=listUsers');
                    exit;
                }
                
                                if ($email !== $existingUser->getEmail()) {
                    $emailUser = $this->userModel->getUserByEmail($email);
                    if ($emailUser !== null) {
                        $_SESSION['message'] = 'Cet email existe déjà pour un autre utilisateur.';
                        $_SESSION['success'] = false;
                        header('Location: index.php?page=updateUser&id=' . $id);
                        exit;
                    }
                }
                
                                $user = new User(
                    intval($id),
                    $firstName,
                    $lastName,
                    $email,
                    $phone,
                    $existingUser->getPassword(),                     $role ?? 'client'
                );
                
                $success = $this->userModel->updateUser($user);
                
                if ($success) {
                    $_SESSION['message'] = 'Utilisateur mis à jour avec succès !';
                    $_SESSION['success'] = true;
                    header('Location: index.php?page=listUsers');
                    exit;
                } else {
                    $_SESSION['message'] = 'Erreur lors de la mise à jour de l\'utilisateur.';
                    $_SESSION['success'] = false;
                }
            } else {
                $_SESSION['message'] = 'Veuillez remplir tous les champs obligatoires.';
                $_SESSION['success'] = false;
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $user = $this->userModel->getOneUser(intval($id));
            
            if ($user === null) {
                $_SESSION['message'] = 'Utilisateur non trouvé.';
                $_SESSION['success'] = false;
                header('Location: index.php?page=listUsers');
                exit;
            }
            
            echo $this->twig->render('userController/updateUser.html.twig', [
                'user' => $user,
                'session' => $_SESSION ?? []
            ]);
        }
    }
    
    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (!empty($id) && !empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
                if ($newPassword !== $confirmPassword) {
                    $_SESSION['message'] = 'Les nouveaux mots de passe ne correspondent pas.';
                    $_SESSION['success'] = false;
                    header('Location: index.php?page=changePassword&id=' . $id);
                    exit;
                }
                
                $user = $this->userModel->getOneUser(intval($id));
                
                if ($user === null) {
                    $_SESSION['message'] = 'Utilisateur non trouvé.';
                    $_SESSION['success'] = false;
                    header('Location: index.php?page=listUsers');
                    exit;
                }
                
                                if (!password_verify($currentPassword, $user->getPassword())) {
                    $_SESSION['message'] = 'Le mot de passe actuel est incorrect.';
                    $_SESSION['success'] = false;
                    header('Location: index.php?page=changePassword&id=' . $id);
                    exit;
                }
                
                                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $success = $this->userModel->updateUserPassword(intval($id), $hashedPassword);
                
                if ($success) {
                    $_SESSION['message'] = 'Mot de passe modifié avec succès !';
                    $_SESSION['success'] = true;
                    header('Location: index.php?page=showUser&id=' . $id);
                    exit;
                } else {
                    $_SESSION['message'] = 'Erreur lors de la modification du mot de passe.';
                    $_SESSION['success'] = false;
                }
            } else {
                $_SESSION['message'] = 'Veuillez remplir tous les champs.';
                $_SESSION['success'] = false;
            }
            
            header('Location: index.php?page=changePassword&id=' . $id);
            exit;
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $user = $this->userModel->getOneUser(intval($id));
            
            if ($user === null) {
                $_SESSION['message'] = 'Utilisateur non trouvé.';
                $_SESSION['success'] = false;
                header('Location: index.php?page=listUsers');
                exit;
            }
            
            echo $this->twig->render('userController/changePassword.html.twig', [
                'user' => $user,
                'session' => $_SESSION ?? []
            ]);
        }
    }

    public function deleteUser()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!empty($id)) {
            $user = $this->userModel->getOneUser(intval($id));
            
            if ($user === null) {
                $_SESSION['message'] = 'Utilisateur non trouvé.';
                $_SESSION['success'] = false;
            } else {
                $success = $this->userModel->deleteUser(intval($id));
                
                if ($success) {
                    $_SESSION['message'] = 'Utilisateur supprimé avec succès !';
                    $_SESSION['success'] = true;
                } else {
                    $_SESSION['message'] = 'Erreur lors de la suppression de l\'utilisateur.';
                    $_SESSION['success'] = false;
                }
            }
        }
        
        header('Location: index.php?page=listUsers');
        exit;
    }
    
    public function dashboard()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        try {
            $user = $this->userModel->getOneUser(intval($_SESSION['user_id']));
            
            if ($user === null) {
                $_SESSION['message'] = 'Utilisateur non trouvé.';
                $_SESSION['success'] = false;
                header('Location: index.php?page=logout');
                exit;
            }
            
            // Charger toutes les relations utilisateur
            $this->userModel->loadUserRelations($user);
            
            // Préparation des données additionnelles pour les administrateurs et le personnel
            $viewData = [
                'user' => $user,
                'session' => $_SESSION ?? []
            ];
            
            // Pour les administrateurs et le personnel, charger des données additionnelles
            if ($user->getRole() == 'admin' || $user->getRole() == 'staff') {
                // Utiliser le conteneur de dépendances pour obtenir le modèle
                $reservationModel = $this->dependencyContainer->get('ReservationModel');
                $viewData['recentReservations'] = $reservationModel->getRecentReservations(5);
                
                // Vérifier si CancellationModel existe, sinon gérer les annulations différemment
                if ($this->dependencyContainer->has('CancellationModel')) {
                    $cancellationModel = $this->dependencyContainer->get('CancellationModel');
                    $viewData['recentCancellations'] = $cancellationModel->getRecentCancellations(5);
                } else {
                    // Alternative: filtrer les réservations avec statut "cancelled"
                    $viewData['recentCancellations'] = [];
                }
            }
            
            // Pour les administrateurs, charger des statistiques
            if ($user->getRole() == 'admin') {
                // Statistiques utilisateurs
                $viewData['userStats'] = [
                    'clients' => count($this->userModel->getUsersByRole('client')),
                    'staff' => count($this->userModel->getUsersByRole('staff')),
                    'admins' => count($this->userModel->getUsersByRole('admin'))
                ];
                
                // Statistiques chambres - utiliser le conteneur de dépendances
                $roomModel = $this->dependencyContainer->get('RoomModel');
                $rooms = $roomModel->getAllRooms();
                $availableRooms = array_filter($rooms, function($room) {
                    return $room->isAvailable();
                });
                
                $viewData['roomStats'] = [
                    'total' => count($rooms),
                    'available' => count($availableRooms),
                    'occupancy' => count($rooms) > 0 ? round((1 - count($availableRooms) / count($rooms)) * 100) : 0
                ];
            }
            
            echo $this->twig->render('userController/dashboard.html.twig', $viewData);
        } catch (\Exception $e) {
            $_SESSION['message'] = 'Une erreur est survenue lors de l\'accès à votre profil: ' . $e->getMessage();
            $_SESSION['success'] = false;
            header('Location: index.php');
            exit;
        }
    }
}