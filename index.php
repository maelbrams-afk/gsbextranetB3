<?php
ob_start();
session_start();
require_once("include/fct.inc.php");
require_once("include/class.pdogsb.inc.php");

date_default_timezone_set('Europe/Paris');

$pdo = PdoGsb::getPdoGsb();
$uc = isset($_GET['uc']) ? $_GET['uc'] : 'connexion';
$maintenance = $pdo->estEnMaintenance();
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
if ($maintenance && !estAdmin() && $uc !== 'connexion') {
    include("vues/v_maintenance.php");
} else switch ($uc) {
    case 'connexion':
        include("controleurs/c_connexion.php");
        break;
    case 'creation':
        include("controleurs/c_creation.php");
        break;
    case 'produits':
        include("controleurs/c_produits.php");
        break;
    case 'admin':
        include("controleurs/c_admin.php");
        break;
    case 'consentement':
        include("controleurs/c_consentement.php");
        break;
    default:
        include("controleurs/c_connexion.php");
        break;
}
?>
</main>

<?php include 'vues/v_footer.php'; ?>

</body>
</html>
