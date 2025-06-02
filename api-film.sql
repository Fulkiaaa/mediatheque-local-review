-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 02 juin 2025 à 08:27
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
-- Base de données : `api-film`
--

DELIMITER $$
--
-- Procédures
--
DROP PROCEDURE IF EXISTS `incrementer_version`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `incrementer_version` ()   BEGIN
  UPDATE version SET numero_version = numero_version + 1 WHERE id = 1;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `acteurs`
--

DROP TABLE IF EXISTS `acteurs`;
CREATE TABLE IF NOT EXISTS `acteurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `date_deces` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `acteurs`
--

INSERT INTO `acteurs` (`id`, `nom`, `date_naissance`, `date_deces`) VALUES
(1, 'Leonardo DiCaprio', '1974-11-11', NULL),
(2, 'Meryl Streep', '1949-06-22', NULL),
(3, 'Heath Ledger', '1979-04-04', '2008-01-22');

--
-- Déclencheurs `acteurs`
--
DROP TRIGGER IF EXISTS `after_insert_acteurs`;
DELIMITER $$
CREATE TRIGGER `after_insert_acteurs` AFTER INSERT ON `acteurs` FOR EACH ROW BEGIN
  CALL incrementer_version();
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_update_acteurs`;
DELIMITER $$
CREATE TRIGGER `after_update_acteurs` AFTER UPDATE ON `acteurs` FOR EACH ROW BEGIN
  CALL incrementer_version();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

DROP TABLE IF EXISTS `avis`;
CREATE TABLE IF NOT EXISTS `avis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `film_id` int NOT NULL,
  `user_id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `note` tinyint DEFAULT NULL,
  `vu` tinyint(1) DEFAULT '0',
  `commentaire` text COLLATE utf8mb4_general_ci,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_film` (`film_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `films`
--

DROP TABLE IF EXISTS `films`;
CREATE TABLE IF NOT EXISTS `films` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `synopsis` text COLLATE utf8mb4_general_ci,
  `annee` year NOT NULL,
  `duree` smallint DEFAULT NULL,
  `support_id` int NOT NULL,
  `genre_id` int NOT NULL,
  `realisateur_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `support_id` (`support_id`),
  KEY `genre_id` (`genre_id`),
  KEY `realisateur_id` (`realisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `films`
--

INSERT INTO `films` (`id`, `titre`, `synopsis`, `annee`, `duree`, `support_id`, `genre_id`, `realisateur_id`) VALUES
(1, 'Inception', 'Un voleur capable d’entrer dans les rêves partage ses connaissances.', '2010', 148, 1, 1, 1),
(2, 'The Shining', 'Un gardien d’hôtel sombre dans la folie durant l’hiver.', '1980', 146, 2, 2, 2),
(3, 'Lady Bird', 'Une adolescente en conflit avec sa mère cherche son identité.', '2017', 94, 3, 3, 3);

--
-- Déclencheurs `films`
--
DROP TRIGGER IF EXISTS `after_insert_films`;
DELIMITER $$
CREATE TRIGGER `after_insert_films` AFTER INSERT ON `films` FOR EACH ROW BEGIN
  CALL incrementer_version();
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_update_films`;
DELIMITER $$
CREATE TRIGGER `after_update_films` AFTER UPDATE ON `films` FOR EACH ROW BEGIN
  CALL incrementer_version();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `film_acteur`
--

DROP TABLE IF EXISTS `film_acteur`;
CREATE TABLE IF NOT EXISTS `film_acteur` (
  `film_id` int NOT NULL,
  `acteur_id` int NOT NULL,
  PRIMARY KEY (`film_id`,`acteur_id`),
  KEY `acteur_id` (`acteur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `film_acteur`
--

INSERT INTO `film_acteur` (`film_id`, `acteur_id`) VALUES
(1, 1),
(3, 2),
(2, 3);

-- --------------------------------------------------------

--
-- Structure de la table `genres`
--

DROP TABLE IF EXISTS `genres`;
CREATE TABLE IF NOT EXISTS `genres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `genres`
--

INSERT INTO `genres` (`id`, `nom`) VALUES
(1, 'Action'),
(2, 'Drame'),
(3, 'Comédie');

--
-- Déclencheurs `genres`
--
DROP TRIGGER IF EXISTS `after_insert_genres`;
DELIMITER $$
CREATE TRIGGER `after_insert_genres` AFTER INSERT ON `genres` FOR EACH ROW BEGIN
  CALL incrementer_version();
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_update_genres`;
DELIMITER $$
CREATE TRIGGER `after_update_genres` AFTER UPDATE ON `genres` FOR EACH ROW BEGIN
  CALL incrementer_version();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `realisateurs`
--

DROP TABLE IF EXISTS `realisateurs`;
CREATE TABLE IF NOT EXISTS `realisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `date_deces` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `realisateurs`
--

INSERT INTO `realisateurs` (`id`, `nom`, `date_naissance`, `date_deces`) VALUES
(1, 'Christopher Nolan', '1970-07-30', NULL),
(2, 'Stanley Kubrick', '1928-07-26', '1999-03-07'),
(3, 'Greta Gerwig', '1983-08-04', NULL);

--
-- Déclencheurs `realisateurs`
--
DROP TRIGGER IF EXISTS `after_insert_realisateurs`;
DELIMITER $$
CREATE TRIGGER `after_insert_realisateurs` AFTER INSERT ON `realisateurs` FOR EACH ROW BEGIN
  CALL incrementer_version();
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_update_realisateurs`;
DELIMITER $$
CREATE TRIGGER `after_update_realisateurs` AFTER UPDATE ON `realisateurs` FOR EACH ROW BEGIN
  CALL incrementer_version();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `support`
--

DROP TABLE IF EXISTS `support`;
CREATE TABLE IF NOT EXISTS `support` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `numero_serie` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `support`
--

INSERT INTO `support` (`id`, `type`, `numero_serie`) VALUES
(1, 'DVD', 'DVD-2025-0001'),
(2, 'Blu-ray', 'BR-2025-0420'),
(3, 'Digital', 'DL-2025-1337');

--
-- Déclencheurs `support`
--
DROP TRIGGER IF EXISTS `after_insert_support`;
DELIMITER $$
CREATE TRIGGER `after_insert_support` AFTER INSERT ON `support` FOR EACH ROW BEGIN
  CALL incrementer_version();
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_update_support`;
DELIMITER $$
CREATE TRIGGER `after_update_support` AFTER UPDATE ON `support` FOR EACH ROW BEGIN
  CALL incrementer_version();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `version`
--

DROP TABLE IF EXISTS `version`;
CREATE TABLE IF NOT EXISTS `version` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_version` INT NOT NULL,
  `date_version` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `version`
--

INSERT INTO `version` (`id`, `numero_version`, `date_version`) VALUES
(1, '1', '2025-05-23');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`film_id`) REFERENCES `films` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `films`
--
ALTER TABLE `films`
  ADD CONSTRAINT `films_ibfk_1` FOREIGN KEY (`support_id`) REFERENCES `support` (`id`),
  ADD CONSTRAINT `films_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`),
  ADD CONSTRAINT `films_ibfk_3` FOREIGN KEY (`realisateur_id`) REFERENCES `realisateurs` (`id`);

--
-- Contraintes pour la table `film_acteur`
--
ALTER TABLE `film_acteur`
  ADD CONSTRAINT `film_acteur_ibfk_1` FOREIGN KEY (`film_id`) REFERENCES `films` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `film_acteur_ibfk_2` FOREIGN KEY (`acteur_id`) REFERENCES `acteurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
