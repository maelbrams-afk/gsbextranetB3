# Documentation technique

## Technologies

- PHP avec PDO pour les pages et les requêtes SQL.
- MySQL ou MariaDB pour la base `gsbextranet`.
- Bootstrap déjà présent dans le projet pour les formulaires et tableaux.

## Installation et mise à jour

1. Copier le projet dans le dossier du serveur web local.
2. Créer la base `gsbextranet` et configurer la variable d'environnement `GSB_DB_PASSWORD` avec le mot de passe MySQL. L'application utilise aussi `GSB_JWT_SECRET` pour signer les jetons : définir une clé aléatoire longue, différente pour chaque environnement. Ne pas enregistrer ces valeurs dans le dépôt. Leur absence empêche le démarrage de l'application. Changer la clé JWT invalide les jetons déjà émis.
3. Pour une nouvelle base, importer `scriptBDD/gsbextranetStructures.sql`, puis les données nécessaires.
4. Pour une base déjà créée, exécuter **une seule fois** `scriptBDD/upgradeMotDePasse.sql`. Cette migration agrandit la colonne des mots de passe, ajoute le rôle, et crée les tables nécessaires.
5. Après migration, attribuer le rôle administrateur à un compte existant depuis phpMyAdmin. Remplacer l'adresse par celle du compte choisi :

   ```sql
   UPDATE medecin
   SET role = 'admin'
   WHERE mail = 'adresse-administrateur@example.fr';
   ```

6. Pour attribuer un compte de chef de produit :

   ```sql
   UPDATE medecin
   SET role = 'chef_produit'
   WHERE mail = 'adresse-chef@example.fr';
   ```

7. Pour activer la double authentification sur une base déjà créée, exécuter **une seule fois** `scriptBDD/upgradeA2f.sql`. Ce script ajoute `medecin.codeA2f` et `medecin.dateCodeA2f`. Une base créée avec `gsbextranetStructures.sql` contient déjà ces colonnes.

Les nouveaux comptes créés par le formulaire ont le rôle `medecin`. Les rôles d'administration ne sont pas proposés à l'inscription publique.

## Connexion et mots de passe

À l'inscription, le mot de passe est enregistré avec `password_hash()`. À la connexion, `password_verify()` compare le mot de passe saisi au hash. La colonne `motDePasse` doit conserver jusqu'à 255 caractères.

Si un ancien hash a déjà été tronqué par la colonne de 30 caractères, l'agrandissement ne peut pas le reconstruire. Le mot de passe de ce compte doit être réinitialisé ou le compte recréé.

## Double authentification par mail (2FA)

La connexion se fait en deux étapes dans `controleurs/c_connexion.php` :

1. `valideConnexion` vérifie le login et le mot de passe (premier facteur). Si c'est correct, le médecin n'est **pas encore connecté** : seuls `$_SESSION['a2f_id']` et `$_SESSION['a2f_mail']` sont renseignés.
2. `saisieCode` génère un code à 6 chiffres (`generateCodeA2f()`), l'enregistre avec la date et l'heure (`enregistreCodeA2f()`), l'envoie par mail (`envoieCodeA2f()`) et affiche `vues/v_saisieCode.php`.
3. `valideCode` vérifie que le code saisi correspond et qu'il a moins d'une minute (`verifieCodeA2f()`). Si c'est bon, le code est effacé (`effaceCodeA2f()`), l'identifiant de session est renouvelé et `connecter()` est appelé. Sinon, le message « Code incorrect ou expiré » s'affiche.

Le code est à usage unique et valable 1 minute. Le délai est calculé par MySQL (`DATE_SUB(NOW(), INTERVAL 1 MINUTE)`).

L'envoi du mail utilise la fonction PHP `mail()` : `SMTP` et `smtp_port` doivent être configurés dans `php.ini`. En local, un serveur de test comme MailHog ou Mailtrap évite d'envoyer de vrais mails.

Les scripts de test sont dans `tests/` : `testEnregistreCodeA2f.php`, `testVerifieCodeA2f.php`, `testEffaceCodeA2f.php`, `testGenerateCodeA2f.php` et `testEnvoieCodeA2f.php`.

## Fonctionnement

- `index.php` distribue les pages et vérifie le mode maintenance.
- `controleurs/c_produits.php` applique les droits, valide les formulaires, et enregistre les consultations.
- `include/class.pdogsb.inc.php` contient les requêtes PDO pour les produits, statistiques et journaux.
- Les ajouts, modifications et suppressions et leur journal sont faits dans une même transaction.
- La suppression désactive le produit au lieu d'effacer la ligne : les consultations et références existantes sont conservées.
- `consultation_produit` permet de compter les consultations. Les statistiques affichées sont le nombre total de consultations, le nombre de médecins distincts et les produits les plus consultés.
- `journal_operation` contient le compte, l'opération, le nom du produit, l'adresse IP et la date. La page affiche les 100 opérations les plus récentes.
- Les formulaires qui modifient des données utilisent un jeton CSRF stocké en session.
- L'administrateur peut laisser l'accès à la connexion ouvert pendant la maintenance afin de pouvoir arrêter celle-ci.

## Tables ajoutées ou modifiées

- `medecin.role` : `medecin`, `chef_produit` ou `admin`.
- `produit.id` : identifiant auto-incrémenté.
- `produit.actif` : masque les produits supprimés sans effacer leur historique.
- `consentement` : historique de consentement utilisé à l'inscription.
- `consultation_produit` : consultations et nom du produit au moment de la consultation.
- `journal_operation` : traces des opérations de gestion.
- `configurationSite` : état de maintenance du site.
- `medecin.codeA2f` et `medecin.dateCodeA2f` : code de connexion temporaire et date de sa création (remis à `NULL` après usage).
