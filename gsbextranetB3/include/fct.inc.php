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
	$_SESSION['mail'] = $_SESSION['mail'] ?? ($_SESSION['a2f_mail'] ?? '');

	if (!empty($_SESSION['mail'])) {
		$_SESSION['jwt'] = genererJetonUtilisateur((int) $id, $_SESSION['mail'], $role, 3600);
		$_SESSION['jwt_exp'] = time() + 3600;
	}
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

if (!defined('JWT_SECRET')) {
    $jwtSecret = getenv('GSB_JWT_SECRET');
    if ($jwtSecret === false || $jwtSecret === '') {
        throw new RuntimeException('La variable GSB_JWT_SECRET doit être configurée.');
    }
    define('JWT_SECRET', $jwtSecret);
}

/**
 * Génère un JWT utilisateur avec le rôle et le mail du médecin
 *
 * @param int $id
 * @param string $email
 * @param string $role
 * @param int $validitySeconds
 * @return string
 */
function genererJetonUtilisateur($id, $email, $role, $validitySeconds = 3600) {
    $jwt = new JWT();
    $payload = array(
        'user_id' => (int) $id,
        'email' => $email,
        'role' => $role,
        'roles' => array($role),
    );

    return $jwt->generate(
        array('typ' => 'JWT', 'alg' => 'HS256'),
        $payload,
        JWT_SECRET,
        (int) $validitySeconds
    );
}

/**
 * Génère un JWT spécifique pour un compte administrateur.
 */
function genererJetonAdmin($id, $email, $validitySeconds = 3600) {
    return genererJetonUtilisateur($id, $email, 'admin', $validitySeconds);
}

class JWT
{
    /**
     * Génération JWT
     * @param array $header Header du token
     * @param array $payload Payload du Token
     * @param string $secret Clé secrète
     * @param int $validity Durée de validité (en secondes)
     * @return string Token
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 86400): string
    {
        if ($validity > 0) {
            $now = new DateTime();
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $now->getTimestamp() + $validity;
        }

        $base64Header = $this->base64UrlEncode(json_encode($header));
        $base64Payload = $this->base64UrlEncode(json_encode($payload));
        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, base64_encode($secret), true);
        $base64Signature = $this->base64UrlEncode($signature);

        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }

    /**
     * Vérification du token
     * @param string $token Token à vérifier
     * @param string $secret Clé secrète
     * @return bool Vérifié ou non
     */
    public function check(string $token, string $secret): bool
    {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);
        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;
    }

    public function getHeader(string $token)
    {
        $array = explode('.', $token);
        if (count($array) !== 3) {
            return array();
        }

        return json_decode($this->base64UrlDecode($array[0]), true) ?: array();
    }

    public function getPayload(string $token)
    {
        $array = explode('.', $token);
        if (count($array) !== 3) {
            return array();
        }

        return json_decode($this->base64UrlDecode($array[1]), true) ?: array();
    }

    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);
        if (!isset($payload['exp'])) {
            return false;
        }

        return $payload['exp'] < time();
    }

    public function isValid(string $token): bool
    {
        return preg_match('/^[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+$/', $token) === 1;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $remainder = strlen($value) % 4;
        if ($remainder > 0) {
            $value .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($value, '-_', '+/'));
    }
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