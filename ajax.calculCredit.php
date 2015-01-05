<?php
/*
Planning Biblio, Plugin Congés Version 1.5.6
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2015 - Jérôme Combes

Fichier : plugins/conges/ajax.calculCredit.php
Création : 2 août 2013
Dernière modification : 5 novembre 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Calcul le nombre d'heures correspondant à un congé
Appelé en arrière plan par la fonction JS calculCredit() (fichier plugins/conges/js/script.conges.js) 
  lors du clic sur le bouton calculer du formulaire de saisie de congés (fichier plugins/conges/enregistrer.php)
*/

require_once "../../include/config.php";
require_once "class.conges.php";

$c=new conges();
$c->calculCredit(dateSQL($_GET['debut']),$_GET['hre_debut'],dateSQL($_GET['fin']),$_GET['hre_fin'],$_GET['perso_id']);
$result=$c->error?array("error"):array("OK");
$result[]=$c->heures;
echo json_encode($result);
?>