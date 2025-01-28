SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de données : `dbHotelNeptune`
--

CREATE TABLE `Administrateur` (
  `id_admin` int(11) NOT NULL,
  `nom_admin` varchar(50) NOT NULL,
  `prenom_admin` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `Chambre` (
  `num_chambre` int(11) NOT NULL,
  `chambre_Disponible` varchar(50) NOT NULL,
  `photosDescriptive_Chambre` varchar(50) NOT NULL,
  `prix_Chambre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `Client` (
  `id_client` int(11) NOT NULL,
  `nom_client` varchar(50) NOT NULL,
  `prenom_client` varchar(50) NOT NULL,
  `NuméroTelephone_client` char(15) NOT NULL,
  `mails_client` varchar(50) NOT NULL,
  `numéro_chambre` varchar(50) NOT NULL,
  `prixTotal_Chambre` varchar(50) NOT NULL,
  `historique_Reservation` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `Paiement` (
  `id_paiement` int(11) NOT NULL,
  `numero_cb` varchar(50) NOT NULL,
  `date_expiration` date NOT NULL,
  `ccv_cb` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `Reservation` (
  `num_reservation` int(11) NOT NULL,
  `dateDebut_reservation` date NOT NULL,
  `dateFin_reservation` date NOT NULL,
  `chambre_Disponible` varchar(50) NOT NULL,
  `annulation` varchar(50) NOT NULL,
  `facture` text NOT NULL,
  `Option_Chambre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin`
--
ALTER TABLE `Admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Index pour la table `chambre`
--
ALTER TABLE `Chambre`
  ADD PRIMARY KEY (`num_chambre`);

--
-- Index pour la table `client`
--
ALTER TABLE `Client`
  ADD PRIMARY KEY (`id_client`);

--
-- Index pour la table `paiement`
--
ALTER TABLE `Paiement`
  ADD PRIMARY KEY (`id_paiement`);

--
-- Index pour la table `réservation`
--
ALTER TABLE `Reservation`
  ADD PRIMARY KEY (`num_reservation`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin`
--
ALTER TABLE `Admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `chambre`
--
ALTER TABLE `Chambre`
  MODIFY `num_chambre` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `Client`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `paiement`
--
ALTER TABLE `Paiement`
  MODIFY `id_paiement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `réservation`
--
ALTER TABLE `Reservation`
  MODIFY `num_reservation` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;