<?php
/*
Planning Biblio, Plugin Conges Version 1.6.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2015 - Jérôme Combes

Fichier : plugins/conges/planning_cellule_poste.php
Création : 30 janvier 2014
Dernière modification : 23 février 2015
Auteur : Jérôme Combes, jerome@planningbiblio.fr

Description :
Fichier intégré au planning (planning/poste/fonctions.php)
Vérifie si l'agent placé dans la cellule est en congés, si oui, il le barre en orange
*/

foreach($GLOBALS['conges'] as $conge){
  if($conge['perso_id']==$elem['perso_id'] and $conge['debut']<"$date {$elem['fin']}" and $conge['fin']>"$date {$elem['debut']}"){
    $class_tmp[]="orange";
    $class_tmp[]="striped";
  }
}
?>