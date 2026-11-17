# Cas de tests

Les tests ci-dessous sont à réaliser sur une base de test, après l'installation ou la migration SQL. Utiliser un compte médecin, un compte chef de produit et un compte administrateur.

| ID | Cas testé | Étapes | Résultat attendu |
|---|---|---|---|
| T01 | Consulter la liste des produits | Se connecter comme médecin puis ouvrir « Produits ». | La liste s'affiche sans boutons de gestion. |
| T02 | Consulter un produit | Ouvrir le détail d'une fiche. | Les informations sont visibles et une consultation est enregistrée. |
| T03 | Refuser un produit inexistant | Ouvrir un détail avec un identifiant inconnu. | Un message indique que le produit n'existe pas. |
| T04 | Ajouter un produit | Se connecter comme chef, remplir les champs et enregistrer. | Le produit apparaît dans la liste et une trace « creation » contient le compte et l'adresse IP. |
| T05 | Refuser un formulaire vide | Envoyer le formulaire produit sans nom ou avec des informations manquantes. | Le produit n'est pas enregistré et un message d'erreur s'affiche. |
| T06 | Modifier un produit | Modifier une fiche comme chef et enregistrer. | Les nouvelles valeurs sont visibles et une trace « modification » est ajoutée. |
| T07 | Supprimer un produit | Confirmer la suppression comme chef. | Le produit disparaît du catalogue, son historique reste en base et une trace « suppression » conserve son nom et l'adresse IP. |
| T08 | Refuser une action de gestion au médecin | Tenter d'ouvrir l'ajout ou la modification avec un compte médecin. | L'accès est refusé et aucune donnée n'est modifiée. |
| T09 | Vérifier les statistiques | Consulter la page statistiques comme chef ou administrateur après plusieurs consultations. | Le total, le nombre de médecins distincts et le classement correspondent aux traces. |
| T10 | Refuser l'administration au chef | Ouvrir la page d'administration avec un compte chef. | L'accès est refusé. |
| T11 | Activer la maintenance | Se connecter comme administrateur et activer la maintenance. | Les comptes non administrateurs voient le message de maintenance ; l'administrateur peut encore accéder au site. |
| T12 | Désactiver la maintenance | Se connecter comme administrateur et arrêter la maintenance. | Les pages sont de nouveau accessibles. |
| T13 | Refuser un formulaire sans jeton | Modifier un formulaire POST en retirant `jetonCsrf`. | La requête est refusée et la base n'est pas modifiée. |
| T14 | Vérifier les mots de passe hachés | Créer un compte, puis se connecter avec le mot de passe utilisé. | Le mot de passe fonctionne et la valeur en base est un hash complet. |
| T15 | Vérifier un ancien hash tronqué | Après migration, essayer le mot de passe d'un compte dont le hash était déjà coupé. | La connexion échoue ; le compte nécessite une réinitialisation du mot de passe. |
| T16 | Connexion avec le bon code | Saisir un login et un mot de passe corrects, ouvrir le mail reçu, puis saisir le code dans la minute. | Le médecin arrive sur le sommaire (ou sur la page de maintenance si elle est active et que le compte n'est pas administrateur). |
| T17 | Refuser un mauvais code | Après un login et un mot de passe corrects, saisir un code différent de celui reçu. | Le message « Code incorrect ou expiré » s'affiche et le médecin n'est pas connecté. |
| T18 | Refuser un code expiré | Attendre plus d'une minute avant de saisir le bon code. | Le message « Code incorrect ou expiré » s'affiche. Le lien « Renvoyer un code » envoie un nouveau code valable. |
| T19 | Code à usage unique | Se connecter avec succès, se déconnecter, se reconnecter jusqu'à la page du code, puis saisir l'ancien code. | L'ancien code est refusé ; seul le nouveau code reçu fonctionne. |
| T20 | Pas d'accès avant le deuxième facteur | Saisir un login et un mot de passe corrects, puis, sans saisir le code, ouvrir `index.php?uc=produits`. | Le médecin est renvoyé vers la page de connexion et ne voit aucune donnée. |

## Recette

Pour chaque cas, noter la date, le compte utilisé (sans noter son mot de passe), le résultat observé et « OK » ou « KO ». Si un cas échoue, décrire l'erreur et le corriger avant la démonstration.
