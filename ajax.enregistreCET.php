<?php
/*
Planning Biblio, Plugin Congés Version 1.5.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/ajax.enregistreCet.php
Création : 7 mars 2014
Dernière modification : 3 juin 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Enregistre la demande de récupération
*/

session_start();
ini_set('display_errors',0);
include "../../include/config.php";
include "class.conges.php";

$id=$_GET['id'];
$perso_id=$_GET['perso_id'];
$validation=$_GET['validation'];
$valideN1=null;
$valideN2=null;
$validationN1=null;
$validationN2=null;

switch($validation){
  case -2 : $valideN2=-$_SESSION['login_id']; $validationN2=date("Y-m-d H:i:s"); break;
  case -1 : $valideN1=-$_SESSION['login_id']; $validationN1=date("Y-m-d H:i:s"); break;
  case 1 : $valideN1=$_SESSION['login_id']; $validationN1=date("Y-m-d H:i:s"); break;
  case 2 : $valideN2=$_SESSION['login_id']; $validationN2=date("Y-m-d H:i:s"); break;
}

$data=array("perso_id"=>$perso_id,"jours"=>$_GET['jours'],"commentaires"=>$_GET['commentaires']);
if($valideN1){
  $data['valideN1']=$valideN1;
  $data['validationN1']=$validationN1;
}
if($valideN2){
  $data['valideN2']=$valideN2;
  $data['validationN2']=$validationN2;
}


if(is_numeric($id)){
  // Modifie la demande d'alimentation du CET
  $data["modif"]=$_SESSION['login_id'];
  $data["modification"]=date("Y-m-d H:i:s");

  $db=new db();
  $db->update2("conges_CET",$data,array("id"=>$id));
}
else{
  // Enregistrement de la demande d'alimentation du CET
  $data["saisie"]=date("Y-m-d H:i:s");
  $data["saisie_par"]=$_SESSION['login_id'];

  $db=new db();
  $db->insert2("conges_CET",$data);
}

if($db->error){
  echo "###Demande-Erreur###";
}
else{
  echo "###Demande-OK###";

  // Envoi d'un e-mail à l'agent et aux responsables
  $p=new personnel();
  $p->fetchById($perso_id);
  $nom=$p->elements[0]['nom'];
  $prenom=$p->elements[0]['prenom'];
  $mail=$p->elements[0]['mail'];
  $mailResponsable=$p->elements[0]['mailResponsable'];

  $c=new conges();
  $c->getResponsables(null,null,$perso_id);
  $responsables=$c->responsables;

  // Choix des destinataires en fonction de la configuration
  $a=new absences();
  $a->getRecipients($config['Absences-notifications'],$responsables,$mail,$mailResponsable);
  $destinataires=$a->recipients;

  if(!empty($destinataires)){
    $sujet="Nouvelle demande de CET";
    $message="Demande de CET a été enregistrée pour $prenom $nom<br/><br/>";
    if($_GET['commentaires']){
      $message.="Commentaires : ".str_replace("\n","<br/>",$_GET['commentaires']);
    }
    sendmail($sujet,$message,$destinataires);
  }
}
?>