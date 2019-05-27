
-- Version du serveur :  5.7.19
-- Version de PHP :  7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `gsb_frais` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `gsb_frais`;

-- --------------------------------------------------------

--
-- Structure de la table `etat`
--


CREATE TABLE IF NOT EXISTS `etat` (
  `id` char(2) NOT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `etat`
--

INSERT INTO `etat` (`id`, `libelle`) VALUES
('CL', 'Saisie clôturée'),
('CR', 'Fiche créée, saisie en cours'),
('PM', 'Mise en paiement'),
('RB', 'Remboursée'),
('VA', 'Validée');

-- --------------------------------------------------------

--
-- Structure de la table `fichefrais`
--


CREATE TABLE IF NOT EXISTS `fichefrais` (
  `iduser` int(11) NOT NULL,
  `mois` char(6) NOT NULL,
  `nbjustificatifs` int(11) DEFAULT NULL,
  `montantvalide` decimal(10,2) DEFAULT NULL,
  `datemodif` date DEFAULT NULL,
  `idetat` char(2) DEFAULT 'CR',
  `idcomptable` int(11) DEFAULT NULL,
  PRIMARY KEY (`iduser`,`mois`),
  KEY `idetat` (`idetat`),
  KEY `fk_idcomptable_user` (`idcomptable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `fichefrais`
--

INSERT INTO `fichefrais` (`iduser`, `mois`, `nbjustificatifs`, `montantvalide`, `datemodif`, `idetat`, `idcomptable`) VALUES
('1', '201802',' 6', '3574.36', '2018-03-08', 'PM', NULL),
('1', '201803', '0', '0.00', '2018-03-07', 'CR', NULL),
('3', '201802', '3', '1968.80', '2018-03-07', 'VA', NULL),
('3', '201803', '0', '0.00', '2018-03-05', 'CR', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `fraisforfait`
--


CREATE TABLE IF NOT EXISTS `fraisforfait` (
  `id` char(3) NOT NULL,
  `description` varchar(30) DEFAULT NULL,
  `montant` decimal(5,2) DEFAULT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `fraisforfait`
--

INSERT INTO `fraisforfait` (`id`, `description`, `montant`, `libelle`) VALUES
('ETP', 'Forfait Etape', '110.00', 'Etape'),
('KM', 'Frais Kilométrique', '0.62', 'KM Parcourus'),
('NUI', 'Nuitée Hôtel', '80.00', 'Nombre de nuit(s) à l\hotel'),
('REP', 'Repas Restaurant', '25.00', 'Nombre de repas au restaurant');






CREATE TABLE IF NOT EXISTS `lignefraisforfait` (
  `iduser` int(11) NOT NULL,
  `mois` char(6) NOT NULL,
  `idfraisforfait` char(3) NOT NULL,
  `quantite` int(11) DEFAULT NULL,
  PRIMARY KEY (`iduser`,`mois`,`idfraisforfait`),
  KEY `idfraisforfait` (`idfraisforfait`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lignefraisforfait`
--

INSERT INTO `lignefraisforfait` (`iduser`, `mois`, `idfraisforfait`, `quantite`) VALUES
('1', '201802', 'ETP', '15'),
('1', '201802', 'KM', '78'),
('1', '201802', 'NUI', '13'),
('1', '201802', 'REP', '22'),
('1', '201803', 'ETP', '9'),
('1', '201803', 'KM', '542'),
('1', '201803', 'NUI', '9'),
('1', '201803', 'REP', '13'),
('3', '201802', 'ETP', '8'),
('3', '201802', 'KM', '240'),
('3', '201802', 'NUI', '8'),
('3', '201802', 'REP', '12'),
('3', '201803', 'ETP', '5'),
('3', '201803', 'KM', '254'),
('3', '201803', 'NUI', '4'),
('3', '201803', 'REP', '7');

-- --------------------------------------------------------

--
-- Structure de la table `lignefraishorsforfait`
--


CREATE TABLE IF NOT EXISTS `lignefraishorsforfait` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `mois` char(6) NOT NULL,
  `libelle` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `refus` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `lignefraishorsforfait_ibfk_1` (`iduser`,`mois`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lignefraishorsforfait`
--

INSERT INTO `lignefraishorsforfait` (`id`, `iduser`, `mois`, `libelle`, `date`, `montant`, `refus`) VALUES
('3', '1', '201802', 'Hotel 5 étoiles', '2018-02-18', '286.00', 0),
('13', '3', '201803', 'Achat bouquet de fleurs', '2018-02-16', '42.00', 0);

-- --------------------------------------------------------

--
-- Structure de la table `pageuser`
--


CREATE TABLE IF NOT EXISTS `pageuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uc` varchar(40) DEFAULT NULL,
  `idtypeuser` int(11) DEFAULT NULL,
  `libelle` varchar(40) DEFAULT NULL,
  `icon` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idtypeuser` (`idtypeuser`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `pageuser`
--

INSERT INTO `pageuser` (`id`, `uc`, `idtypeuser`, `libelle`, `icon`) VALUES
('1', 'gererFrais', '1', 'Renseigner la fiche de frais', 'pencil'),
('2', 'etatFrais', '1', 'Afficher mes fiches de frais', 'list-alt'),
('3', 'validerFicheFrais', '2', 'Validation des fiches de frais', 'pencil'),
('4', 'suivreFicheFrais', '2', 'Suivi du paiement des fiches de frais', 'list-alt');

-- --------------------------------------------------------

--
-- Structure de la table `typeuser`
--


CREATE TABLE IF NOT EXISTS `typeuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `typeuser`
--

INSERT INTO `typeuser` (`id`, `libelle`) VALUES
('1', 'visiteur'),
('2', 'comptable');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--


CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) DEFAULT NULL,
  `prenom` varchar(30) DEFAULT NULL,
  `login` varchar(20) DEFAULT NULL,
  `mdp` varchar(128) DEFAULT NULL,
  `adresse` varchar(30) DEFAULT NULL,
  `cp` varchar(8) DEFAULT NULL,
  `ville` varchar(30) DEFAULT NULL,
  `dateembauche` date DEFAULT NULL,
  `idtypeuser` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  KEY `idtypeuser` (`idtypeuser`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `nom`, `prenom`, `login`, `mdp`, `adresse`, `cp`, `ville`, `dateembauche`, `idtypeuser`) VALUES
(1, 'Tartampion', 'Lulu', 'visiteur', '340cd2c5e0eb66683f07921833736fc7abf061df6fcc338ca3fe6e8ea99b2feef4c750e3fa0efbe437dcce9a6d82eaff32c084fcec3bc75066a40a4e1a0a430f', '8 rue des Charmes', '46000', 'Cahors', '2005-12-21', 1),
(2, 'Cosette', 'Gertrude', 'comptable', '7863bd8ec731dec906a030ead5826a9c9c59f2d79baf740069ba0208f765745651abf0182f331072cf25a878a5343f76ba2a14c39036076df551f79dc4a8b0a7', '8 rue des Charmes', '46000', 'Cahors', '2005-12-21', 2),
(3, 'Villechalane', 'Louis', 'lvillachane', '13dbdf7d3600e727d522707cb533f868367be729cdfc4032e6d06b8e6b19f911f63cf661fe56ad11169092488d210438e816f4f79078c295718df6262b290cd8', '8 rue des Charmes', '46000', 'Cahors', '2005-12-21', 1),
(4, 'Andre', 'David', 'dandre', '93c3d3d7ca890c6f3fd3fe5e270e9b0cde3aa69523208976b2e95fa382d7f43a4cf97a2a7bd10e88214065b27159cc0a0f97fedf53d800a5b9e276e28e17b4c2', '1 rue Petit', '46200', 'Lalbenque', '1998-11-23', 1),
(5, 'Bedos', 'Christian', 'cbedos', '0ef320534cc5e92a107325f6a3150cf641aaeb0b1e7b17ac3f89f1ef5c5238028b85b5cb3a2bb48e34d035735c2157a81b3eed31c7ef2e61f228c461975d700a', '1 rue Peranud', '46250', 'Montcuq', '1995-01-12', 1),
(6, 'Tusseau', 'Louis', 'ltusseau', 'd3e4c8eb9180f86ef1627d46a8ec5a975caf70aa6135fb196a1a2aa56ebc246b42075fca83b61357afbc92f163bb0b55da6c2e9d59e19061fe59c6c4166d0f8d', '22 rue des Ternes', '46123', 'Gramat', '2000-05-01', 1),
(7, 'Bentot', 'Pascal', 'pbentot', '534a091cdb6432e844ef547bc1d87b5a99d5ab11a2a701e1db9ff80b4473869efb486c4f40caf350617d3c917c744df42e169454b04e1329e52126da71c7a6ed', '11 allée des Cerises', '46512', 'Bessines', '1992-07-09', 1),
(8, 'Bioret', 'Luc', 'lbioret', '6ca809e76a3830026782b6b0de6fdc8c4f24990a2cd95c5bbfe189256f7db3650d8dec40a846cfbe157cc9a228d15b5ba1a0b5cbd43850a9f47b1a9e0c0f2a06', '1 Avenue gambetta', '46000', 'Cahors', '1998-05-11', 1),
(9, 'Bunisset', 'Francis', 'fbunisset', '1f3eab5ab9fc9945bf8e0e80ca93975f5f5d985257a24455290eaf70182cb8ce9fdd5e686e253d40fc31acbef27b7dc47c40f32456de8549ac642b0bee660ef5', '10 rue des Perles', '93100', 'Montreuil', '1987-10-21', 1),
(10, 'Bunisset', 'Denise', 'dbunisset', '68ee1cf8a33c8f052ad57cafa14026b22e6c3d9dc1394782efaea901af7dcae44d8bc8f144ff8339713099c11953e7da087619a5e43d278d84c03b46d589f9c8', '23 rue Manin', '75019', 'paris', '2010-12-05', 1),
(11, 'Cacheux', 'Bernard', 'bcacheux', 'c27bce94a964281439ea6a17ab46ce6e0670da3877c93df0cd5e14ebe0aef3bce66c3bea7257f3b27d034e398a7089888e3b255f4a49caaccff980879ad4c1fd', '114 rue Blanche', '75017', 'Paris', '2009-11-12', 1),
(12, 'Cadic', 'Eric', 'ecadic', 'e459545a65f978f12eaff36ad96f475ea9e2d497ab1c29a4fdccc8c889b996bfd20c91c9c7296f4b0532feabae0fd25068fa253fad2fd8e03ab9b5b637a2edef', '123 avenue de la République', '75011', 'Paris', '2008-09-23', 1),
(13, 'Charoze', 'Catherine', 'ccharoze', '9fce860fede4fb4441e65ae1957675bea6d00f74533f10fd47e493dddaa2da2ab29ef409538a047459e9997c378b7d2694dda5d5d68791ef074f8f66b337ed9b', '100 rue Petit', '75019', 'Paris', '2005-11-12', 1),
(14, 'Clepkens', 'Christophe', 'cclepkens', 'b8864461865eaab5b66ee5ad083a8a4620beed4e20cfd7e4ee76bc10677610893fbfe64c63f48af01fb5bb2f6d0690a70efef72879a9a0192310c60e48f09897', '12 allée des Anges', '93230', 'Romainville', '2003-08-11', 1),
(15, 'Cottin', 'Vincenne', 'vcottin', '6e5d9d888f31ee2e4c90e89ca1f54420ce2e7b7c4d9a08174f697e415bf69fdea5d9ab59c9652e912867991451fe800f93d66c5ee009ae65b664c385cb8d7bcd', '36 rue Des Roches', '93100', 'Monteuil', '2001-11-18', 1),
(16, 'Daburon', 'François', 'fdaburon', '6352987726652d06b4cebc40ced3eead997a1adaffee47dce53c2df01970ed900824ee05f72a6b9e314e053f7cd1e7763ebe962b586641d059ac95a32ff175a7', '13 rue de Chanzy', '94000', 'Créteil', '2002-02-11', 1),
(17, 'De', 'Philippe', 'pde', '62d8e965a3cceb4ded0f95b1158ded7b6fa3ec7c654f1ae322c8bea78f7ccd46b524f46c1f61242b3fd12bb7fdc5959a8ed194feec2ac1a2fbb809e74bb45f3a', '13 rue Barthes', '94000', 'Créteil', '2010-12-14', 2),
(18, 'Debelle', 'Michel', 'mdebelle', 'f73a98ab9a0561e7d898969e93f0f27e77fb34e91821b21227f3aad8e01278efe07d5f32ebd4bc5601277ea8c6b22deb27009b59e11306523eb794ffa2981aa7', '181 avenue Barbusse', '93210', 'Rosny', '2006-11-23', 2),
(19, 'Debelle', 'Jeanne', 'jdebelle', '8bba249274c8439780fd90f436ca62100979116e598663c24642d0a371ab71970032c3fa0b9da1573db80f867fd00c7b463b16c64d9819f022b656041aaf7fd6', '134 allée des Joncs', '44000', 'Nantes', '2000-05-11', 2),
(20, 'Debroise', 'Michel', 'mdebroise', 'dfe7b593ddb72898a0bbf326ef249efbfb1e278a04716d07f1e5baa23732a55313c5169e2c0a760c981d56cb87e65855beeb8d595704f4886612dc86e55edb11', '2 Bld Jourdain', '44000', 'Nantes', '2001-04-17', 2),
(21, 'Desmarquest', 'Nathalie', 'ndesmarquest', 'c95dd9f05de37f54df7f202a0c0f5a39552eb72661b7da84b9a3e782216ae1d1788188e5ca83dcc1d16b743588812fccc3d69e6a9adfcd7836a6b9c907770cd2', '14 Place d Arc', '45000', 'Orléans', '2005-11-12', 2),
(22, 'Desnost', 'Pierre', 'pdesnost', '3d078e614b8ea8c6e22de1fa797a1a44cf697687e45e27d81f24a15a7a782db4965f315b37f066a1a6a405930e2ff8363ba9efa7884f727784144df0b34f958f', '16 avenue des Cèdres', '23200', 'Guéret', '2001-02-05', 2),
(23, 'Dudouit', 'Frédéric', 'fdudouit', '707736ce79cbfaab003254677f4fae59c6e83df5ea81851d503b622b6a9ed0fb79d13d6ea3136a8de5325734798b8c216030ddf1c0a2268abf2da3fa6bb752ed', '18 rue de l église', '23120', 'GrandBourg', '2000-08-01', 2),
(24, 'Duncombe', 'Claude', 'cduncombe', '4d62f09c266b7c81539a658af42daebeaf3701d9a8922c982d3711e97de2b897a988488726001cb5772d3b5e42b93f03882ef0e8d0a5a18cc200a5559bac369e', '19 rue de la tour', '23100', 'La souteraine', '1987-10-10', 2),
(25, 'Enault-Pascreau', 'Céline', 'cenault', 'a309a05d7051be5dab6933292007ec6c9012a16682fe30a1c1b03726ba1811cc9b8f63651315d073eff66cfad821bb4d1c56c89597e8058f2238ee7204a3f902', '25 place de la gare', '23200', 'Gueret', '1995-09-01', 2),
(26, 'Eynde', 'Valérie', 'veynde', 'c58437bafd323a6da6de8780bc9fc088c457231062fca2b1b4333eec07981166f9ea9b3d09f4e5fa6f8d6354efe85b39a07f86fb153721f5807adbadd9958ec2', '3 Grand Place', '13015', 'Marseille', '1999-11-01', 1),
(27, 'Finck', 'Jacques', 'jfinck', '9edf00a05e0aeaf318ea0dcc14d631056a88d9c2de3420b6ea439395ddc24057640390c5135a9a31964f0b6c5eb54ee89869df2af8a6bd16392ba7a1edef39a3', '10 avenue du Prado', '13002', 'Marseille', '2001-11-10', 1),
(28, 'Frémont', 'Fernande', 'ffremont', '32061e7c2ef0dde665e7b6a70722a1a93225a33ac4928000e0e940198c823859332bc906494065745e5216b387bd0224ae746248d7a5b8a284ced8df85e8f84f', '4 route de la mer', '13012', 'Allauh', '1998-10-01', 1),
(29, 'Gest', 'Alain', 'agest', '889fe2177c79378eaa9b57f8a62a6e5afc95b10178598bcb3bbfd39aedaa2d49007ee2165d29486d9e5b8443e1b1c8d884982a630a68e0d4f081b86c8e4b2bcf', '30 avenue de la mer', '13025', 'Berre', '1985-11-01', 1);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `fichefrais`
--
ALTER TABLE `fichefrais`
  ADD CONSTRAINT `fichefrais_ibfk_1` FOREIGN KEY (`idetat`) REFERENCES `etat` (`id`),
  ADD CONSTRAINT `fichefrais_ibfk_2` FOREIGN KEY (`iduser`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `fk_idcomptable_user` FOREIGN KEY (`idcomptable`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `lignefraisforfait`
--
ALTER TABLE `lignefraisforfait`
  ADD CONSTRAINT `lignefraisforfait_ibfk_1` FOREIGN KEY (`iduser`,`mois`) REFERENCES `fichefrais` (`iduser`, `mois`) ON DELETE CASCADE,
  ADD CONSTRAINT `lignefraisforfait_ibfk_2` FOREIGN KEY (`idfraisforfait`) REFERENCES `fraisforfait` (`id`);

--
-- Contraintes pour la table `lignefraishorsforfait`
--
ALTER TABLE `lignefraishorsforfait`
  ADD CONSTRAINT `lignefraishorsforfait_ibfk_1` FOREIGN KEY (`iduser`,`mois`) REFERENCES `fichefrais` (`iduser`, `mois`) ON DELETE CASCADE;

--
-- Contraintes pour la table `pageuser`
--
ALTER TABLE `pageuser`
  ADD CONSTRAINT `pageuser_ibfk_1` FOREIGN KEY (`idtypeuser`) REFERENCES `typeuser` (`id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`idtypeuser`) REFERENCES `typeuser` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
