<?php
// A LANCER DANS LE NAVIGATEUR : http://localhost/.../tests/testEnregistreCodeA2f.php
// Remplacer $id par l'id d'un médecin qui existe dans la table medecin
require_once ("../include/class.pdogsb.inc.php");
$lePdo = PdoGsb::getPdoGsb();
$id = 1;
echo "<pre>";

// Enregistre un code avec la date et l'heure
var_dump($lePdo->enregistreCodeA2f($id, '123456'));   // true : requête exécutée
// Vérifier ensuite dans phpMyAdmin : codeA2f = 123456 et dateCodeA2f = maintenant

$lePdo->effaceCodeA2f($id);   // on nettoie
echo "</pre>";
