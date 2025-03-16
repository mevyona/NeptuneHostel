<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\User;
use MyApp\Model\UserModel;

class UserController
{
    private $twig;
    private $userModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->userModel = $dependencyContainer->get('UserModel');
    }

    public function users()
    {
        $users = $this->userModel->getAllUsers();
        echo $this->twig->render('user/users.html.twig', ['users' => $users]);
    }

    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

            if ($firstName && $lastName && $email && $password) {
                $user = new User(null, $firstName, $lastName, $email, $phone, $password, $role);
                $success = $this->userModel->createUser($user);
                if ($success) {
                    header('Location: index.php?page=users');
                    exit;
                }
            }
        }
        echo $this->twig->render('user/addUser.html.twig', []);
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

            if ($id && $firstName && $lastName && $email) {
                $user = new User($id, $firstName, $lastName, $email, $phone, null, $role);
                $success = $this->userModel->updateUser($user);
                if ($success) {
                    header('Location: index.php?page=users');
                    exit;
                }
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $user = $this->userModel->getOneUser(intval($id));
            echo $this->twig->render('user/updateUser.html.twig', ['user' => $user]);
        }
    }

    public function deleteUser()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($id) {
            $this->userModel->deleteUser(intval($id));
        }
        header('Location: index.php?page=users');
        exit;
    }
    
    public function changeUserPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if ($id && $newPassword && $newPassword === $confirmPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $success = $this->userModel->updateUserPassword(intval($id), $hashedPassword);
                
                if ($success) {
                    header('Location: index.php?page=users&message=password_updated');
                    exit;
                }
            }
            
            header('Location: index.php?page=change_password&id=' . $id . '&error=1');
            exit;
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $user = $this->userModel->getOneUser(intval($id));
            echo $this->twig->render('user/changePassword.html.twig', ['user' => $user]);
        }
    }
}