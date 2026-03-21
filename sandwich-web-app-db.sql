-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 21 mars 2026 à 22:33
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `sandwich`
--
CREATE DATABASE IF NOT EXISTS `sandwich` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sandwich`;

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id_commande` int(11) NOT NULL,
  `jour` varchar(50) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `date_de_commande` date DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `crudites` varchar(4) DEFAULT NULL,
  `id_cuisinier` smallint(6) DEFAULT NULL,
  `id_utilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `composition`
--

CREATE TABLE `composition` (
  `id_commande` int(11) NOT NULL,
  `id_sandwich` smallint(6) NOT NULL,
  `crudites` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cuisinier`
--

CREATE TABLE `cuisinier` (
  `id_cuisinier` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `facturation`
--

CREATE TABLE `facturation` (
  `id_commande` int(11) NOT NULL,
  `id_transaction` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `remarque`
--

CREATE TABLE `remarque` (
  `id_remarque` int(11) NOT NULL,
  `expéditeur` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sandwich`
--

CREATE TABLE `sandwich` (
  `id_sandwich` smallint(6) NOT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sandwich`
--

INSERT INTO `sandwich` (`id_sandwich`, `prix`, `nom`) VALUES
(1, 2.50, 'Dagobert'),
(2, 3.00, 'Curry'),
(3, 2.80, 'Vegetarien'),
(4, 2.20, 'Jambon');

-- --------------------------------------------------------

--
-- Structure de la table `signalement`
--

CREATE TABLE `signalement` (
  `id_commande` int(11) NOT NULL,
  `id_remarque` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transaction`
--

CREATE TABLE `transaction` (
  `id_transaction` int(11) NOT NULL,
  `heure` time DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `jour_` date DEFAULT NULL,
  `id_utilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `solde` decimal(10,2) DEFAULT 0.00,
  `email` varchar(100) NOT NULL,
  `login` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD UNIQUE KEY `commandes_unique` (`jour`,`id_utilisateur`,`date_de_commande`),
  ADD KEY `id_cuisinier` (`id_cuisinier`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `cuisinier`
--
ALTER TABLE `cuisinier`
  ADD PRIMARY KEY (`id_cuisinier`);

--
-- Index pour la table `remarque`
--
ALTER TABLE `remarque`
  ADD PRIMARY KEY (`id_remarque`);

--
-- Index pour la table `sandwich`
--
ALTER TABLE `sandwich`
  ADD PRIMARY KEY (`id_sandwich`);

--
-- Index pour la table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id_transaction`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `cuisinier`
--
ALTER TABLE `cuisinier`
  MODIFY `id_cuisinier` smallint(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `remarque`
--
ALTER TABLE `remarque`
  MODIFY `id_remarque` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sandwich`
--
ALTER TABLE `sandwich`
  MODIFY `id_sandwich` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
