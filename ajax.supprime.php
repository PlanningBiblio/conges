<?php
/*
Planning Biblio, Plugin Congés Version 1.6.5
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
@copyright 2013-2017 Jérôme Combes

Fichier : plugins/conges/ajax.supprime.php
Création : 9 janvier 2014
Dernière modification : 22 avril 2015
@author Jérôme Combes <jerome@planningbiblio.fr>

Description :
Supprime un congé
Appelé en Ajax via la fonction supprimeConges à partir de la page modif.php
*/

session_start();

$version="1.6.5";

ini_set('display_errors',0);

include "../../include/config.php";
include "class.conges.php";

$id=filter_input(INPUT_GET,"id",FILTER_SANITIZE_NUMBER_INT);

$c=new conges();
$c->id=$id;
$c->delete();
?>