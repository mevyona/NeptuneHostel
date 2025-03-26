SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Base de données: `NeptuneHotelDB`
DROP DATABASE IF EXISTS NeptuneHotelDB;
CREATE DATABASE NeptuneHotelDB;
USE NeptuneHotelDB;

-- Supprime d'abord toutes les tables pour éviter les problèmes de contraintes de clé étrangère
DROP TABLE IF EXISTS Cancellation;
DROP TABLE IF EXISTS ContactMessage;
DROP TABLE IF EXISTS Notification;
DROP TABLE IF EXISTS Review;
DROP TABLE IF EXISTS Payment;
DROP TABLE IF EXISTS Invoice;
DROP TABLE IF EXISTS Reservation;
DROP TABLE IF EXISTS RoomOption;
DROP TABLE IF EXISTS Room;
DROP TABLE IF EXISTS Media;
DROP TABLE IF EXISTS User;

-- Table Utilisateur
CREATE TABLE User (
   id INT AUTO_INCREMENT,
   first_name VARCHAR(50) NOT NULL,
   last_name VARCHAR(50) NOT NULL,
   email VARCHAR(100) NOT NULL UNIQUE,
   phone VARCHAR(15),
   password VARCHAR(255) NOT NULL,
   role ENUM('client', 'admin', 'staff') NOT NULL DEFAULT 'client',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Média
CREATE TABLE Media (
   id INT AUTO_INCREMENT,
   file_name VARCHAR(255) NOT NULL,
   file_path VARCHAR(255) NOT NULL,
   file_type VARCHAR(50) NOT NULL,
   file_size INT,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Chambre
CREATE TABLE Room (
   id INT AUTO_INCREMENT,
   name VARCHAR(100) NOT NULL,
   is_available BOOLEAN DEFAULT TRUE,
   price DECIMAL(10,2) NOT NULL,
   capacity INT NOT NULL,
   description TEXT,
   featured_image_id INT,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY(id),
   FOREIGN KEY(featured_image_id) REFERENCES Media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Options de Chambre
CREATE TABLE RoomOption (
   id INT AUTO_INCREMENT,
   room_id INT NOT NULL,
   option_name VARCHAR(100) NOT NULL,
   option_value TEXT,
   is_highlighted BOOLEAN DEFAULT FALSE,
   additional_cost DECIMAL(10,2) DEFAULT 0.00,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY(id),
   FOREIGN KEY(room_id) REFERENCES Room(id) ON DELETE CASCADE,
   INDEX(option_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Réservation
CREATE TABLE Reservation (
   id INT AUTO_INCREMENT,
   user_id INT NOT NULL,
   room_id INT NOT NULL,
   check_in DATETIME NOT NULL,
   check_out DATETIME NOT NULL,
   status ENUM('pending', 'confirmed', 'checked_in', 'completed', 'cancelled') DEFAULT 'pending',
   total_price DECIMAL(10,2) NOT NULL,
   special_requests TEXT,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY(id),
   FOREIGN KEY(user_id) REFERENCES User(id) ON DELETE CASCADE,
   FOREIGN KEY(room_id) REFERENCES Room(id) ON DELETE CASCADE,
   INDEX(check_in),
   INDEX(check_out),
   INDEX(status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Facture
CREATE TABLE Invoice (
   id INT AUTO_INCREMENT,
   reservation_id INT NOT NULL UNIQUE,
   invoice_number VARCHAR(20) NOT NULL UNIQUE,
   amount DECIMAL(10,2) NOT NULL,
   tax_amount DECIMAL(10,2) NOT NULL,
   total_amount DECIMAL(10,2) NOT NULL,
   invoice_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   due_date DATE NOT NULL,
   status ENUM('pending', 'paid', 'cancelled', 'refunded') DEFAULT 'pending',
   pdf_path VARCHAR(255),
   PRIMARY KEY(id),
   FOREIGN KEY(reservation_id) REFERENCES Reservation(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Paiement
CREATE TABLE Payment (
   id INT AUTO_INCREMENT,
   invoice_id INT NOT NULL,
   amount DECIMAL(10,2) NOT NULL,
   payment_method ENUM('credit_card', 'bank_transfer', 'cash', 'paypal') NOT NULL,
   transaction_id VARCHAR(100),
   status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
   payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   last_four_digits VARCHAR(4),
   PRIMARY KEY(id),
   FOREIGN KEY(invoice_id) REFERENCES Invoice(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Avis
CREATE TABLE Review (
   id INT AUTO_INCREMENT,
   user_id INT NOT NULL,
   room_id INT NOT NULL,
   reservation_id INT NOT NULL,
   rating DECIMAL(2,1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
   comment TEXT,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY(id),
   FOREIGN KEY(user_id) REFERENCES User(id) ON DELETE CASCADE,
   FOREIGN KEY(room_id) REFERENCES Room(id) ON DELETE CASCADE,
   FOREIGN KEY(reservation_id) REFERENCES Reservation(id) ON DELETE CASCADE,
   UNIQUE KEY(user_id, reservation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Notification
CREATE TABLE Notification (
   id INT AUTO_INCREMENT,
   user_id INT NOT NULL,
   title VARCHAR(100) NOT NULL,
   message TEXT NOT NULL,
   is_read BOOLEAN DEFAULT FALSE,
   notification_type ENUM('reservation', 'payment', 'system', 'other') NOT NULL DEFAULT 'system',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY(id),
   FOREIGN KEY(user_id) REFERENCES User(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Message de Contact
CREATE TABLE ContactMessage (
   id INT AUTO_INCREMENT,
   first_name VARCHAR(50) NOT NULL,
   last_name VARCHAR(50) NOT NULL,
   email VARCHAR(100) NOT NULL,
   phone VARCHAR(15),
   message TEXT NOT NULL,
   status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Annulation
CREATE TABLE Cancellation (
   id INT AUTO_INCREMENT,
   reservation_id INT NOT NULL UNIQUE,
   reason TEXT,
   refund_amount DECIMAL(10,2),
   cancellation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   cancelled_by_id INT NOT NULL,
   PRIMARY KEY(id),
   FOREIGN KEY(reservation_id) REFERENCES Reservation(id) ON DELETE CASCADE,
   FOREIGN KEY(cancelled_by_id) REFERENCES User(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;