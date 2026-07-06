-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : lun. 06 juil. 2026 à 07:44
-- Version du serveur : 8.0.44
-- Version de PHP : 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bibliotheque`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_categorie` int NOT NULL,
  `nom_categorie` varchar(100) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_categorie`, `nom_categorie`, `description`) VALUES
(1, 'Informatique', 'Programmation, réseaux, IA, bases de données'),
(2, 'Mathématiques', 'Algèbre, analyse, géométrie, statistiques'),
(3, 'Physique', 'Mécanique, électricité, thermodynamique'),
(4, 'Littérature', 'Romans, poésie, théâtre'),
(5, 'Histoire', 'Histoire générale, histoire de l\'art'),
(6, 'Langues', 'Anglais, français, arabe, etc.'),
(7, 'Philosophie', 'Philosophie générale, éthique'),
(8, 'Médecine', 'Anatomie, pharmacie, biologie médicale'),
(9, 'Droit', 'Droit civil, pénal, commercial'),
(10, 'Économie', 'Microéconomie, macroéconomie, gestion');

-- --------------------------------------------------------

--
-- Structure de la table `exemplaires`
--

CREATE TABLE `exemplaires` (
  `id_exemplaire` int NOT NULL,
  `id_livre` int NOT NULL,
  `code_barres` varchar(50) NOT NULL,
  `cote` varchar(50) DEFAULT NULL,
  `emplacement` varchar(100) DEFAULT NULL,
  `statut` enum('disponible','emprunte','reserve','perdu','detérioré') DEFAULT 'disponible',
  `date_acquisition` date DEFAULT NULL,
  `prix_achat` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `exemplaires`
--

INSERT INTO `exemplaires` (`id_exemplaire`, `id_livre`, `code_barres`, `cote`, `emplacement`, `statut`, `date_acquisition`, `prix_achat`) VALUES
(1, 1, 'BU-001', 'INF 004.6', 'Rayon A1, Étagère 1', 'disponible', '2026-07-05', 25000.00),
(2, 1, 'BU-002', 'INF 004.6', 'Rayon A1, Étagère 1', 'disponible', '2026-07-05', 25000.00),
(3, 1, 'BU-003', 'INF 004.6', 'Rayon A1, Étagère 1', 'disponible', '2026-07-05', 25000.00),
(4, 2, 'BU-004', 'LIT 840', 'Rayon B2, Étagère 3', 'disponible', '2026-07-05', 12000.00),
(5, 2, 'BU-005', 'LIT 840', 'Rayon B2, Étagère 3', 'disponible', '2026-07-05', 12000.00),
(6, 4, 'BU-006', 'INF 005.1', 'Rayon A1, Étagère 2', 'disponible', '2026-07-05', 18000.00),
(7, 4, 'BU-007', 'INF 005.1', 'Rayon A1, Étagère 2', 'disponible', '2026-07-05', 18000.00),
(8, 6, 'BU-008', 'LIT 843', 'Rayon B1, Étagère 1', 'disponible', '2026-07-05', 22000.00),
(9, 6, 'BU-009', 'LIT 843', 'Rayon B1, Étagère 1', 'disponible', '2026-07-05', 22000.00),
(10, 7, 'BU-013', 'ART 700', 'Rayon D1, Étagère 1', 'disponible', '2026-07-05', 35000.00);

-- --------------------------------------------------------

--
-- Structure de la table `historique_actions`
--

CREATE TABLE `historique_actions` (
  `id_action` int NOT NULL,
  `id_usager` int DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text,
  `date_action` datetime DEFAULT CURRENT_TIMESTAMP,
  `ip_adresse` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `historique_actions`
--

INSERT INTO `historique_actions` (`id_action`, `id_usager`, `action`, `description`, `date_action`, `ip_adresse`) VALUES
(1, 1, 'CONNEXION', 'Connexion de l\'utilisateur Admin Principal', '2026-07-05 11:02:52', NULL),
(2, 1, 'DECONNEXION', 'Déconnexion de l\'utilisateur Admin Principal', '2026-07-05 11:03:14', NULL),
(3, 3, 'CONNEXION', 'Connexion de l\'utilisateur Dupont Jean', '2026-07-05 11:04:32', NULL),
(4, 3, 'DECONNEXION', 'Déconnexion de l\'utilisateur Dupont Jean', '2026-07-05 11:04:44', NULL),
(5, 2, 'CONNEXION', 'Connexion de l\'utilisateur Bibliothecaire Principal', '2026-07-05 11:05:53', NULL),
(6, 2, 'DECONNEXION', 'Déconnexion de l\'utilisateur Bibliothecaire Principal', '2026-07-05 23:39:37', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `livres`
--

CREATE TABLE `livres` (
  `id_livre` int NOT NULL,
  `isbn` varchar(13) DEFAULT NULL,
  `titre` varchar(255) NOT NULL,
  `auteur` varchar(150) NOT NULL,
  `editeur` varchar(150) DEFAULT NULL,
  `annee_publication` int DEFAULT NULL,
  `edition` varchar(50) DEFAULT NULL,
  `nombre_pages` int DEFAULT NULL,
  `resume` text,
  `mot_cles` text,
  `id_categorie` int DEFAULT NULL,
  `date_ajout` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `livres`
--

INSERT INTO `livres` (`id_livre`, `isbn`, `titre`, `auteur`, `editeur`, `annee_publication`, `edition`, `nombre_pages`, `resume`, `mot_cles`, `id_categorie`, `date_ajout`) VALUES
(1, '978-2-10', 'Algorithmes', 'Thomas Cormen', 'Dunod', 2022, '4ème édition', 1312, 'Livre de référence sur la conception et l\'analyse des algorithmes. Couvre les structures de données, le tri, la recherche, la programmation dynamique et les graphes.', 'algorithmes, programmation, structures de données, informatique, tri, graphes', 1, '2026-07-05'),
(2, '978-2-07', 'Le Petit Prince', 'Antoine de Saint-Exupéry', 'Gallimard', 1943, '1ère édition', 96, 'Le célèbre conte philosophique qui raconte l\'histoire d\'un petit prince venu d\'une autre planète et de sa rencontre avec un aviateur dans le désert du Sahara.', 'conte, philosophie, amitié, enfance, aventure', 4, '2026-07-05'),
(4, '978-3-10', 'Introduction à la Programmation', 'Jean-Pierre Archambault', 'Dunod', 2023, '2ème édition', 456, 'Premiers pas en programmation avec des exercices pratiques et des exemples concrets. Couvre les bases de la syntaxe, les structures de contrôle, les fonctions et les algorithmes simples.', 'programmation, débutant, algorithmes, initiation, code', 1, '2026-07-05'),
(6, '978-2-06', 'Les Misérables', 'Victor Hugo', 'Gallimard', 1862, '1ère édition', 1952, 'Chef-d\'œuvre de la littérature française qui raconte l\'histoire de Jean Valjean, un ancien forçat en quête de rédemption, et de Cosette, dans le Paris du XIXe siècle.', 'roman, justice, rédemption, amour, révolution, France', 4, '2026-07-05'),
(7, '978-2-08', 'Histoire de l\'Art', 'Ernst Gombrich', 'Phaidon', 2019, '16ème édition', 688, 'L\'ouvrage de référence sur l\'histoire de l\'art occidental, des peintures préhistoriques à l\'art contemporain. Accessible à tous les publics.', 'art, peinture, sculpture, architecture, histoire', 5, '2026-07-05');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id_notification` int NOT NULL,
  `id_usager` int NOT NULL,
  `titre` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  `est_lue` tinyint(1) DEFAULT '0',
  `type_notification` enum('rappel','alerte_retard','reservation_disponible','suspension') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `prets`
--

CREATE TABLE `prets` (
  `id_pret` int NOT NULL,
  `id_usager` int NOT NULL,
  `id_exemplaire` int NOT NULL,
  `date_pret` date NOT NULL,
  `date_retour_prevue` date NOT NULL,
  `date_retour_reelle` date DEFAULT NULL,
  `statut` enum('en_cours','termine','retard','relance') DEFAULT 'en_cours',
  `nombre_relances` int DEFAULT '0',
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id_reservation` int NOT NULL,
  `id_usager` int NOT NULL,
  `id_exemplaire` int NOT NULL,
  `date_reservation` date DEFAULT NULL,
  `date_limite_retrait` date NOT NULL,
  `statut` enum('en_attente','notifie','annule','termine') DEFAULT 'en_attente',
  `position_file_attente` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id_role` int NOT NULL,
  `nom_role` varchar(50) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id_role`, `nom_role`, `description`) VALUES
(1, 'administrateur', 'Gestion complète du système'),
(2, 'bibliothecaire', 'Gestion des prêts, retours et usagers'),
(3, 'usager', 'Consultation et réservation uniquement');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_usager` int NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text,
  `date_inscription` date DEFAULT NULL,
  `date_expiration` date DEFAULT NULL,
  `id_role` int DEFAULT '3',
  `statut` enum('actif','suspendu','bloque') DEFAULT 'actif',
  `suspension_jusquau` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_usager`, `matricule`, `nom`, `prenom`, `email`, `mot_de_passe`, `telephone`, `adresse`, `date_inscription`, `date_expiration`, `id_role`, `statut`, `suspension_jusquau`) VALUES
(1, 'ADMIN-001', 'Admin', 'Principal', 'admin@bibliotheque.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0707070707', 'Université', NULL, NULL, 1, 'actif', NULL),
(2, 'BIB-001', 'Bibliothecaire', 'Principal', 'biblio@bibliotheque.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0707070708', 'Université', NULL, NULL, 2, 'actif', NULL),
(3, 'ETU-2024-001', 'Dupont', 'Jean', 'jeandupond@bibliotheque.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0101010101', 'Résidence Universitaire', '2026-07-04', NULL, 3, 'actif', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `exemplaires`
--
ALTER TABLE `exemplaires`
  ADD PRIMARY KEY (`id_exemplaire`),
  ADD UNIQUE KEY `code_barres` (`code_barres`),
  ADD KEY `id_livre` (`id_livre`);

--
-- Index pour la table `historique_actions`
--
ALTER TABLE `historique_actions`
  ADD PRIMARY KEY (`id_action`),
  ADD KEY `id_usager` (`id_usager`);

--
-- Index pour la table `livres`
--
ALTER TABLE `livres`
  ADD PRIMARY KEY (`id_livre`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `id_usager` (`id_usager`);

--
-- Index pour la table `prets`
--
ALTER TABLE `prets`
  ADD PRIMARY KEY (`id_pret`),
  ADD KEY `id_usager` (`id_usager`),
  ADD KEY `id_exemplaire` (`id_exemplaire`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id_reservation`),
  ADD KEY `id_usager` (`id_usager`),
  ADD KEY `id_exemplaire` (`id_exemplaire`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `nom_role` (`nom_role`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_usager`),
  ADD UNIQUE KEY `matricule` (`matricule`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_categorie` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `exemplaires`
--
ALTER TABLE `exemplaires`
  MODIFY `id_exemplaire` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `historique_actions`
--
ALTER TABLE `historique_actions`
  MODIFY `id_action` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `livres`
--
ALTER TABLE `livres`
  MODIFY `id_livre` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id_notification` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `prets`
--
ALTER TABLE `prets`
  MODIFY `id_pret` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id_reservation` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_usager` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `exemplaires`
--
ALTER TABLE `exemplaires`
  ADD CONSTRAINT `exemplaires_ibfk_1` FOREIGN KEY (`id_livre`) REFERENCES `livres` (`id_livre`) ON DELETE CASCADE;

--
-- Contraintes pour la table `historique_actions`
--
ALTER TABLE `historique_actions`
  ADD CONSTRAINT `historique_actions_ibfk_1` FOREIGN KEY (`id_usager`) REFERENCES `utilisateurs` (`id_usager`);

--
-- Contraintes pour la table `livres`
--
ALTER TABLE `livres`
  ADD CONSTRAINT `livres_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id_categorie`);

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`id_usager`) REFERENCES `utilisateurs` (`id_usager`);

--
-- Contraintes pour la table `prets`
--
ALTER TABLE `prets`
  ADD CONSTRAINT `prets_ibfk_1` FOREIGN KEY (`id_usager`) REFERENCES `utilisateurs` (`id_usager`),
  ADD CONSTRAINT `prets_ibfk_2` FOREIGN KEY (`id_exemplaire`) REFERENCES `exemplaires` (`id_exemplaire`);

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`id_usager`) REFERENCES `utilisateurs` (`id_usager`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`id_exemplaire`) REFERENCES `exemplaires` (`id_exemplaire`);

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
