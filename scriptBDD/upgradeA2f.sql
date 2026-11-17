-- Sprint 2FA : ajout du code de connexion et de sa date de création
ALTER TABLE `medecin`
  ADD COLUMN `codeA2f` VARCHAR(10) NULL,
  ADD COLUMN `dateCodeA2f` DATETIME NULL;
