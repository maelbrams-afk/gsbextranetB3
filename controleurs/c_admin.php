<?php

if (!estAdmin()) {
    http_response_code(403);
    header('Location: index.php?uc=produits');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'maintenance') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST'
        || !verifierJetonCsrf(isset($_POST['jetonCsrf']) ? $_POST['jetonCsrf'] : null)
    ) {
        http_response_code(400);
        $erreur = "Le formulaire a expiré. Rechargez la page puis réessayez.";
    } else {
        $actif = isset($_POST['maintenance']) && $_POST['maintenance'] === '1';
        $pdo->changerMaintenance($actif);
        $_SESSION['message'] = $actif
            ? "Le site est maintenant en maintenance."
            : "Le site est de nouveau accessible.";
        header('Location: index.php?uc=admin');
        exit();
    }
}

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
$statistiques = $pdo->donneStatistiquesProduits();
$operations = $pdo->donneOperationsProduits();
$maintenance = $pdo->estEnMaintenance();

include 'vues/v_admin.php';
