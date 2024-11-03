-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 01 juil. 2024 à 16:07
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
-- Base de données : `dbabsences`
--

-- --------------------------------------------------------

--
-- Structure de la table `absence`
--

CREATE TABLE `absence` (
  `id` int(11) NOT NULL,
  `idStg` varchar(20) DEFAULT NULL,
  `idSeance` int(11) DEFAULT NULL,
  `statut` enum('absent','traité','présent','retard') DEFAULT NULL,
  `raison` enum('justifié','non justifié') DEFAULT NULL,
  `cumul_absences` time DEFAULT '00:00:00',
  `Nom et Prénom` varchar(60) NOT NULL,
  `date_enregistrement` date DEFAULT NULL,
  `heure_enregistrement` time DEFAULT NULL,
  `jour_semaine` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `absence`
--

INSERT INTO `absence` (`id`, `idStg`, `idSeance`, `statut`, `raison`, `cumul_absences`, `Nom et Prénom`, `date_enregistrement`, `heure_enregistrement`, `jour_semaine`) VALUES
(1, '1', 22, 'traité', 'non justifié', '00:00:02', '', NULL, NULL, NULL),
(2, '1', 23, 'traité', 'non justifié', '00:00:02', '', NULL, NULL, NULL),
(3, '12', 19, 'traité', 'non justifié', '00:00:02', '', NULL, NULL, NULL),
(4, '12', 20, 'traité', 'justifié', '00:00:02', '', NULL, NULL, NULL),
(5, '17', 21, 'traité', 'non justifié', '00:00:20', '', NULL, NULL, NULL),
(6, '18', 25, 'traité', 'non justifié', '00:00:02', '', NULL, NULL, NULL),
(8, '35', 28, 'absent', 'justifié', '00:00:02', '', NULL, NULL, NULL),
(9, '32', 27, 'absent', 'non justifié', '00:00:02', '', NULL, NULL, NULL),
(10, '43', 37, 'traité', 'justifié', '00:00:02', '', NULL, NULL, NULL),
(11, '45', 35, 'traité', 'non justifié', '00:00:05', '', NULL, NULL, NULL),
(12, '40', 36, 'traité', 'non justifié', '00:00:02', '', NULL, NULL, NULL),
(14, '42', 34, 'traité', 'non justifié', '00:00:17', '', NULL, NULL, NULL),
(16, '2', 70, 'traité', 'non justifié', '02:30:00', 'AHMIMED ALAE', '2024-06-30', '12:39:02', 'Sunday'),
(17, '4', 70, 'traité', 'justifié', '02:30:00', 'AIT EL HADJ ISMAIL', '2024-06-30', '12:39:02', 'Sunday'),
(19, '6', 70, 'traité', 'non justifié', '02:30:00', 'BAGHOUS ACHRAF', '2024-06-30', '12:39:02', 'Sunday'),
(20, '1', 70, 'présent', NULL, '00:00:00', 'ACHEBAK IMAD', '2024-06-30', '12:50:18', 'Sunday'),
(21, '5', 70, 'absent', NULL, '00:00:00', 'BADDER REDOUAN', '2024-06-30', '12:50:18', 'Sunday'),
(22, '8', 70, 'absent', NULL, '00:00:00', 'BOUIDAN SOUFIAN', '2024-06-30', '13:05:18', 'Sunday'),
(23, '7', 70, 'absent', NULL, '00:00:00', 'BOUALI BILAL', '2024-06-30', '16:44:08', 'Sunday'),
(24, '1', 72, 'absent', NULL, '00:00:00', 'ACHEBAK IMAD', '2024-07-01', '01:59:41', 'Monday'),
(25, '2', 72, 'absent', NULL, '02:30:00', 'AHMIMED ALAE', '2024-07-01', '01:59:41', 'Monday'),
(26, '3', 72, 'absent', NULL, '00:00:00', 'AHMIMED AYA', '2024-07-01', '01:59:41', 'Monday'),
(28, '5', 72, 'absent', NULL, '02:30:00', 'BADDER REDOUAN', '2024-07-01', '01:59:41', 'Monday'),
(29, '6', 72, 'présent', NULL, '00:00:00', 'BAGHOUS ACHRAF', '2024-07-01', '01:59:41', 'Monday'),
(30, '7', 72, 'absent', NULL, '02:30:00', 'BOUALI BILAL', '2024-07-01', '01:59:41', 'Monday'),
(35, '14', 73, 'traité', 'non justifié', '02:30:00', 'EL KAWTIT DAHAB', '2024-07-02', '02:37:28', 'Tuesday'),
(36, '15', 73, 'présent', NULL, '02:30:00', 'EL KHATIB CHAIMAE', '2024-07-02', '02:37:28', 'Tuesday'),
(37, '16', 73, 'présent', NULL, '02:30:00', 'ER-ROUAHE DOUAE', '2024-07-02', '02:37:28', 'Tuesday'),
(38, '17', 74, 'traité', 'non justifié', '02:30:00', 'FADL IMAD', '2024-07-03', '02:47:58', 'Wednesday'),
(39, '18', 74, 'traité', 'non justifié', '02:30:00', 'HRADA ROMAISSAE', '2024-07-03', '02:47:58', 'Wednesday'),
(40, '19', 74, 'traité', 'justifié', '02:30:00', 'KANBOUH ZOUHAIR', '2024-07-03', '02:47:58', 'Wednesday'),
(41, '13', 55, 'absent', NULL, '00:00:00', 'EL KAID ZAID', '2024-07-01', '15:34:01', 'Monday'),
(45, '9', 55, 'absent', NULL, '00:00:00', 'BOULAATOR YASSIR', '2024-07-01', '15:52:20', 'Monday'),
(46, '10', 55, 'présent', NULL, '00:00:00', 'EL ALILTI YASSINE', '2024-07-01', '15:52:20', 'Monday'),
(47, '11', 55, 'présent', NULL, '00:00:00', 'EL AMRANI NADA', '2024-07-01', '15:52:20', 'Monday'),
(48, '12', 55, 'présent', NULL, '00:00:00', 'EL KAID MOHAMED', '2024-07-01', '15:52:20', 'Monday'),
(49, '14', 55, 'absent', NULL, '02:30:00', 'EL KAWTIT DAHAB', '2024-07-01', '15:52:31', 'Monday'),
(50, '16', 55, 'présent', NULL, '02:30:00', 'ER-ROUAHE DOUAE', '2024-07-01', '15:52:31', 'Monday'),
(51, '15', 55, 'présent', NULL, '00:00:00', 'EL KHATIB CHAIMAE', '2024-07-01', '15:59:36', 'Monday');

-- --------------------------------------------------------

--
-- Structure de la table `appartenir`
--

CREATE TABLE `appartenir` (
  `ref_grp` varchar(50) NOT NULL,
  `idForm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `appartenir`
--

INSERT INTO `appartenir` (`ref_grp`, `idForm`) VALUES
('DEV101', 1),
('DEV102', 1),
('DEV103', 1),
('DEV101', 2),
('DEV102', 2),
('DEV103', 2),
('ID101', 2),
('ID102', 2),
('ID103', 2),
('INFO101', 2),
('INFO102', 2),
('ID101', 4),
('ID102', 4),
('ID103', 4),
('ID201', 4),
('ID202', 4),
('ID203', 4),
('DEV101', 3),
('DEV102', 3),
('DEV103', 3),
('DEV201', 3),
('DEV202', 3),
('DEV203', 3);

-- --------------------------------------------------------

--
-- Structure de la table `filiere`
--

CREATE TABLE `filiere` (
  `ref_fil` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `filiere`
--

INSERT INTO `filiere` (`ref_fil`, `libelle`) VALUES
('DEV', 'Developpement Digital'),
('ID', 'Infrastructure Digital'),
('INFO', 'Infographie');

-- --------------------------------------------------------

--
-- Structure de la table `formateur`
--

CREATE TABLE `formateur` (
  `idForm` int(11) NOT NULL,
  `matricule` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `formateur`
--

INSERT INTO `formateur` (`idForm`, `matricule`) VALUES
(2, 'FHB24'),
(1, 'FJL24'),
(4, 'FMH24'),
(3, 'FMN24');

-- --------------------------------------------------------

--
-- Structure de la table `groupe`
--

CREATE TABLE `groupe` (
  `ref_grp` varchar(50) NOT NULL,
  `ref_fil` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `groupe`
--

INSERT INTO `groupe` (`ref_grp`, `ref_fil`) VALUES
('DEV101', 'DEV'),
('DEV102', 'DEV'),
('DEV103', 'DEV'),
('DEV201', 'DEV'),
('DEV202', 'DEV'),
('DEV203', 'DEV'),
('ID101', 'ID'),
('ID102', 'ID'),
('ID103', 'ID'),
('ID201', 'ID'),
('ID202', 'ID'),
('ID203', 'ID'),
('INFO101', 'INFO'),
('INFO102', 'INFO'),
('INFO201', 'INFO'),
('INFO202', 'INFO');

-- --------------------------------------------------------

--
-- Structure de la table `seance`
--

CREATE TABLE `seance` (
  `idSeance` int(11) NOT NULL,
  `grp_Seance` varchar(50) NOT NULL,
  `dateSeance` date DEFAULT NULL,
  `h_debut` time DEFAULT NULL,
  `h_fin` time DEFAULT NULL,
  `idForm` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `seance`
--

INSERT INTO `seance` (`idSeance`, `grp_Seance`, `dateSeance`, `h_debut`, `h_fin`, `idForm`) VALUES
(1, 'DEV101', '2024-06-26', '08:00:00', '10:00:00', 1),
(18, 'DEV102', '2024-06-03', '08:30:00', '11:00:00', 1),
(19, 'DEV102', '2024-06-03', '11:00:00', '13:30:00', 1),
(20, 'DEV102', '2024-06-04', '08:30:00', '11:00:00', 1),
(21, 'DEV101', '2024-06-04', '11:00:00', '13:30:00', 1),
(22, 'DEV103', '2024-06-05', '08:30:00', '11:00:00', 1),
(23, 'DEV103', '2024-06-05', '11:00:00', '13:30:00', 1),
(24, 'DEV103', '2024-06-06', '13:30:00', '16:00:00', 1),
(25, 'DEV101', '2024-06-07', '08:30:00', '11:00:00', 1),
(26, 'DEV101', '2024-06-07', '08:30:00', '11:00:00', 1),
(27, 'INFO101', '2024-06-20', '08:30:00', '11:00:00', 1),
(28, 'INFO102', '2024-06-20', '11:00:00', '13:30:00', 1),
(29, 'INFO101', '2024-06-15', '13:30:00', '16:00:00', 2),
(30, 'INFO101', '2024-06-28', '11:00:00', '13:30:00', 3),
(31, 'INFO102', '2024-06-01', '16:00:00', '18:30:00', 4),
(32, 'INFO102', '2024-06-18', '13:30:00', '16:00:00', 2),
(33, 'ID101', '2024-06-28', '11:00:00', '13:30:00', 4),
(34, 'ID102', '2024-06-01', '16:00:00', '18:30:00', 4),
(35, 'ID103', '2024-06-18', '13:30:00', '16:00:00', 4),
(36, 'ID101', '2024-05-09', '08:30:00', '11:00:00', 3),
(37, 'ID102', '2024-04-17', '13:30:00', '16:00:00', 3),
(38, 'ID103', '2024-05-30', '11:00:00', '13:30:00', 3),
(39, 'ID101', '2024-04-15', '16:00:00', '18:30:00', 2),
(40, 'ID102', '2024-06-07', '08:30:00', '11:00:00', 2),
(41, 'ID103', '2024-04-30', '13:30:00', '16:00:00', 2),
(42, 'DEV103', '2024-06-24', '08:30:00', '11:00:00', 2),
(43, 'ID102', '2024-06-25', '11:00:00', '13:30:00', 2),
(44, 'INFO101', '2024-06-26', '13:30:00', '16:00:00', 2),
(45, 'INFO102', '2024-06-27', '16:00:00', '18:30:00', 2),
(46, 'DEV103', '2024-06-26', '08:30:00', '23:59:00', 1),
(51, 'DEV101', '2024-07-06', '13:30:00', '16:00:00', 1),
(52, 'DEV103', '2024-07-05', '08:30:00', '11:00:00', 1),
(53, 'DEV101', '2024-07-04', '13:30:00', '16:00:00', 1),
(54, 'DEV103', '2024-07-01', '08:30:00', '11:00:00', 1),
(55, 'DEV102', '2024-07-01', '13:30:00', '16:00:00', 1),
(56, 'ID102', '2024-07-01', '16:00:00', '18:30:00', 4),
(62, 'DEV102', '2024-07-01', '08:30:00', '11:00:00', 2),
(63, 'ID103', '2024-07-01', '11:00:00', '13:30:00', 2),
(64, 'DEV103', '2024-07-02', '13:30:00', '16:00:00', 2),
(65, 'DEV103', '2024-07-02', '16:00:00', '18:30:00', 2),
(66, 'INFO101', '2024-07-04', '08:30:00', '11:00:00', 2),
(67, 'INFO101', '2024-07-04', '11:00:00', '13:30:00', 2),
(68, 'DEV102', '2024-07-02', '11:00:00', '13:30:00', 1),
(69, 'DEV102', '2024-07-01', '08:30:00', '11:00:00', 1),
(70, 'DEV103', '2024-06-30', '11:00:00', '23:59:00', 1),
(72, 'DEV103', '2024-07-01', '00:00:00', '23:59:00', 1),
(73, 'DEV102', '2024-07-02', '00:00:00', '23:59:00', 1),
(74, 'DEV101', '2024-07-03', '00:00:00', '23:59:00', 1),
(75, 'DEV201', '2024-07-01', '08:30:00', '11:00:00', 3),
(76, 'DEV203', '2024-07-01', '11:00:00', '13:30:00', 3),
(77, 'DEV202', '2024-07-05', '08:30:00', '11:00:00', 3),
(78, 'DEV103', '2024-07-05', '11:00:00', '13:30:00', 3),
(79, 'DEV103', '2024-07-03', '11:00:00', '13:30:00', 3);

-- --------------------------------------------------------

--
-- Structure de la table `stagiaire`
--

CREATE TABLE `stagiaire` (
  `idStg` varchar(20) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `groupe` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `stagiaire`
--

INSERT INTO `stagiaire` (`idStg`, `nom`, `prenom`, `groupe`) VALUES
('1', 'ACHEBAK', 'IMAD', 'DEV103'),
('10', 'EL ALILTI', 'YASSINE', 'DEV102'),
('11', 'EL AMRANI', 'NADA', 'DEV102'),
('12', 'EL KAID', 'MOHAMED', 'DEV102'),
('13', 'EL KAID', 'ZAID', 'DEV102'),
('14', 'EL KAWTIT', 'DAHAB', 'DEV102'),
('15', 'EL KHATIB', 'CHAIMAE', 'DEV102'),
('16', 'ER-ROUAHE', 'DOUAE', 'DEV102'),
('17', 'FADL', 'IMAD', 'DEV101'),
('18', 'HRADA', 'ROMAISSAE', 'DEV101'),
('19', 'KANBOUH', 'ZOUHAIR', 'DEV101'),
('2', 'AHMIMED', 'ALAE', 'DEV103'),
('20', 'KENIKSSI', 'BAHAE', 'DEV101'),
('21', 'OULAD BEN HAMDI', 'SARA', 'DEV101'),
('22', 'ROUAH', 'AYA', 'DEV101'),
('23', 'SAOUTI', 'MOHAMED', 'DEV101'),
('24', 'TAGHZATI', 'OAMR', 'DEV101'),
('3', 'AHMIMED', 'AYA', 'DEV103'),
('30', 'stg1', 'info1', 'INFO101'),
('31', 'stg2', 'info2', 'INFO101'),
('32', 'stg3', 'info3', 'INFO101'),
('33', 'stg4', 'info4', 'INFO101'),
('34', 'stg5', 'info5', 'INFO102'),
('35', 'stg6', 'info6', 'INFO102'),
('36', 'stg7', 'info7', 'INFO102'),
('37', 'stg8', 'info8', 'INFO102'),
('38', 'stg1', 'id1', 'ID101'),
('39', 'stg2', 'id2', 'ID101'),
('4', 'AIT EL HADJ', 'ISMAIL', 'DEV103'),
('40', 'stg3', 'id3', 'ID101'),
('41', 'stg4', 'id4', 'ID102'),
('42', 'stg5', 'id5', 'ID102'),
('43', 'stg6', 'id6', 'ID102'),
('44', 'stg7', 'id7', 'ID103'),
('45', 'stg8', 'id8', 'ID103'),
('46', 'stg9', 'id9', 'ID103'),
('5', 'BADDER', 'REDOUAN', 'DEV103'),
('6', 'BAGHOUS', 'ACHRAF', 'DEV103'),
('7', 'BOUALI', 'BILAL', 'DEV103'),
('8', 'BOUIDAN', 'SOUFIAN', 'DEV103'),
('9', 'BOULAATOR', 'YASSIR', 'DEV102');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `login` varchar(50) DEFAULT NULL,
  `mot_de_passe` varchar(50) DEFAULT NULL,
  `rôle` enum('directeur','formateur','gestionnaire') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`matricule`, `nom`, `prenom`, `login`, `mot_de_passe`, `rôle`) VALUES
('DYA24', 'AJBAR', 'YOUSSEF', 'ajbaryoussef', '123456', 'directeur'),
('FHB24', 'BABAKHOUYA', 'HIBA', 'babakhouyahiba', '123456', 'formateur'),
('FJL24', 'LAFHAL', 'JOAIRIA', 'lafhaljoairia ', '123456', 'formateur'),
('FMH24', 'HASSANI', 'MUSTAPHA', 'hassanimustapha', '123456', 'formateur'),
('FMN24', 'NIBAOUI', 'MAROUA', 'nibaouimaroua', '123456', 'formateur'),
('GHH24', 'HELOUANI', 'HAJAR', 'helouanihajar', '123456', 'gestionnaire');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `absence`
--
ALTER TABLE `absence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idStg` (`idStg`),
  ADD KEY `idSeance` (`idSeance`);

--
-- Index pour la table `appartenir`
--
ALTER TABLE `appartenir`
  ADD KEY `refer_groupe` (`ref_grp`),
  ADD KEY `idFormat` (`idForm`);

--
-- Index pour la table `filiere`
--
ALTER TABLE `filiere`
  ADD PRIMARY KEY (`ref_fil`);

--
-- Index pour la table `formateur`
--
ALTER TABLE `formateur`
  ADD PRIMARY KEY (`idForm`),
  ADD KEY `matricule` (`matricule`);

--
-- Index pour la table `groupe`
--
ALTER TABLE `groupe`
  ADD PRIMARY KEY (`ref_grp`),
  ADD KEY `ref_fil` (`ref_fil`);

--
-- Index pour la table `seance`
--
ALTER TABLE `seance`
  ADD PRIMARY KEY (`idSeance`),
  ADD KEY `idForm` (`idForm`);

--
-- Index pour la table `stagiaire`
--
ALTER TABLE `stagiaire`
  ADD PRIMARY KEY (`idStg`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`matricule`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `absence`
--
ALTER TABLE `absence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT pour la table `seance`
--
ALTER TABLE `seance`
  MODIFY `idSeance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `absence`
--
ALTER TABLE `absence`
  ADD CONSTRAINT `absence_ibfk_1` FOREIGN KEY (`idStg`) REFERENCES `stagiaire` (`idStg`),
  ADD CONSTRAINT `absence_ibfk_2` FOREIGN KEY (`idSeance`) REFERENCES `seance` (`idSeance`);

--
-- Contraintes pour la table `appartenir`
--
ALTER TABLE `appartenir`
  ADD CONSTRAINT `idFormat` FOREIGN KEY (`idForm`) REFERENCES `formateur` (`idForm`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `refer_groupe` FOREIGN KEY (`ref_grp`) REFERENCES `groupe` (`ref_grp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `formateur`
--
ALTER TABLE `formateur`
  ADD CONSTRAINT `formateur_ibfk_1` FOREIGN KEY (`matricule`) REFERENCES `utilisateur` (`matricule`);

--
-- Contraintes pour la table `groupe`
--
ALTER TABLE `groupe`
  ADD CONSTRAINT `groupe_ibfk_1` FOREIGN KEY (`ref_fil`) REFERENCES `filiere` (`ref_fil`);

--
-- Contraintes pour la table `seance`
--
ALTER TABLE `seance`
  ADD CONSTRAINT `seance_ibfk_1` FOREIGN KEY (`idForm`) REFERENCES `formateur` (`idForm`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
