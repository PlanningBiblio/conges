<?php
/*
Planning Biblio, Plugin Congés Version 2.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2015 - Jérôme Combes

Fichier : plugins/conges/ajax.enregistreRecup.php
Création : 11 octobre 2013
Dernière modification : 9 janvier 2016
Auteur : Jérôme Combes, jerome@planningbiblio.fr

Description :
Enregistre la demande de récupération
*/

session_start();
include "../../include/config.php";

ini_set('display_errors',0);

include "class.conges.php";

// Initialisation des variables
$commentaires=trim(filter_input(INPUT_POST,"commentaires",FILTER_SANITIZE_STRING));
$date=filter_input(INPUT_POST,"date",FILTER_CALLBACK,array("options"=>"sanitize_dateFr"));
$date2=filter_input(INPUT_POST,"date2",FILTER_CALLBACK,array("options"=>"sanitize_dateFr"));
$heures=filter_input(INPUT_POST,"heures",FILTER_SANITIZE_STRING);
$perso_id=filter_input(INPUT_POST,"perso_id",FILTER_SANITIZE_NUMBER_INT);

// Les dates sont au format DD/MM/YYYY et converti en YYYY-MM-DD
$date=dateSQL($date);
$date2=dateSQL($date2);

if($perso_id===null){
  $perso_id=$_SESSION['login_id'];
}

$insert=array("perso_id"=>$perso_id,"date"=>$date,"date2"=>$date2,"heures"=>$heures,"commentaires"=>$commentaires,
  "saisie_par"=>$_SESSION['login_id']);
$db=new db();
$db->insert2("recuperations",$insert);
if($db->error){
  $return=array("Demande-Erreur");
  echo json_encode($return);
  exit;
}
else{
  $return=array("Demande-OK");

  // Envoi d'un e-mail à l'agent et aux responsables
  $p=new personnel();
  $p->fetchById($perso_id);
  $nom=$p->elements[0]['nom'];
  $prenom=$p->elements[0]['prenom'];
  $mail=$p->elements[0]['mail'];
  $mailsResponsables=$p->elements[0]['mailsResponsables'];

  $c=new conges();
  $c->getResponsables($date,$date,$perso_id);
  $responsables=$c->responsables;

  // Choix des destinataires en fonction de la configuration
  $a=new absences();
  $a->getRecipients(1,$responsables,$mail,$mailsResponsables);
  $destinataires=$a->recipients;

  if(!empty($destinataires)){
    $sujet="Nouvelle demande de récupération";
    $message="Demande de récupération du ".dateFr($date)." enregistrée pour $prenom $nom<br/><br/>";
    if($commentaires){
      $message.="Commentaires : ".str_replace("\n","<br/>",$commentaires);
    }

    // Envoi du mail
    $m=new sendmail();
    $m->subject=$sujet;
    $m->message=$message;
    $m->to=$destinataires;
    $m->send();
    
    $return[]=$m->error_CJInfo;
  }
  
  echo json_encode($return);
}
?>