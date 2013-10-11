<?php
/*
Planning Biblio, Plugin Congés Version 1.3.4
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/ajax.verifRecup.php
Création : 18 septembre 2013
Dernière modification : 23 septembre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Vérifie si le jour demandé à déjà fait l'objet d'une demande de récuperation.
Dans le cas contraire, enregistre la demande.
Appelé en arrière plan par la fonction JS verifRecup()
*/

session_start();
ini_set('display_errors',0);
ini_set('error_reporting',E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

$version="1.3.5";
include "../../include/config.php";
include "../../include/function.php";
include "../../personnel/class.personnel.php";
include "class.conges.php";

$date=dateFr($_GET['date']);
$perso_id=is_numeric($_GET['perso_id'])?$_GET['perso_id']:$_SESSION['login_id'];


$db=new db();
$db->select("recuperations",null,"`perso_id`='$perso_id' AND `date`='$date'");
if($db->result){
  echo "###Demande###";
}
else{
  $update=array("perso_id"=>$perso_id,"date"=>$date,"heures"=>$_GET['heures'],"commentaires"=>$_GET['commentaires']);
  $db=new db();
  $db->insert2("recuperations",$update);
  if($db->error){
    echo "###Demande-Erreur###";
  }
  else{
    echo "###Demande-OK###";

    // Envoi d'un e-mail à l'agent et aux responsables
    $destinataires=array();
    $p=new personnel();
    $p->fetchById($perso_id);
    $nom=$p->elements[0]['nom'];
    $prenom=$p->elements[0]['prenom'];
    $mail=$p->elements[0]['mail'];
    if(verifmail($mail)){
      $destinataires[]=$mail;
    }
    $c=new conges();
    $c->getResponsables($date,$date,$perso_id);
    $responsables=$c->responsables;
    foreach($responsables as $elem){
      if(verifmail($elem['mail']) and !in_array($elem['mail'],$destinataires)){
	$destinataires[]=$elem['mail'];
      }
    }
    if(!empty($destinataires)){
      $sujet="Nouvelle demande de récupération";
      $message="Demande de récupération du ".dateFr($date)." enregistrée pour $prenom $nom<br/><br/>";
      if($_GET['commentaires']){
	$message.="Commentaires : ".str_replace("\n","<br/>",$_GET['commentaires']);
      }
      $destinataires=join(";",$destinataires);
      sendmail($sujet,$message,$destinataires);
    }
  }
}
?>