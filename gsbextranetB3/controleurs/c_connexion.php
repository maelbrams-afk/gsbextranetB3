<?php


if(!isset($_GET['action'])){
	$_GET['action'] = 'demandeConnexion';
}
$action = $_GET['action'];
switch($action){
	
	case 'demandeConnexion':{
		include("vues/v_connexion.php");
		break;
	}

	// ETAPE 1 : vérification du 1er facteur (login + mot de passe)
	case 'valideConnexion':{
		$login = isset($_POST['login']) ? trim($_POST['login']) : '';
		$mdp = isset($_POST['mdp']) ? $_POST['mdp'] : '';
		$connexionOk = $pdo->checkUser($login,$mdp);
		if(!$connexionOk){
			ajouterErreur("Login ou mot de passe incorrect");
			include("vues/v_erreurs.php");
			include("vues/v_connexion.php");
		}
		else {
			// Mot de passe correct, mais le médecin n'est PAS encore connecté :
			// on ne remplit pas $_SESSION['id'] (donc pas d'appel à connecter()).
			// On mémorise seulement qui est en train de passer la 2FA.
			$infosMedecin = $pdo->donneLeMedecinByMail($login);
			$_SESSION['a2f_id'] = $infosMedecin['id'];
			$_SESSION['a2f_mail'] = $login;
			header('Location: index.php?uc=connexion&action=saisieCode');
			exit();
		}
		break;
	}

	// ETAPE 2a : création du code, enregistrement en base, envoi par mail, affichage de la saisie
	case 'saisieCode':{
		if(!isset($_SESSION['a2f_id'])){
			// personne n'a passé l'étape 1 : retour à la connexion
			include("vues/v_connexion.php");
			break;
		}
		$code = generateCodeA2f();
		$pdo->enregistreCodeA2f($_SESSION['a2f_id'], $code);
		envoieCodeA2f($code, $_SESSION['a2f_mail']);
		include("vues/v_saisieCode.php");
		break;
	}

	// ETAPE 2b : vérification du code saisi (2ème facteur), puis vraie connexion
	case 'valideCode':{
		if(!isset($_SESSION['a2f_id'])){
			include("vues/v_connexion.php");
			break;
		}
		$id = $_SESSION['a2f_id'];
		$codeSaisi = trim(isset($_POST['code']) ? $_POST['code'] : '');
		if($pdo->verifieCodeA2f($id, $codeSaisi)){
			$pdo->effaceCodeA2f($id);                  // code à usage unique
			$infosMedecin = $pdo->donneLeMedecinByMail($_SESSION['a2f_mail']);
			$_SESSION['mail'] = $infosMedecin['mail'];
			unset($_SESSION['a2f_id'], $_SESSION['a2f_mail']);
			session_regenerate_id(true);               // nouvel identifiant de session
			connecter($infosMedecin['id'], $infosMedecin['nom'], $infosMedecin['prenom'], $infosMedecin['role']);

			if ($pdo->estEnMaintenance() && !estAdmin()) {
				include("vues/v_maintenance.php");
			} else {
				include("vues/v_sommaire.php");
			}
		}
		else {
			ajouterErreur("Code incorrect ou expiré");
			include("vues/v_erreurs.php");
			include("vues/v_saisieCode.php");
		}
		break;
	}

	case 'deconnexion':{
		$_SESSION = array();
		session_destroy();
		header('Location: index.php?uc=connexion');
		exit();
	}

	default :{
		include("vues/v_connexion.php");
		break;
	}
}
?>