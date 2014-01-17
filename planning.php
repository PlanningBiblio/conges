<?php
/*
Planning Biblio, Plugin Conges Version 1.3
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/menudiv.php
Création : 24 octobre 2013
Dernière modification : 24 octobre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier intégré au planning (planning/poste/index.php)
Ajoute les agents en congés à la liste des absents
*/

include_once "plugins/conges/class.conges.php";

$c=new conges();
$c->debut=$date." 00:00:00";
$c->fin=$date." 23:59:59";
$c->valide=true;
$c->fetch();
foreach($c->elements as $elem){
  if(!in_array($elem['perso_id'],$absences_id)){
    $elem['motif']="Cong&eacute; pay&eacute;";
    $absences[]=$elem;
    $absences_id[]=$elem['perso_id'];
  }
}
?>