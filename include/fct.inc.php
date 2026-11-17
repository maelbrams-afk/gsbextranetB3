<?php
//on insère le fichier qui contient les fonctions


 /**
 * Teste si un quelconque visiteur est connecté
 * @return vrai ou faux 
 */
function estConnecte(){
  return isset($_SESSION['id']);
}
/**
 * Enregistre dans une variable session les infos d'un visiteur
 
 * @param $id 
 * @param $nom
 * @param $prenom
 */
function connecter($id, $nom, $prenom, $role = 'medecin'){
	$_SESSION['id'] = $id;
	$_SESSION['nom'] = $nom;
	$_SESSION['prenom'] = $prenom;
	$_SESSION['role'] = $role;
}

function estAdmin(){
	return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function estChefProduit(){
	return isset($_SESSION['role']) && in_array($_SESSION['role'], array('admin', 'chef_produit'), true);
}

function jetonCsrf(){
	if (!isset($_SESSION['jetonCsrf'])) {
		$_SESSION['jetonCsrf'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['jetonCsrf'];
}

function verifierJetonCsrf($jeton){
	return isset($_SESSION['jetonCsrf'])
		&& is_string($jeton)
		&& hash_equals($_SESSION['jetonCsrf'], $jeton);
}


/* gestion des erreurs*/
/**
 * Indique si une valeur est un entier positif ou nul
 
 * @param $valeur
 * @return vrai ou faux
*/
function estEntierPositif($valeur) {
	return preg_match("/[^0-9]/", $valeur) == 0;
	
}

/**
 * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
 
 * @param $tabEntiers : le tableau
 * @return vrai ou faux
*/
function estTableauEntiers($tabEntiers) {
	$ok = true;
	if (isset($unEntier) ){
		foreach($tabEntiers as $unEntier){
			if(!estEntierPositif($unEntier)){
		 		$ok=false; 
			}
		}	
	}
	return $ok;
}

/**
 * Ajoute le libellé d'une erreur au tableau des erreurs 
 
 * @param $msg : le libellé de l'erreur 
 */
function ajouterErreur($msg){
   if (! isset($_REQUEST['erreurs'])){
      $_REQUEST['erreurs']=array();
	} 
   $_REQUEST['erreurs'][]=$msg;
}
/**
 * Retoune le nombre de lignes du tableau des erreurs 
 
 * @return le nombre d'erreurs
 */
function nbErreurs(){
   if (!isset($_REQUEST['erreurs'])){
	   return 0;
	}
	else{
	   return count($_REQUEST['erreurs']);
	}
}


function input_data($data){
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;

}


    
    function generateCode(){
          // Generate a 6 digits hexadecimal number
	  $numbytes = 3; // Because 6 digits hexadecimal = 3 bytes
	  $bytes = openssl_random_pseudo_bytes($numbytes); 
          $hex   = bin2hex($bytes);
	  return $hex;
        
    }
    
    
    

/**
 * 2FA - Génère un code à 6 chiffres (les zéros du début sont conservés : 004217)
 * random_int() est cryptographiquement sûr, contrairement à rand()
 * @return string
 */
function generateCodeA2f() {
    return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * 2FA - Envoie le code de connexion par mail
 * @param string $code
 * @param string $mail adresse du médecin
 * @return bool vrai si le mail a été accepté pour envoi
 */
function envoieCodeA2f($code, $mail) {
    $sujet   = "GSB Extranet - votre code de connexion";
    $message = "Votre code de connexion : " . $code . "\n"
             . "Il est valable 1 minute.\n\n"
             . "Si vous n'êtes pas à l'origine de cette connexion, ignorez ce message.";
    $entetes = "From: no-reply@gsb.fr\r\nContent-Type: text/plain; charset=UTF-8";
    return mail($mail, $sujet, $message, $entetes);
}

?>