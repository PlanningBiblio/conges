 <?php
/*
Planning Biblio, Plugin Congés Version 1.5.5
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2015 - Jérôme Combes

Fichier : plugins/conges/ficheAgentValid.php
Création : 15 janvier 2014
Dernière modification : 11 septembre 2014
Auteur : Jérôme Combes, jerome@planningbiblio.fr

Description :
Fichier permettant de mettre à jour les crédits congés des agents lors de la modification de leur fiche
Inclus dans le fichier personnel/valid.php
*/

// Include
include_once "plugins/conges/class.conges.php";

// Mise à jour des crédits dans la table personnel
$credits=array();
$credits["congesCredit"]=$_POST['congesCredit'].".".$_POST['congesCreditMin'];
$credits["congesReliquat"]=$_POST['congesReliquat'].".".$_POST['congesReliquatMin'];
$credits["congesAnticipation"]=$_POST['congesAnticipation'].".".$_POST['congesAnticipationMin'];
$credits["recupSamedi"]=$_POST['recupSamedi'].".".$_POST['recupSamediMin'];
$credits["congesAnnuel"]=$_POST['congesAnnuel'].".".$_POST['congesAnnuelMin'];

if($action=="modif"){
  $update=array_merge($update,$credits);
}else{
  $insert=array_merge($insert,$credits);
}

// Ajout d'un ligne d'information dans la liste des congés
$c=new conges();
$c->perso_id=$id;
$c->maj($credits,$action);

?>