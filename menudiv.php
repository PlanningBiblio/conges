<?php
/**
Planning Biblio, Plugin Conges Version 2.6
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
@copyright 2013-2017 Jérôme Combes

Fichier : plugins/conges/menudiv.php
Création : 13 août 2013
Dernière modification : 16 mars 2017
@author Jérôme Combes <jerome@planningbiblio.fr>

Description :
Fichier intégré au menudiv (planning/poste/menudiv.php)
Permet de retirer du menu les agents en congés
*/

require_once "class.conges.php";

// recherche des personnes à exclure (congés)
$c=new conges();
$c->debut="$date $debut";
$c->fin="$date $fin";
$c->valide=false;
$c->supprime = false;
$c->information = false;
$c->bornesExclues=true;
$c->fetch();

foreach($c->elements as $elem){
  if($elem['valide'] > 0){
    $tab_exclus[]=$elem['perso_id'];
    $absents[]=$elem['perso_id'];
    break;
  }else{
    $absences_non_validees[] = $elem['perso_id'];
  }
}

?>