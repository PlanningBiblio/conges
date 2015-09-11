<?php
/*
Planning Biblio, Plugin Conges Version 1.3
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2015 - Jérôme Combes

Fichier : plugins/conges/planning.php
Création : 24 octobre 2013
Dernière modification : 11 février 2014
Auteur : Jérôme Combes, jerome@planningbiblio.fr

Description :
Fichier intégré au planning (planning/poste/index.php)
Ajoute les agents en congés à la liste des absents
*/

// $conges = liste des congés du jour, créé par planning_cellules.php inclus plutôt dans planning/poste/index.php

foreach($conges as $elem){
  $elem['motif']="Cong&eacute; pay&eacute;";
  $absences[]=$elem;
  $absences_id[]=$elem['perso_id'];
}
?>