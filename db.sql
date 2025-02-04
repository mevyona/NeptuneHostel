
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Base de données : `dbHotelNeptune`

CREATE DATABASE IF NOT EXISTS dbHotelNeptune;
USE dbHotelNeptune;
CREATE TABLE client(
   id_client INT,
   nom_client VARCHAR(50),
   prenom_client VARCHAR(50),
   numeroTelephone_client VARCHAR(15),
   mails_client VARCHAR(50),
   numeroChambre VARCHAR(50),
   historique_Reservation VARCHAR(50),
   mot_de_passe VARCHAR(255),
   adresse VARCHAR(100),
   PRIMARY KEY(id_client)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE photo(
   id_photos INT,
   nom_img VARCHAR(50),
   taille_img TEXT,
   chemin_fichier VARCHAR(50),
   PRIMARY KEY(id_photos)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE options(
   id_option INT,
   nom_option VARCHAR(50),
   prix_supplémentaire VARCHAR(50),
   PRIMARY KEY(id_option)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE notification(
   id_notification INT,
   message VARCHAR(50),
   date_envoie DATE,
   statut VARCHAR(50),
   id_client INT NOT NULL,
   PRIMARY KEY(id_notification),
   FOREIGN KEY(id_client) REFERENCES client(id_client)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE Hotel(
   idHotel INT,
   nom_hotel VARCHAR(50) NOT NULL,
   adresse_hotel VARCHAR(50) NOT NULL,
   tel_hotel INT NOT NULL,
   description VARCHAR(50) NOT NULL,
   emailHotel VARCHAR(50),
   PRIMARY KEY(idHotel)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE Contact(
   Id_message VARCHAR(50),
   Nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   Tel INT NOT NULL,
   message VARCHAR(50) NOT NULL,
   PRIMARY KEY(Id_message)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE admin(
   id_admin INT,
   nom_admin VARCHAR(50),
   prenom_admin VARCHAR(50),
   email_admin VARCHAR(255),
   mot_de_passe_ VARCHAR(255),
   Id_message VARCHAR(50),
   idHotel INT NOT NULL,
   PRIMARY KEY(id_admin),
   FOREIGN KEY(Id_message) REFERENCES Contact(Id_message),
   FOREIGN KEY(idHotel) REFERENCES Hotel(idHotel)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE chambre(
   num_chambre INT,
   chambreDisponible_reservation VARCHAR(50),
   photosDescriptive_Chambre VARCHAR(50),
   prix_Chambre VARCHAR(50),
   capacité DECIMAL(10,2),
   description VARCHAR(50),
   nom_chambre VARCHAR(50) NOT NULL,
   id_photos INT NOT NULL,
   idHotel INT NOT NULL,
   PRIMARY KEY(num_chambre),
   FOREIGN KEY(id_photos) REFERENCES photo(id_photos),
   FOREIGN KEY(idHotel) REFERENCES Hotel(idHotel)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE avis(
   id_avis INT,
   note DECIMAL(10,2),
   commentaire VARCHAR(50),
   date_avis DATE,
   id_client INT NOT NULL,
   num_chambre INT NOT NULL,
   PRIMARY KEY(id_avis),
   FOREIGN KEY(id_client) REFERENCES client(id_client),
   FOREIGN KEY(num_chambre) REFERENCES chambre(num_chambre)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE réservation(
   num_réservation INT,
   DateDebut_réservation DATETIME,
   DateFin_réservation DATETIME,
   facture_réservation TEXT,
   date_reservation DATE,
   statut VARCHAR(50),
   id_client INT NOT NULL,
   num_chambre INT NOT NULL,
   PRIMARY KEY(num_réservation),
   FOREIGN KEY(id_client) REFERENCES client(id_client),
   FOREIGN KEY(num_chambre) REFERENCES chambre(num_chambre)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE paiement_(
   id_paiement VARCHAR(50),
   Numero_CB INT NOT NULL,
   dateExpiration DATETIME,
   ccv_cb VARCHAR(50),
   montant DECIMAL(10,2),
   statut VARCHAR(50),
   num_réservation INT NOT NULL,
   PRIMARY KEY(id_paiement),
   FOREIGN KEY(num_réservation) REFERENCES réservation(num_réservation)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE Facture(
   id_facture INT,
   numero_cb DECIMAL(25,2),
   date_facture DATE,
   chemin_pdf VARCHAR(50),
   montant_total DECIMAL(10,2),
   num_réservation INT NOT NULL,
   PRIMARY KEY(id_facture),
   FOREIGN KEY(num_réservation) REFERENCES réservation(num_réservation)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE annulation(
   id_annulation INT,
   motif_annulation VARCHAR(50),
   date_annulation VARCHAR(50),
   num_réservation INT NOT NULL,
   PRIMARY KEY(id_annulation),
   FOREIGN KEY(num_réservation) REFERENCES réservation(num_réservation)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE option_chambre(
   num_chambre INT,
   id_option INT,
   PRIMARY KEY(num_chambre, id_option),
   FOREIGN KEY(num_chambre) REFERENCES chambre(num_chambre),
   FOREIGN KEY(id_option) REFERENCES options(id_option)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;