-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 22 avr. 2026 à 11:12
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
-- Base de données : `gestionbibiotheque`
--

-- --------------------------------------------------------

--
-- Structure de la table `auteur`
--

CREATE TABLE `auteur` (
  `IDAUTEUR` int(11) NOT NULL,
  `NOM` text DEFAULT NULL,
  `NATIONALITE` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ecrire`
--

CREATE TABLE `ecrire` (
  `IDAUTEUR` int(11) NOT NULL,
  `IDLIVRE` int(11) NOT NULL,
  `PERIODE` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `emprunt`
--

CREATE TABLE `emprunt` (
  `IDEMP` int(11) DEFAULT NULL,
  `LIVREEMP` text DEFAULT NULL,
  `DATEDONNEE` date DEFAULT NULL,
  `DATERETOUR` int(11) DEFAULT NULL,
  `QUANTITE` int(11) DEFAULT NULL,
  `NBRFOISEMP` int(11) NOT NULL,
  `IDLIVRE` int(11) NOT NULL,
  `IDLECTEUR` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lecteur`
--

CREATE TABLE `lecteur` (
  `IDLECTEUR` int(11) NOT NULL,
  `NOM` text DEFAULT NULL,
  `ADRESSE` text DEFAULT NULL,
  `TELEPHONE` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livre`
--

CREATE TABLE `livre` (
  `IDLIVRE` int(11) NOT NULL,
  `TITRE` text DEFAULT NULL,
  `DATEPUBLICATION` text DEFAULT NULL,
  `CODEUNIQUE` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `auteur`
--
ALTER TABLE `auteur`
  ADD PRIMARY KEY (`IDAUTEUR`);

--
-- Index pour la table `ecrire`
--
ALTER TABLE `ecrire`
  ADD PRIMARY KEY (`IDAUTEUR`,`IDLIVRE`),
  ADD KEY `FK_ECRIRE` (`IDLIVRE`);

--
-- Index pour la table `emprunt`
--
ALTER TABLE `emprunt`
  ADD PRIMARY KEY (`NBRFOISEMP`),
  ADD KEY `FK_EFFECTUER` (`IDLECTEUR`),
  ADD KEY `FK_EMPRUNTER` (`IDLIVRE`);

--
-- Index pour la table `lecteur`
--
ALTER TABLE `lecteur`
  ADD PRIMARY KEY (`IDLECTEUR`);

--
-- Index pour la table `livre`
--
ALTER TABLE `livre`
  ADD PRIMARY KEY (`IDLIVRE`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `ecrire`
--
ALTER TABLE `ecrire`
  ADD CONSTRAINT `FK_ECRIRE` FOREIGN KEY (`IDLIVRE`) REFERENCES `livre` (`IDLIVRE`),
  ADD CONSTRAINT `FK_ECRIRE2` FOREIGN KEY (`IDAUTEUR`) REFERENCES `auteur` (`IDAUTEUR`);

--
-- Contraintes pour la table `emprunt`
--
ALTER TABLE `emprunt`
  ADD CONSTRAINT `FK_EFFECTUER` FOREIGN KEY (`IDLECTEUR`) REFERENCES `lecteur` (`IDLECTEUR`),
  ADD CONSTRAINT `FK_EMPRUNTER` FOREIGN KEY (`IDLIVRE`) REFERENCES `livre` (`IDLIVRE`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
