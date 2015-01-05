<?php
/*
Planning Biblio, Plugin Conges Version 1.3
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2015 - Jérôme Combes

Fichier : plugins/conges/menudiv.php
Création : 26 septembre 2013
Dernière modification : 26 septembre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier appelé lors de la suppression d'un agent par le fichier personnel/class.personnel.php, fonction personnel::delete()
Permet de supprimer les informations sur les congés des agents supprimés définitivement
La variables $liste et la liste des ids des agents à supprimer, séparés par des virgules
*/

require_once "class.conges.php";

// recherche des personnes à exclure (congés)
$c=new conges();
$c->suppression_agents($liste);
?>