<?php
// Test des 3 fonctions DAO de la 2FA
// A LANCER DANS LE NAVIGATEUR : http://localhost/.../tests/testCodeA2f.php
// Remplacer $id par l'id d'un médecin qui existe dans la table medecin
require_once ("../include/class.pdogsb.inc.php");
$lePdo = PdoGsb::getPdoGsb();
$id = 1;

echo "<pre>";
var_dump($lePdo->enregistreCodeA2f($id, '123456'));   // true  : code enregistré
var_dump($lePdo->verifieCodeA2f($id, '123456'));      // true  : bon code, moins d'1 minute
var_dump($lePdo->verifieCodeA2f($id, '000000'));      // false : mauvais code

$lePdo->effaceCodeA2f($id);
var_dump($lePdo->verifieCodeA2f($id, '123456'));      // false : code effacé

// Test de l'expiration (la page met 61 secondes à s'afficher)
$lePdo->enregistreCodeA2f($id, '654321');
sleep(61);
var_dump($lePdo->verifieCodeA2f($id, '654321'));      // false : expiré

$lePdo->effaceCodeA2f($id);
echo "</pre>";
