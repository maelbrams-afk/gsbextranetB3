ALTER TABLE `medecin`
MODIFY COLUMN `motDePasse` varchar(255) DEFAULT NULL,
ADD COLUMN `role` enum('medecin','chef_produit','admin') NOT NULL DEFAULT 'medecin';

ALTER TABLE `produit`
MODIFY COLUMN `id` int(11) NOT NULL AUTO_INCREMENT,
ADD COLUMN `actif` tinyint(1) NOT NULL DEFAULT 1;

CREATE TABLE IF NOT EXISTS `consentement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idMedecin` int(11) NOT NULL,
  `dateConsentement` date NOT NULL,
  `idVersion` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idMedecin` (`idMedecin`),
  CONSTRAINT `consentement_ibfk_1` FOREIGN KEY (`idMedecin`) REFERENCES `medecin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `consultation_produit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idMedecin` int(11) DEFAULT NULL,
  `idProduit` int(11) DEFAULT NULL,
  `nomProduit` varchar(60) NOT NULL,
  `dateConsultation` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dateConsultation` (`dateConsultation`),
  KEY `idProduit` (`idProduit`),
  CONSTRAINT `consultation_produit_ibfk_1` FOREIGN KEY (`idMedecin`) REFERENCES `medecin` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `journal_operation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idMedecin` int(11) DEFAULT NULL,
  `compte` varchar(50) NOT NULL,
  `action` varchar(20) NOT NULL,
  `idProduit` int(11) DEFAULT NULL,
  `nomProduit` varchar(60) NOT NULL,
  `adresseIp` varchar(45) NOT NULL,
  `dateOperation` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dateOperation` (`dateOperation`),
  KEY `action` (`action`),
  CONSTRAINT `journal_operation_ibfk_1` FOREIGN KEY (`idMedecin`) REFERENCES `medecin` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `configurationSite` (
  `id` tinyint(1) NOT NULL,
  `maintenance` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `configurationSite` (`id`, `maintenance`)
VALUES (1, 0)
ON DUPLICATE KEY UPDATE `id` = `id`;
