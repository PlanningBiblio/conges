<?php
/*
Planning Biblio, Plugin Congés Version 1.4.5
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/ajax.getReliquat.php
Création : 6 mars 2014
Dernière modification : 10 mars 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Recupére le nombre d'heure de reliquat pour un agent donné
Utilisé pour l'alimentation du CET, formulaire de la page plugins/conges/cet.php
*/
include "../../include/config.php";
include "class.conges.php";

$c=new conges();
$c->perso_id=$_GET['perso_id'];
$c->fetchCredit();
$reliquatHeures=$c->elements['reliquat']?$c->elements['reliquat']:0;
$reliquatJours=number_format($reliquatHeures/7,2,","," ");
echo json_encode(array("reliquatHeures"=>$reliquatHeures,"reliquatJours"=>$reliquatJours));
?>