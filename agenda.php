<?php
/*
Planning Biblio, Plugin Conges Version 1.3
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2015 - Jérôme Combes

Fichier : plugins/conges/agenda.php
Création : 14 mars 2014
Dernière modification : 17 mars 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier intégré dans l'agenda (agenda/index.php)
Ajoute les informations sur les congés dans l'agenda
Modifie les variables $absent et $absences_affichage initiées dans le fichier agenda/index.php
*/

/*
$absent = true si une absence sur toute la journée est enregistrée, permet de ne pas afficher les horaires habituels dans ce cas
$absences_affichage = message d'absence (absent(e) toute la journée, absent de telle à telle heure)
$current = date de la cellule courante
$perso_id = id de l'agent
$current_postes = liste des postes occupés avec heures de début, de fin, et indicateur "absent"
*/

include_once "class.conges.php";
$c=new conges();
$c->perso_id=$perso_id;
$c->debut=$current." 00:00:00";
$c->fin=$current." 23:59:59";
$c->valide=true;
$c->fetch();
$conges_affichage=null;
$conge_journee=false;

if(!empty($c->elements)){
  $conge=$c->elements[0];
  // Si en congé toute la journée, n'affiche pas les horaires de présence habituels et les absences enregistrées 
  // (remplace le message d'absence)
  if($conge['debut']<=$current." 00:00:00" and $conge['fin']>=$current." 23:59:59"){
    $absent=true;
    $conge_journee=true;
    $absences_affichage="En cong&eacute; toute la journ&eacute;e";
  }
  elseif(substr($conge['debut'],0,10)==$current and substr($conge['fin'],0,10)==$current){
    $deb=heure2(substr($conge['debut'],-8));
    $fi=heure2(substr($conge['fin'],-8));
    $conges_affichage="En cong&eacute; de $deb &agrave; $fi";
  }
  elseif(substr($conge['debut'],0,10)==$current and $conge['fin']>=$current." 23:59:59"){
    $deb=heure2(substr($conge['debut'],-8));
    $conges_affichage="En cong&eacute; &agrave; partir de $deb";
  }
  elseif($conge['debut']<=$current." 00:00:00" and substr($conge['fin'],0,10)==$current){
    $fi=heure2(substr($conge['fin'],-8));
    $conges_affichage="En cong&eacute; jusqu'&agrave; $fi";
  }
  else{
    $conges_affichage="{$conge['debut']} --> {$conge['fin']}";
  }

  // Modifie l'index "absent" du tableau $current_postes pour barrer les postes concernés par le congé
  for($i=0;$i<count($current_postes);$i++){
    if($current." ".$current_postes[$i]['debut']<$conge['fin'] and $current." ".$current_postes[$i]['fin']>$conge['debut']){
      $current_postes[$i]['absent']=1;
    }
  }

}

// Si congé sur une partie de la journée seulement, complète le message d'absence
if($conges_affichage and !$conge_journee){
  $absences_affichage.="<p>$conges_affichage</p>";
}
?>