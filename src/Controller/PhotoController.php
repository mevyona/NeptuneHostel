<?php
declare(strict_types=1);
namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Photo;
use MyApp\Model\PhotoModel;

class PhotoController
{
    private $twig;
    private $photoModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->photoModel = $dependencyContainer->get('PhotoModel');
    }

    public function photos()
    {
        $photos = $this->photoModel->getAllPhotos();
        echo $this->twig->render('photo/photos.html.twig', ['photos' => $photos]);
    }

    public function addPhoto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom_img = filter_input(INPUT_POST, 'nom_img', FILTER_SANITIZE_STRING);
            $taille_img = filter_input(INPUT_POST, 'taille_img', FILTER_SANITIZE_STRING);
            $chemin_fichier = filter_input(INPUT_POST, 'chemin_fichier', FILTER_SANITIZE_STRING);

            if ($nom_img && $taille_img && $chemin_fichier) {
                $photo = new Photo(null, $nom_img, $taille_img, $chemin_fichier);
                $success = $this->photoModel->createPhoto($photo);
                if ($success) {
                    header('Location: index.php?page=photos');
                }
            }
        }
        echo $this->twig->render('photo/addPhoto.html.twig', []);
    }

    public function updatePhoto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_photo = filter_input(INPUT_POST, 'id_photo', FILTER_SANITIZE_NUMBER_INT);
            $nom_img = filter_input(INPUT_POST, 'nom_img', FILTER_SANITIZE_STRING);
            $taille_img = filter_input(INPUT_POST, 'taille_img', FILTER_SANITIZE_STRING);
            $chemin_fichier = filter_input(INPUT_POST, 'chemin_fichier', FILTER_SANITIZE_STRING);

            if ($id_photo && $nom_img && $taille_img && $chemin_fichier) {
                $photo = new Photo($id_photo, $nom_img, $taille_img, $chemin_fichier);
                $success = $this->photoModel->updatePhoto($photo);
                if ($success) {
                    header('Location: index.php?page=photos');
                }
            }
        } else {
            $id_photo = filter_input(INPUT_GET, 'id_photo', FILTER_SANITIZE_NUMBER_INT);
            $photo = $this->photoModel->getOnePhoto(intval($id_photo));
            echo $this->twig->render('photo/updatePhoto.html.twig', ['photo' => $photo]);
        }
    }

    public function deletePhoto()
    {
        $id_photo = filter_input(INPUT_GET, 'id_photo', FILTER_SANITIZE_NUMBER_INT);
        $this->photoModel->deletePhoto(intval($id_photo));
        header('Location: index.php?page=photos');
    }
}
?>
