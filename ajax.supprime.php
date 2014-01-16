<?php
/*
Planning Biblio, Plugin Congés Version 1.3.9
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/ajax.supprime.php
Création : 9 janvier 2014
Dernière modification : 10 janvier 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Supprime un congé
Appelé en Ajax via la fonction supprimeConges à partir de la page modif.php
*/

session_start();

$version="1.3.9";

ini_set('display_errors',0);
error_reporting(0);

include "../../include/config.php";
include "../../include/function.php";
include "class.conges.php";

$c=new conges();
$c->id=$_GET['id'];
$c->delete();
?>