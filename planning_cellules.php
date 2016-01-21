<?php
/*
Planning Biblio, Plugin Conges Version 1.3
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
@copyright 2013-1016 Jérôme Combes

Fichier : plugins/conges/planning_cellules.php
Création : 30 janvier 2014
Dernière modification : 30 janvier 2014
@author Jérôme Combes <jerome@planningbiblio.fr>

Description :
Fichier intégré au planning (planning/poste/index.php)
Créé un tableau recensant les informations sur les congés du jour
Permet de barrer les agents en congés dans les cellules
*/

include_once "plugins/conges/class.conges.php";

$c=new conges();
$c->debut=$date." 00:00:00";
$c->fin=$date." 23:59:59";
$c->valide=true;
$c->fetch();
$conges=$c->elements;
global $conges;
?>