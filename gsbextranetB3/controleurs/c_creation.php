<?php

if(!isset($_GET['action'])){
	$_GET['action'] = 'demandeCreation';
}
$action = $_GET['action'];
switch($action){
	
	case 'demandeCreation':{
		include("vues/v_creation.php");
		break;
	}
	case 'valideCreation':{
		
		$leLogin = isset($_POST['login']) ? trim($_POST['login']) : '';
        $lePassword = isset($_POST['mdp']) ? $_POST['mdp'] : '';
        $loginOk = true;
        $passwordOk = true;

        $rempli = false;
        if ($loginOk && $passwordOk){
            $rempli = true; 
            if ($leLogin === '') {
                echo 'Le login n\'a pas été saisi<br/>';
                $rempli = false;
            }
            if ($lePassword === ''){
                echo 'Le mot de passe n\'a pas été saisi<br/>';
                $rempli = false; 
            }
            
            // si le login et le mdp contiennent quelque chose
            // on continue les vérifications
            if ($rempli){
                // vérification de la taille du champs
                $nbCarMaxLogin = $pdo->tailleChampsMail();
                if(strlen($leLogin) > $nbCarMaxLogin){
                    echo 'Le login ne peut contenir plus de '.$nbCarMaxLogin.' caractères<br/>';
                    $loginOk = false;
                }
                // vérification du format du login
                if (!filter_var($leLogin, FILTER_VALIDATE_EMAIL)) {
                    echo 'le mail n\'a pas un format correct<br/>';
                    $loginOk = false;
                }
                $patternPassword = '#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{12,}$#';
                if (preg_match($patternPassword, $lePassword) !== 1){
                    echo 'Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial<br/>';
                    $passwordOk = false;
                }
            }
        }
        if($rempli && $loginOk && $passwordOk){
            // Hachage du mot de passe avant insertion en base
            $hashedPassword = password_hash($lePassword, PASSWORD_DEFAULT);
            $executionOK = $pdo->creeMedecin($leLogin, $hashedPassword);
            if ($executionOK == true){
                echo "Votre compte a bien été créé.";
                $pdo->connexionInitiale($leLogin);
            }
            else {
                echo "Ce login existe déjà, veuillez en choisir un autre.";
            }
        }

        break;
    }
	default :{
		include("vues/v_connexion.php");
		break;
	}
}
?>