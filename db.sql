SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Base de données : `dbHotelNeptune`
CREATE DATABASE IF NOT EXISTS dbHotelNeptune;
USE dbHotelNeptune;

-- Table Photo (créée en premier pour éviter l'erreur de clé étrangère)
CREATE TABLE `Photo` (
  `id_photos` int(11) NOT NULL AUTO_INCREMENT,
  `nom_img` varchar(100) NOT NULL,
  `taille_img` int(11) NOT NULL,
  `chemin_fichier` varchar(255) NOT NULL,
  PRIMARY KEY (`id_photos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Administrateur
CREATE TABLE `Administrateur` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `nom_admin` varchar(50) NOT NULL,
  `prenom_admin` varchar(50) NOT NULL,
  `email_admin` varchar(100) UNIQUE NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Client
CREATE TABLE `Client` (
  `id_client` int(11) NOT NULL AUTO_INCREMENT,
  `nom_client` varchar(50) NOT NULL,
  `prenom_client` varchar(50) NOT NULL,
  `telephone_client` varchar(15) NOT NULL,
  `email_client` varchar(100) UNIQUE NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `adresse` TEXT,
  `historique_reservations` TEXT,
  PRIMARY KEY (`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Chambre
CREATE TABLE `Chambre` (
  `num_chambre` int(11) NOT NULL AUTO_INCREMENT,
  `type_chambre` varchar(50) NOT NULL,
  `disponibilite` BOOLEAN DEFAULT TRUE,
  `prix` DECIMAL(10,2) NOT NULL,
  `id_photos` int(11),
  `capacite` int NOT NULL,
  `description` TEXT,
  PRIMARY KEY (`num_chambre`),
  FOREIGN KEY (`id_photos`) REFERENCES `Photo`(`id_photos`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Réservation
CREATE TABLE `Reservation` (
  `num_reservation` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11),
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NOT NULL,
  `statut` ENUM('Confirmée', 'Annulée', 'En attente') DEFAULT 'En attente',
  `prix_total` DECIMAL(10,2) NOT NULL,
  `date_reservation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`num_reservation`),
  FOREIGN KEY (`id_client`) REFERENCES `Client`(`id_client`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Paiement
CREATE TABLE `Paiement` (
  `id_paiement` int(11) NOT NULL AUTO_INCREMENT,
  `num_reservation` int(11),
  `numero_cb` varchar(16) NOT NULL,
  `date_expiration` DATE NOT NULL,
  `cvv` varchar(4) NOT NULL,
  `montant` DECIMAL(10,2) NOT NULL,
  `statut` ENUM('Réussi', 'Échoué', 'En attente') DEFAULT 'En attente',
  PRIMARY KEY (`id_paiement`),
  FOREIGN KEY (`num_reservation`) REFERENCES `Reservation`(`num_reservation`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Facture
CREATE TABLE `Facture` (
  `id_facture` int(11) NOT NULL AUTO_INCREMENT,
  `num_reservation` int(11),
  `date_facture` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `montant_total` DECIMAL(10,2) NOT NULL,
  `chemin_pdf` varchar(255) NOT NULL,
  PRIMARY KEY (`id_facture`),
  FOREIGN KEY (`num_reservation`) REFERENCES `Reservation`(`num_reservation`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Annulation
CREATE TABLE `Annulation` (
  `id_annulation` int(11) NOT NULL AUTO_INCREMENT,
  `num_reservation` int(11),
  `motif_annulation` TEXT,
  `date_annulation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_annulation`),
  FOREIGN KEY (`num_reservation`) REFERENCES `Reservation`(`num_reservation`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Options de Chambre
CREATE TABLE `Options_Chambre` (
  `id_option` int(11) NOT NULL AUTO_INCREMENT,
  `nom_option` varchar(100) NOT NULL,
  `prix_supplementaire` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id_option`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table de relation Chambre-Option
CREATE TABLE `Chambre_Option` (
  `num_chambre` int(11),
  `id_option` int(11),
  PRIMARY KEY (`num_chambre`, `id_option`),
  FOREIGN KEY (`num_chambre`) REFERENCES `Chambre`(`num_chambre`) ON DELETE CASCADE,
  FOREIGN KEY (`id_option`) REFERENCES `Options_Chambre`(`id_option`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Avis
CREATE TABLE `Avis` (
  `id_avis` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11),
  `num_chambre` int(11),
  `note` INT CHECK (`note` BETWEEN 1 AND 5),
  `commentaire` TEXT,
  `date_avis` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_avis`),
  FOREIGN KEY (`id_client`) REFERENCES `Client`(`id_client`) ON DELETE CASCADE,
  FOREIGN KEY (`num_chambre`) REFERENCES `Chambre`(`num_chambre`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Notifications
CREATE TABLE `Notifications` (
  `id_notification` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11),
  `message` TEXT NOT NULL,
  `date_envoi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `statut` ENUM('Non lu', 'Lu') DEFAULT 'Non lu',
  PRIMARY KEY (`id_notification`),
  FOREIGN KEY (`id_client`) REFERENCES `Client`(`id_client`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;