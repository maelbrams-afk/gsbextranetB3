<?php
// A LANCER DANS LE NAVIGATEUR : http://localhost/.../tests/testVerifieCodeA2f.php
// Remplacer $id par l'id d'un médecin qui existe dans la table medecin
require_once ("../include/class.pdogsb.inc.php");
$lePdo = PdoGsb::getPdoGsb();
$id = 1;
echo "<pre>";

$lePdo->enregistreCodeA2f($id, '123456');
var_dump($lePdo->verifieCodeA2f($id, '123456'));   // true  : bon code, moins d'1 minute
var_dump($lePdo->verifieCodeA2f($id, '000000'));   // false : mauvais code

// Test de l'expiration (la page met 61 secondes à s'afficher)
sleep(61);
var_dump($lePdo->verifieCodeA2f($id, '123456'));   // false : code expiré

$lePdo->effaceCodeA2f($id);
var_dump($lePdo->verifieCodeA2f($id, '123456'));   // false : code effacé
echo "</pre>";
