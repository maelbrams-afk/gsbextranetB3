<?php
session_start();
require_once("include/fct.inc.php");
require_once("include/class.pdogsb.inc.php");

date_default_timezone_set('Europe/Paris');

$pdo = PdoGsb::getPdoGsb();
$estConnecte = estConnecte();

if (!isset($_GET['uc'])) {
    $_GET['uc'] = 'connexion';
} else {
    if ($_GET['uc'] == "connexion" && !estConnecte()) {
        $_GET['uc'] = 'connexion';
    }
}
$uc = $_GET['uc'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>GSB - Extranet</title>
</head>
<body>

<main>
<?php
switch ($uc) {
    case 'connexion':
        include("controleurs/c_connexion.php");
        break;
    case 'creation':
        include("controleurs/c_creation.php");
        break;
    case 'consentement':
        include("controleurs/c_consentement.php");
    default:
        include("controleurs/c_connexion.php");
        break;
}
?>
</main>

<?php include '/var/www/html/projet/gsbextranetB3/vues/v_footer.php'; ?>

</body>
</html>
