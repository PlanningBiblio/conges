 <?php
/*
Planning Biblio, Plugin Congés Version 1.4.2
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/ficheAgentValid.php
Création : 15 janvier 2014
Dernière modification : 15 janvier 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de mettre à jour les crédits congés des agents lors de la modification de leur fiche
Inclus dans le fichier personnel/valid.php
*/

// Include
include_once "plugins/conges/class.conges.php";

// Mise à jour des crédits dans la table personnel
$credits=array();
$credits["congesCredit"]=heure4($_POST['congesCredit']);
$credits["congesReliquat"]=heure4($_POST['congesReliquat']);
$credits["congesAnticipation"]=heure4($_POST['congesAnticipation']);
$credits["recupSamedi"]=heure4($_POST['recupSamedi']);
$credits["congesAnnuel"]=heure4($_POST['congesAnnuel']);

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