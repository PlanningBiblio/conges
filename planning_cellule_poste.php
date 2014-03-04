<?php
/*
Planning Biblio, Plugin Conges Version 1.3
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/planning_cellule_poste.php
Création : 30 janvier 2014
Dernière modification : 30 janvier 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier intégré au planning (planning/poste/fonctions.php)
Vérifie si l'agent placé dans la cellule est en congés, si oui, il le barre en orange
*/

$date=$_SESSION['PLdate'];
foreach($GLOBALS['conges'] as $conge){
  if($conge['perso_id']==$elem['perso_id'] and $conge['debut']<"$date {$elem['fin']}" and $conge['fin']>"$date {$elem['debut']}"){
    $resultat="<s style='color:orange;'>$resultat</s>";
  }
}
?>