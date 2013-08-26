<?php
/*
Planning Biblio, Plugin Conges Version 1.0
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/menudiv.php
Création : 13 août 2013
Dernière modification : 13 août 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier intégré au menudiv (planning/poste/menudiv.php)
Permet de retirer du menu les agents en congés
*/

require_once "class.conges.php";

// recherche des personnes à exclure (congés)
$c=new conges();
$c->debut="$date $debut";
$c->fin="$date $fin";
$c->valide=true;
$c->fetch();

foreach($c->elements as $elem){
  $tab_exclus[]=$elem['perso_id'];
  $absents[]=$elem['perso_id'];
}

?>