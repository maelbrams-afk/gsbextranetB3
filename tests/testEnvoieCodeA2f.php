<?php
// Test de l'envoi du mail : remplacer l'adresse par la vôtre (ou celle de MailHog / Mailtrap)
require_once '../include/fct.inc.php';

var_dump(envoieCodeA2f('123456', 'votre.adresse@exemple.fr'));   // true si le mail est accepté pour envoi
