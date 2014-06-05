<?php
/*
Planning Biblio, Plugin Congés Version 1.5.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/ajax.enregistreRecup.php
Création : 11 octobre 2013
Dernière modification : 3 juin 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Enregistre la demande de récupération
*/

session_start();

$version="1.5.1";
include "../../include/config.php";

ini_set('display_errors',0);
error_reporting(E_ALL ^ E_NOTICE);

include "class.conges.php";

// Les dates sont au format DD/MM/YYYY et converti en YYYY-MM-DD
$date=dateFr($_GET['date']);
$date2=dateFr($_GET['date2']);
$perso_id=is_numeric($_GET['perso_id'])?$_GET['perso_id']:$_SESSION['login_id'];

$insert=array("perso_id"=>$perso_id,"date"=>$date,"date2"=>$date2,"heures"=>$_GET['heures'],"commentaires"=>$_GET['commentaires'],
  "saisie_par"=>$_SESSION['login_id']);
$db=new db();
$db->insert2("recuperations",$insert);
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
  $c->getResponsables($date,$date,$perso_id);
  $responsables=$c->responsables;

  // Choix des destinataires en fonction de la configuration
  $a=new absences();
  $a->getRecipients($config['Absences-notifications'],$responsables,$mail,$mailResponsable);
  $destinataires=$a->recipients;

  if(!empty($destinataires)){
    $sujet="Nouvelle demande de récupération";
    $message="Demande de récupération du ".dateFr($date)." enregistrée pour $prenom $nom<br/><br/>";
    if($_GET['commentaires']){
      $message.="Commentaires : ".str_replace("\n","<br/>",$_GET['commentaires']);
    }
    sendmail($sujet,$message,$destinataires);
  }
}
?>