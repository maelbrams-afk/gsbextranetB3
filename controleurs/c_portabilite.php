<?php
/**
 * Contrôleur de la portabilité des données (Article 20 RGPD)
 */

// Vérification de la sécurité : s'assurer qu'un médecin est bien connecté
$idMedecinConnecte = $_SESSION['idMedecin'] ?? $_SESSION['id'] ?? null;

if (!$idMedecinConnecte) {
    // Redirection vers la page de connexion si la session est expirée ou absente
    header('Location: index.php?uc=connexion&action=demandeConnexion');
    exit();
}

// Inclusion du modèle contenant les fonctions PDO de portabilité
require_once 'modele/bd.portabilite.inc.php';

// Récupération de l'action demandée (par défaut 'demande')
$action = $_REQUEST['action'] ?? 'demande';
$vue = 'vues/v_portabilite.php';

switch ($action) {
    case 'demande':
        // Affiche simplement la vue avec le bouton de confirmation
        break;

    case 'generer':
        // Vérification de la confirmation explicite de l'utilisateur ("Oui")
        if (isset($_POST['confirmation']) && $_POST['confirmation'] === 'oui') {
            try {
                // Appel de la fonction 2 pour générer le fichier JSON et récupérer l'empreinte
                $resultatExport = genererFichierJsonPortabilite($pdo, (int)$idMedecinConnecte);
                $succes = true;
            } catch (Exception $e) {
                $erreur = $e->getMessage();
            }
        } else {
            // Si l'utilisateur annule ou ne confirme pas, on le redirige ou on affiche un message
            $erreur = "La génération du fichier a été annulée.";
        }
        break;

    case 'verifier':
        // Redirige ou charge la vue de vérification d'intégrité si nécessaire
        $vue = 'vues/v_verifier_integrite.php';
        break;

    default:
        // Action par défaut en cas de valeur non reconnue
        $action = 'demande';
        break;
}

// Chargement de la vue correspondante
include $vue;
?>