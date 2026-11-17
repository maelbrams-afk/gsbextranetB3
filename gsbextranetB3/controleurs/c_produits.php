<?php

if (!estConnecte()) {
    header('Location: index.php?uc=connexion');
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : 'liste';
$gestionAutorisee = estChefProduit();
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
$erreur = '';
$produit = null;
$produits = array();
$statistiques = null;

if (in_array($action, array('ajouter', 'modifier', 'supprimer'), true) && !$gestionAutorisee) {
    http_response_code(403);
    $erreur = "Vous n'avez pas les droits pour modifier les produits.";
    $action = 'liste';
}

if (in_array($action, array('modifier', 'supprimer', 'detail'), true)) {
    $idProduit = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : false;
    if ($idProduit === false || $idProduit === null || $idProduit < 1) {
        $erreur = "Le produit demandé n'est pas valide.";
        $action = 'liste';
    } else {
        $produit = $pdo->donneProduit($idProduit);
        if ($produit === false) {
            $erreur = "Le produit demandé n'existe pas.";
            $action = 'liste';
        }
    }
}

if ($action === 'detail' && $produit !== null) {
    $pdo->enregistrerConsultationProduit((int)$_SESSION['id'], $produit);
}

if (in_array($action, array('ajouter', 'modifier'), true)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifierJetonCsrf(isset($_POST['jetonCsrf']) ? $_POST['jetonCsrf'] : null)) {
            http_response_code(400);
            $erreur = "Le formulaire a expiré. Rechargez la page puis réessayez.";
        } else {
            $produit = array(
                'id' => isset($_GET['id']) ? (int)$_GET['id'] : null,
                'nom' => isset($_POST['nom']) ? trim($_POST['nom']) : '',
                'objectif' => isset($_POST['objectif']) ? trim($_POST['objectif']) : '',
                'information' => isset($_POST['information']) ? trim($_POST['information']) : '',
                'effetIndesirable' => isset($_POST['effetIndesirable']) ? trim($_POST['effetIndesirable']) : ''
            );

            if ($produit['nom'] === '' || strlen($produit['nom']) > 60) {
                $erreur = "Le nom du produit est obligatoire et limité à 60 caractères.";
            } elseif ($produit['objectif'] === '' || $produit['information'] === '' || $produit['effetIndesirable'] === '') {
                $erreur = "Tous les champs du produit doivent être remplis.";
            } else {
                $adresseIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue';
                if ($action === 'ajouter') {
                    $pdo->ajouterProduitEtTracer(
                        $produit['nom'],
                        $produit['objectif'],
                        $produit['information'],
                        $produit['effetIndesirable'],
                        (int)$_SESSION['id'],
                        $adresseIp
                    );
                    $_SESSION['message'] = "Le produit a été ajouté.";
                } else {
                    $pdo->modifierProduitEtTracer(
                        (int)$produit['id'],
                        $produit['nom'],
                        $produit['objectif'],
                        $produit['information'],
                        $produit['effetIndesirable'],
                        (int)$_SESSION['id'],
                        $adresseIp
                    );
                    $_SESSION['message'] = "Le produit a été modifié.";
                }
                header('Location: index.php?uc=produits');
                exit();
            }
        }
    } elseif ($action === 'ajouter') {
        $produit = array(
            'nom' => '',
            'objectif' => '',
            'information' => '',
            'effetIndesirable' => ''
        );
    }
}

if ($action === 'supprimer' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifierJetonCsrf(isset($_POST['jetonCsrf']) ? $_POST['jetonCsrf'] : null)) {
        http_response_code(400);
        $erreur = "Le formulaire a expiré. Rechargez la page puis réessayez.";
    } else {
        $adresseIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue';
        $pdo->supprimerProduitEtTracer(
            (int)$produit['id'],
            $produit['nom'],
            (int)$_SESSION['id'],
            $adresseIp
        );
        $_SESSION['message'] = "Le produit a été supprimé.";
        header('Location: index.php?uc=produits');
        exit();
    }
}

if ($action === 'statistiques' && $gestionAutorisee) {
    $statistiques = $pdo->donneStatistiquesProduits();
}

if ($action === 'liste' || $action === 'statistiques') {
    $produits = $pdo->listeProduits();
}

include 'vues/v_produits.php';
