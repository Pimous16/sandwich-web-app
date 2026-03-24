-- Base de données
CREATE DATABASE IF NOT EXISTS `sandwich` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sandwich`;

-- Table commandes
CREATE TABLE `commandes` (
  `id_commande` INT(11) NOT NULL AUTO_INCREMENT,
  `jour` VARCHAR(50) DEFAULT NULL,
  `nom` VARCHAR(50) DEFAULT NULL,
  `date_de_commande` DATE DEFAULT NULL,
  `total` DECIMAL(10,2) DEFAULT NULL,
  `crudites` VARCHAR(4) DEFAULT NULL,
  `id_cuisinier` SMALLINT(6) DEFAULT NULL,
  `id_utilisateur` INT(11) NOT NULL,
  PRIMARY KEY (`id_commande`),
  UNIQUE KEY `commandes_unique` (`jour`, `id_utilisateur`, `date_de_commande`),
  KEY `id_cuisinier` (`id_cuisinier`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `fk_commandes_cuisinier` FOREIGN KEY (`id_cuisinier`) REFERENCES `cuisinier` (`id_cuisinier`) ON DELETE SET NULL,
  CONSTRAINT `fk_commandes_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table composition
CREATE TABLE `composition` (
  `id_commande` INT(11) NOT NULL,
  `id_sandwich` SMALLINT(6) NOT NULL,
  `crudites` TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (`id_commande`, `id_sandwich`),
  CONSTRAINT `fk_composition_commande` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  CONSTRAINT `fk_composition_sandwich` FOREIGN KEY (`id_sandwich`) REFERENCES `sandwich` (`id_sandwich`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table cuisinier
CREATE TABLE `cuisinier` (
  `id_cuisinier` SMALLINT(6) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id_cuisinier`)
) ENGINE=InnoDB;

-- Table facturation
CREATE TABLE `facturation` (
  `id_commande` INT(11) NOT NULL,
  `id_transaction` INT(11) NOT NULL,
  PRIMARY KEY (`id_commande`, `id_transaction`),
  CONSTRAINT `fk_facturation_commande` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  CONSTRAINT `fk_facturation_transaction` FOREIGN KEY (`id_transaction`) REFERENCES `transaction` (`id_transaction`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table remarque
CREATE TABLE `remarque` (
  `id_remarque` INT(11) NOT NULL AUTO_INCREMENT,
  `expediteur` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`id_remarque`)
) ENGINE=InnoDB;

-- Table sandwich
CREATE TABLE `sandwich` (
  `id_sandwich` SMALLINT(6) NOT NULL AUTO_INCREMENT,
  `prix` DECIMAL(10,2) DEFAULT NULL,
  `nom` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`id_sandwich`)
) ENGINE=InnoDB;

INSERT INTO `sandwich` (`id_sandwich`, `prix`, `nom`) VALUES
(1, 2.50, 'Dagobert'),
(2, 3.00, 'Curry'),
(3, 2.80, 'Vegetarien'),
(4, 2.20, 'Jambon');

-- Table signalement
CREATE TABLE `signalement` (
  `id_commande` INT(11) NOT NULL,
  `id_remarque` INT(11) NOT NULL,
  PRIMARY KEY (`id_commande`, `id_remarque`),
  CONSTRAINT `fk_signalement_commande` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  CONSTRAINT `fk_signalement_remarque` FOREIGN KEY (`id_remarque`) REFERENCES `remarque` (`id_remarque`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table transaction
CREATE TABLE `transaction` (
  `id_transaction` INT(11) NOT NULL AUTO_INCREMENT,
  `heure` TIME DEFAULT NULL,
  `montant` DECIMAL(10,2) DEFAULT NULL,
  `jour_` DATE DEFAULT NULL,
  `id_utilisateur` INT(11) NOT NULL,
  PRIMARY KEY (`id_transaction`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `fk_transaction_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table utilisateur
CREATE TABLE `utilisateur` (
  `id_utilisateur` INT(11) NOT NULL AUTO_INCREMENT,
  `solde` DECIMAL(10,2) DEFAULT 0.00,
  `email` VARCHAR(100) NOT NULL,
  `login` VARCHAR(50) DEFAULT NULL,
  `password` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB;