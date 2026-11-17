<?php

/** 
 * Classe d'accès aux données. 
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoGsb{           
    
private static $serveur = 'mysql:host=localhost;port=3308';
    private static $bdd = 'dbname=gsbextranet;charset=utf8';
    private static $user = 'gsbextranetAdmin';            
    private static $monPdo;
    private static $monPdoGsb = null;
/**
 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
 * pour toutes les méthodes de la classe
 */            
    private function __construct(){
        $mdp = getenv('GSB_DB_PASSWORD');
        if ($mdp === false || $mdp === '') {
            throw new RuntimeException('La variable GSB_DB_PASSWORD doit être configurée.');
        }

        PdoGsb::$monPdo = new PDO(
            PdoGsb::$serveur.';'.PdoGsb::$bdd,
            PdoGsb::$user,
            $mdp,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
    }

    public function __destruct(){
        PdoGsb::$monPdo = null;
    }

/**
 * Fonction statique qui crée l'unique instance de la classe
 * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
 * @return l'unique objet de la classe PdoGsb
 */
    public static function getPdoGsb(){
        if(PdoGsb::$monPdoGsb==null){
            PdoGsb::$monPdoGsb= new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;  
    }

/**
 * vérifie si le login et le mot de passe sont corrects
 * renvoie true si les 2 sont corrects
 * @param type $lePDO
 * @param type $login
 * @param type $pwd
 * @return bool
 * @throws Exception
 */
    public function checkUser($login, $pwd): bool {
    $user = false;
    $pdo = PdoGsb::$monPdo;
    $monObjPdoStatement = $pdo->prepare("SELECT motDePasse FROM medecin WHERE mail = :login AND token IS NULL");
    $monObjPdoStatement->bindValue(':login', $login, PDO::PARAM_STR);

    $monObjPdoStatement->execute();
    $unUser = $monObjPdoStatement->fetch(PDO::FETCH_ASSOC);
    if (is_array($unUser) && password_verify($pwd, $unUser['motDePasse'])) {
        $user = true;
    }
    return $user;
    }

    public function donneLeMedecinByMail($login) {
    $pdo = PdoGsb::$monPdo;
    $monObjPdoStatement = $pdo->prepare(
        "SELECT id, nom, prenom, mail, role FROM medecin WHERE mail = :login"
    );
    $monObjPdoStatement->bindValue(':login', $login, PDO::PARAM_STR);
    $monObjPdoStatement->execute();
    return $monObjPdoStatement->fetch(PDO::FETCH_ASSOC);
    }

    public function tailleChampsMail() {
        $pdoStatement = PdoGsb::$monPdo->prepare(
            "SELECT CHARACTER_MAXIMUM_LENGTH
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_name='medecin'
            AND COLUMN_NAME='mail'"
        );
        $pdoStatement->execute();
        return $pdoStatement->fetchColumn();
    }

    public function ajouterConsentement($idMedecin, $idVersion = 1) {
        $pdoStatement = PdoGsb::$monPdo->prepare(
            "INSERT INTO consentement
            (idMedecin, dateConsentement, idVersion)
            VALUES (:idMedecin, DATE(NOW()), :idVersion)"
        );
        $pdoStatement->bindValue(':idMedecin', $idMedecin, PDO::PARAM_INT);
        $pdoStatement->bindValue(':idVersion', $idVersion, PDO::PARAM_INT);
        return $pdoStatement->execute();
    }

    public function creeMedecin($email, $mdp) {
        $pdoStatement = PdoGsb::$monPdo->prepare(
            "INSERT INTO medecin
            (id, mail, motDePasse, dateCreation, dateConsentement)
            VALUES
            (NULL, :leMail, :leMdp, DATE(NOW()), DATE(NOW()))"
        );
        $pdoStatement->bindValue(':leMail', $email);
        $pdoStatement->bindValue(':leMdp', $mdp);
        $execution = $pdoStatement->execute();
        if ($execution) {
            $idMedecin = PdoGsb::$monPdo->lastInsertId();
            $this->ajouterConsentement($idMedecin, 1);
        }
        return $execution;
    }

    public function connexionInitiale($mail) {
        $pdo = PdoGsb::$monPdo;
        $medecin= $this->donneLeMedecinByMail($mail);
        $id = $medecin['id'];
        return $this->ajouteConnexionInitiale($id);
    }

    public function ajouteConnexionInitiale($id) {
        $pdoStatement = PdoGsb::$monPdo->prepare("INSERT INTO historiqueconnexion "
            . "VALUES (:leMedecin, now(), now())");
        $bv1 = $pdoStatement->bindValue(':leMedecin', $id);
        $execution = $pdoStatement->execute();
        return $execution;
    }

    public function donneinfosmedecin($id) {
        $pdo = PdoGsb::$monPdo;
        $monObjPdoStatement=$pdo->prepare("SELECT id,nom,prenom FROM medecin WHERE id= :lId");
        $bvc1=$monObjPdoStatement->bindValue(':lId',$id,PDO::PARAM_INT);
        if ($monObjPdoStatement->execute()) {
            $unUser=$monObjPdoStatement->fetch();
            return $unUser;
        }
        else {
            throw new Exception("erreur");
        }
    }

    public function listeProduits() {
        $statement = PdoGsb::$monPdo->query(
            "SELECT id, nom, objectif, information, effetIndesirable
            FROM produit
            WHERE actif = 1
            ORDER BY nom"
        );
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function donneProduit($idProduit) {
        $statement = PdoGsb::$monPdo->prepare(
            "SELECT id, nom, objectif, information, effetIndesirable
            FROM produit
            WHERE id = :id AND actif = 1"
        );
        $statement->bindValue(':id', $idProduit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function ajouterProduit($nom, $objectif, $information, $effetIndesirable) {
        $statement = PdoGsb::$monPdo->prepare(
            "INSERT INTO produit (nom, objectif, information, effetIndesirable)
            VALUES (:nom, :objectif, :information, :effetIndesirable)"
        );
        $statement->execute(array(
            ':nom' => $nom,
            ':objectif' => $objectif,
            ':information' => $information,
            ':effetIndesirable' => $effetIndesirable
        ));
        return PdoGsb::$monPdo->lastInsertId();
    }

    public function modifierProduit($idProduit, $nom, $objectif, $information, $effetIndesirable) {
        $statement = PdoGsb::$monPdo->prepare(
            "UPDATE produit
            SET nom = :nom, objectif = :objectif, information = :information,
                effetIndesirable = :effetIndesirable
            WHERE id = :id"
        );
        return $statement->execute(array(
            ':id' => $idProduit,
            ':nom' => $nom,
            ':objectif' => $objectif,
            ':information' => $information,
            ':effetIndesirable' => $effetIndesirable
        ));
    }

    public function supprimerProduit($idProduit) {
        $statement = PdoGsb::$monPdo->prepare("UPDATE produit SET actif = 0 WHERE id = :id");
        return $statement->execute(array(':id' => $idProduit));
    }

    public function enregistrerConsultationProduit($idMedecin, $produit) {
        $statement = PdoGsb::$monPdo->prepare(
            "INSERT INTO consultation_produit
            (idMedecin, idProduit, nomProduit, dateConsultation)
            VALUES (:idMedecin, :idProduit, :nomProduit, NOW())"
        );
        return $statement->execute(array(
            ':idMedecin' => $idMedecin,
            ':idProduit' => $produit['id'],
            ':nomProduit' => $produit['nom']
        ));
    }

    public function enregistrerOperationProduit($idMedecin, $action, $idProduit, $nomProduit, $adresseIp) {
        $statement = PdoGsb::$monPdo->prepare(
            "INSERT INTO journal_operation
            (idMedecin, compte, action, idProduit, nomProduit, adresseIp, dateOperation)
            SELECT id, mail, :action, :idProduit, :nomProduit, :adresseIp, NOW()
            FROM medecin
            WHERE id = :idMedecin"
        );
        return $statement->execute(array(
            ':idMedecin' => $idMedecin,
            ':action' => $action,
            ':idProduit' => $idProduit,
            ':nomProduit' => $nomProduit,
            ':adresseIp' => $adresseIp
        ));
    }

    public function ajouterProduitEtTracer($nom, $objectif, $information, $effetIndesirable, $idMedecin, $adresseIp) {
        $pdo = PdoGsb::$monPdo;
        $pdo->beginTransaction();
        try {
            $idProduit = $this->ajouterProduit($nom, $objectif, $information, $effetIndesirable);
            $this->enregistrerOperationProduit($idMedecin, 'creation', $idProduit, $nom, $adresseIp);
            $pdo->commit();
            return $idProduit;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function modifierProduitEtTracer($idProduit, $nom, $objectif, $information, $effetIndesirable, $idMedecin, $adresseIp) {
        $pdo = PdoGsb::$monPdo;
        $pdo->beginTransaction();
        try {
            $this->modifierProduit($idProduit, $nom, $objectif, $information, $effetIndesirable);
            $this->enregistrerOperationProduit($idMedecin, 'modification', $idProduit, $nom, $adresseIp);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function supprimerProduitEtTracer($idProduit, $nomProduit, $idMedecin, $adresseIp) {
        $pdo = PdoGsb::$monPdo;
        $pdo->beginTransaction();
        try {
            $this->supprimerProduit($idProduit);
            $this->enregistrerOperationProduit($idMedecin, 'suppression', $idProduit, $nomProduit, $adresseIp);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function donneStatistiquesProduits() {
        $totaux = PdoGsb::$monPdo->query(
            "SELECT COUNT(*) AS nombreConsultations,
                COUNT(DISTINCT idMedecin) AS nombreMedecins
            FROM consultation_produit"
        )->fetch(PDO::FETCH_ASSOC);

        $produits = PdoGsb::$monPdo->query(
            "SELECT nomProduit, COUNT(*) AS nombreConsultations
            FROM consultation_produit
            GROUP BY idProduit, nomProduit
            ORDER BY nombreConsultations DESC, nomProduit
            LIMIT 10"
        )->fetchAll(PDO::FETCH_ASSOC);

        return array(
            'totaux' => $totaux,
            'produits' => $produits
        );
    }

    public function donneOperationsProduits() {
        $statement = PdoGsb::$monPdo->query(
            "SELECT compte, action, nomProduit, adresseIp, dateOperation
            FROM journal_operation
            ORDER BY dateOperation DESC
            LIMIT 100"
        );
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function estEnMaintenance() {
        $statement = PdoGsb::$monPdo->query(
            "SELECT maintenance FROM configurationSite WHERE id = 1"
        );
        return (bool)$statement->fetchColumn();
    }

    public function changerMaintenance($actif) {
        $statement = PdoGsb::$monPdo->prepare(
            "UPDATE configurationSite SET maintenance = :maintenance WHERE id = 1"
        );
        return $statement->execute(array(':maintenance' => $actif ? 1 : 0));
    }

    /**
     * 2FA - Enregistre un code de connexion pour un médecin,
     * avec la date et l'heure de sa création (début de la minute de validité)
     * @param int $idMedecin
     * @param string $code le code à 6 chiffres
     * @return bool
     */
    public function enregistreCodeA2f($idMedecin, $code) {
        $st = PdoGsb::$monPdo->prepare(
            "UPDATE medecin SET codeA2f = :code, dateCodeA2f = NOW() WHERE id = :id"
        );
        $st->bindValue(':code', $code, PDO::PARAM_STR);
        $st->bindValue(':id', $idMedecin, PDO::PARAM_INT);
        return $st->execute();
    }

    /**
     * 2FA - Vérifie que le code saisi existe pour ce médecin
     * ET qu'il a été créé il y a moins d'une minute
     * @param int $idMedecin
     * @param string $code le code saisi par le médecin
     * @return bool vrai si le code est correct et encore valable
     */
    public function verifieCodeA2f($idMedecin, $code) {
        $verif = PdoGsb::$monPdo->prepare(
            "SELECT COUNT(*) FROM medecin
             WHERE id = :id AND codeA2f = :code
             AND dateCodeA2f >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)"
        );
        $verif->bindValue(':id', $idMedecin, PDO::PARAM_INT);
        $verif->bindValue(':code', $code, PDO::PARAM_STR);
        $verif->execute();
        return $verif->fetchColumn() > 0;
    }

    /**
     * 2FA - Remet à NULL le code et sa date (code à usage unique)
     * @param int $idMedecin
     * @return bool
     */
    public function effaceCodeA2f($idMedecin) {
        $st = PdoGsb::$monPdo->prepare(
            "UPDATE medecin SET codeA2f = NULL, dateCodeA2f = NULL WHERE id = :id"
        );
        $st->bindValue(':id', $idMedecin, PDO::PARAM_INT);
        return $st->execute();
    }
}
?>