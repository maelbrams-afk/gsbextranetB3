<?php
// Test de la génération du code à 6 chiffres
require_once '../include/fct.inc.php';

for ($i = 0; $i < 5; $i++) {
    var_dump(generateCodeA2f());   // string de 6 chiffres, ex : "004217"
}
