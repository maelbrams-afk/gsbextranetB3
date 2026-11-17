<?php
// A LANCER DANS LE NAVIGATEUR : http://localhost/.../tests/testEffaceCodeA2f.php
// Remplacer $id par l'id d'un médecin qui existe dans la table medecin
require_once ("../include/class.pdogsb.inc.php");
$lePdo = PdoGsb::getPdoGsb();
$id = 1;
echo "<pre>";

$lePdo->enregistreCodeA2f($id, '123456');
var_dump($lePdo->effaceCodeA2f($id));              // true : requête exécutée
var_dump($lePdo->verifieCodeA2f($id, '123456'));   // false : le code n'existe plus
// Vérifier dans phpMyAdmin : codeA2f et dateCodeA2f valent NULL
echo "</pre>";
