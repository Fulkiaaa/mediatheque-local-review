-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 03 juin 2025 à 13:00
-- Version du serveur : 8.2.0
-- Version de PHP : 8.1.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `films_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `acteurs`
--

DROP TABLE IF EXISTS `acteurs`;
CREATE TABLE IF NOT EXISTS `acteurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `date_deces` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `acteurs`
--

INSERT INTO `acteurs` (`id`, `nom`, `prenom`, `date_naissance`, `date_deces`) VALUES
(1, 'DiCaprio', 'Leonardo', '1974-11-11', NULL),
(2, 'Page', 'Elliot', '1987-02-21', NULL),
(3, 'Washington', 'Denzel', '1954-12-28', NULL),
(4, 'Ferguson', 'Rebecca', '1983-10-19', NULL),
(5, 'MORIN', 'Clara', '2000-02-09', '0000-00-00'),
(6, 'MORIN', 'Owen', '2011-04-09', '0000-00-00');

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

DROP TABLE IF EXISTS `avis`;
CREATE TABLE IF NOT EXISTS `avis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_film` int DEFAULT NULL,
  `vu` tinyint(1) DEFAULT NULL,
  `note` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_film` (`id_film`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `films`
--

DROP TABLE IF EXISTS `films`;
CREATE TABLE IF NOT EXISTS `films` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `synopsis` text COLLATE utf8mb4_general_ci,
  `annee` int DEFAULT NULL,
  `duree` int DEFAULT NULL,
  `id_genre` int DEFAULT NULL,
  `id_realisateur` int DEFAULT NULL,
  `id_support` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_genre` (`id_genre`),
  KEY `id_realisateur` (`id_realisateur`),
  KEY `id_support` (`id_support`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `films`
--

INSERT INTO `films` (`id`, `titre`, `synopsis`, `annee`, `duree`, `id_genre`, `id_realisateur`, `id_support`) VALUES
(2, 'Tenetszszsz', 'Un agent voyage à travers le temps pour sauver le monde.', 2020, 150, 2, 1, 2),
(3, 'Dune', 'Un jeune héritier découvre un destin lié à une planète dangereuse.', 2021, 155, 1, 2, 1),
(6, 'Drole de vie', 'Sur ma drôle de vie', 2025, 125, 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `films_acteurs`
--

DROP TABLE IF EXISTS `films_acteurs`;
CREATE TABLE IF NOT EXISTS `films_acteurs` (
  `id_film` int NOT NULL,
  `id_acteur` int NOT NULL,
  PRIMARY KEY (`id_film`,`id_acteur`),
  KEY `id_acteur` (`id_acteur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `films_acteurs`
--

INSERT INTO `films_acteurs` (`id_film`, `id_acteur`) VALUES
(2, 3),
(3, 4),
(6, 5);

-- --------------------------------------------------------

--
-- Structure de la table `genres`
--

DROP TABLE IF EXISTS `genres`;
CREATE TABLE IF NOT EXISTS `genres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `genres`
--

INSERT INTO `genres` (`id`, `nom`) VALUES
(1, 'Science-fiction'),
(2, 'Action'),
(3, 'Drame'),
(6, 'Horreur');

-- --------------------------------------------------------

--
-- Structure de la table `realisateurs`
--

DROP TABLE IF EXISTS `realisateurs`;
CREATE TABLE IF NOT EXISTS `realisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `realisateurs`
--

INSERT INTO `realisateurs` (`id`, `nom`, `prenom`, `date_naissance`) VALUES
(1, 'Nolan', 'Christopher', '1970-07-30'),
(2, 'Villeneuve', 'Denis', '1967-10-03');

-- --------------------------------------------------------

--
-- Structure de la table `support`
--

DROP TABLE IF EXISTS `support`;
CREATE TABLE IF NOT EXISTS `support` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `numero_serie` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `support`
--

INSERT INTO `support` (`id`, `type`, `numero_serie`) VALUES
(1, 'Blu-ray', 'BR123456'),
(2, 'DVD', 'DVD789654'),
(3, 'Clé USB', '26288265DF');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_utilisateur` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mot_de_passe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prenom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'admin',
  `actif` tinyint(1) DEFAULT '1',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `derniere_connexion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom_utilisateur` (`nom_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom_utilisateur`, `email`, `mot_de_passe`, `nom`, `prenom`, `role`, `actif`, `date_creation`, `derniere_connexion`) VALUES
(4, 'Clara', 'clara@email.com', '$2y$10$jyJd3iRKTTUhXYCp4sAGsOmvk6Y3ZOCfwOB7rjyCLuqSTC6bGtz1u', 'MORIN', 'Clara', 'admin', 1, '2025-06-03 12:47:08', '2025-06-03 12:51:13'),
(5, 'admin', 'admin@email.com', '$2y$10$w056QyuW63f3u8pyHLU/UuzBoMBrQNgikioq7aJTAt2Hy4dwr2bXi', 'admin', 'admin', 'admin', 1, '2025-06-03 12:49:18', '2025-06-03 12:56:37');

-- --------------------------------------------------------

--
-- Structure de la table `version`
--

DROP TABLE IF EXISTS `version`;
CREATE TABLE IF NOT EXISTS `version` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_version` int NOT NULL,
  `date_version` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `version`
--

INSERT INTO `version` (`id`, `numero_version`, `date_version`) VALUES
(1, 18, '2025-06-03');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`id_film`) REFERENCES `films` (`id`);

--
-- Contraintes pour la table `films`
--
ALTER TABLE `films`
  ADD CONSTRAINT `films_ibfk_1` FOREIGN KEY (`id_genre`) REFERENCES `genres` (`id`),
  ADD CONSTRAINT `films_ibfk_2` FOREIGN KEY (`id_realisateur`) REFERENCES `realisateurs` (`id`),
  ADD CONSTRAINT `films_ibfk_3` FOREIGN KEY (`id_support`) REFERENCES `support` (`id`);

--
-- Contraintes pour la table `films_acteurs`
--
ALTER TABLE `films_acteurs`
  ADD CONSTRAINT `films_acteurs_ibfk_1` FOREIGN KEY (`id_film`) REFERENCES `films` (`id`),
  ADD CONSTRAINT `films_acteurs_ibfk_2` FOREIGN KEY (`id_acteur`) REFERENCES `acteurs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
