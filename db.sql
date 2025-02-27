
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Base de données : `dbHotelNeptune`
CREATE DATABASE IF NOT EXISTS dbHotelNeptune;
USE dbHotelNeptune;
-- Table Administrateur
CREATE TABLE admin (
   id_admin INT AUTO_INCREMENT,
   nom_admin VARCHAR(50),
   prenom_admin VARCHAR(50),
   email_admin VARCHAR(255) UNIQUE,
   mot_de_passe VARCHAR(255),
   super_admin TINYINT(1) DEFAULT 0,
   PRIMARY KEY(id_admin)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Client
CREATE TABLE client (
   id_client INT AUTO_INCREMENT,
   nom_client VARCHAR(50),
   prenom_client VARCHAR(50),
   numero_telephone VARCHAR(15),
   email_client VARCHAR(50) UNIQUE,
   numero_chambre VARCHAR(50),
   historique_reservation TEXT,
   mot_de_passe VARCHAR(255),
   adresse VARCHAR(100),
   PRIMARY KEY(id_client)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Photo
CREATE TABLE photo (
   id_photos INT AUTO_INCREMENT,
   nom_img VARCHAR(50),
   taille_img TEXT,
   chemin_fichier VARCHAR(255),
   PRIMARY KEY(id_photos)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Facture
CREATE TABLE facture (
   id_facture INT AUTO_INCREMENT,
   numero_cb VARCHAR(16),
   date_facture DATE,
   chemin_pdf VARCHAR(255),
   montant_total DECIMAL(10,2),
   PRIMARY KEY(id_facture)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Option Chambre
CREATE TABLE options (
   id_option INT AUTO_INCREMENT,
   nom_option VARCHAR(50),
   prix_supplementaire DECIMAL(10,2),
   PRIMARY KEY(id_option)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Notification
CREATE TABLE notification (
   id_notification INT AUTO_INCREMENT,
   message TEXT,
   date_envoi DATETIME,
   statut VARCHAR(50),
   id_client INT NOT NULL,
   PRIMARY KEY(id_notification),
   FOREIGN KEY(id_client) REFERENCES client(id_client)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Contact
CREATE TABLE contact (
   id_message INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   telephone VARCHAR(15) NOT NULL,
   message TEXT NOT NULL,
   PRIMARY KEY(id_message)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Chambre
CREATE TABLE chambre (
   num_chambre INT AUTO_INCREMENT,
   chambre_disponible BOOLEAN DEFAULT TRUE,
   prix_chambre DECIMAL(10,2),
   capacite DECIMAL(10,2),
   description TEXT,
   nom_chambre VARCHAR(50) NOT NULL,
   id_photos INT,
   PRIMARY KEY(num_chambre),
   FOREIGN KEY(id_photos) REFERENCES photo(id_photos)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Avis
CREATE TABLE avis (
   id_avis INT AUTO_INCREMENT,
   note DECIMAL(2,1),
   commentaire TEXT,
   date_avis DATE,
   id_client INT NOT NULL,
   num_chambre INT NOT NULL,
   PRIMARY KEY(id_avis),
   FOREIGN KEY(id_client) REFERENCES client(id_client),
   FOREIGN KEY(num_chambre) REFERENCES chambre(num_chambre)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Reservation
CREATE TABLE reservation (
   num_reservation INT AUTO_INCREMENT,
   date_debut DATETIME,
   date_fin DATETIME,
   facture_reservation TEXT,
   date_reservation DATE,
   statut VARCHAR(50),
   id_client INT NOT NULL,
   num_chambre INT NOT NULL,
   PRIMARY KEY(num_reservation),
   FOREIGN KEY(id_client) REFERENCES client(id_client),
   FOREIGN KEY(num_chambre) REFERENCES chambre(num_chambre)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Paiement
CREATE TABLE paiement (
   id_paiement INT AUTO_INCREMENT,
   numero_cb VARCHAR(16) NOT NULL,
   date_expiration DATE,
   ccv_cb VARCHAR(4),
   montant DECIMAL(10,2),
   statut VARCHAR(50),
   id_facture INT NOT NULL,
   num_reservation INT NOT NULL,
   PRIMARY KEY(id_paiement),
   FOREIGN KEY(id_facture) REFERENCES facture(id_facture),
   FOREIGN KEY(num_reservation) REFERENCES reservation(num_reservation)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Annulation
CREATE TABLE annulation (
   id_annulation INT AUTO_INCREMENT,
   motif_annulation TEXT,
   date_annulation DATE,
   num_reservation INT NOT NULL,
   PRIMARY KEY(id_annulation),
   FOREIGN KEY(num_reservation) REFERENCES reservation(num_reservation)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Option Chambre (Many to Many)
CREATE TABLE option_chambre (
   num_chambre INT,
   id_option INT,
   PRIMARY KEY(num_chambre, id_option),
   FOREIGN KEY(num_chambre) REFERENCES chambre(num_chambre),
   FOREIGN KEY(id_option) REFERENCES options(id_option)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;