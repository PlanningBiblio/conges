<?php
/*
Planning Biblio, Plugin Congés Version 1.3.2
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/recuperation_valide.php
Création : 30 août 2013
Dernière modification : 25 septembre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de modifier et valider les demandes de récupérations des samedis (validation du formulaire)
*/

session_start();
ini_set('display_errors',0);
ini_set('error_reporting',E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

$version="1.3.2";
include "../../include/config.php";
include "../../include/function.php";
include "class.conges.php";

// Initialisation des variables
$admin=in_array(2,$_SESSION['droits'])?true:false;
$id=$_POST['id'];
$msg="Erreur";

$c=new conges();
$c->recupId=$id;
$c->getRecup();
$recup=$c->elements[0];
$perso_id=$recup['perso_id'];

// Sécurité
if(!$admin and $perso_id!=$_SESSION['login_id']){
  header("Location: ../../index.php?page=plugins/conges/recuperations.php&message=Refus");
  exit;
}

// Modification des heures
$update=array("heures"=>$_POST['heures'],"commentaires"=>$_POST['commentaires'],"modif"=>$_SESSION['login_id'],"modification"=>date("Y-m-d H:i:s"));

// Modification des heures  et validation par l'administrateur
if(isset($_POST['validation']) and $admin){
  $update['valide']=$_POST['validation'];
  $update['validation']=date("Y-m-d H:i:s");
  $update['refus']=isset($_POST['refus'])?htmlentities($_POST['refus'],ENT_QUOTES|ENT_IGNORE,"UTF-8"):null;
}

if(isset($update)){
  // Modification de la table recuperations
  $db=new db();
  $db->update2("recuperations",$update,array("id"=>$id));
  if(!$db->error){
    $msg="OK";
  }
  // Modification du crédit d'heures de récupérations si il y a validation
  if(isset($update['valide']) and $update['valide']>0){
    $db=new db();
    $db->select("personnel","recupSamedi","id='$perso_id'");
    $solde_prec=$db->result[0]['recupSamedi'];
    $recupSamedi=$solde_prec+$update['heures'];
    $db=new db();
    $db->update2("personnel",array("recupSamedi"=>$recupSamedi),array("id"=>$perso_id));
    $db=new db();
    $db->update2("recuperations",array("solde_prec"=>$solde_prec,"solde_actuel"=>$recupSamedi),array("id"=>$id));
    
  }
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
  $c->getResponsables($recup['date'],$recup['date'],$perso_id);
  $responsables=$c->responsables;
  foreach($responsables as $elem){
    if(verifmail($elem['mail']) and !in_array($elem['mail'],$destinataires)){
      $destinataires[]=$elem['mail'];
    }
  }
  if(!empty($destinataires)){
    if(isset($update['valide']) and $update['valide']>0){
      $sujet="Demande de récupération validée";
      $message="Demande de récupération du ".dateFr($recup['date'])." validée pour $prenom $nom";
    }
    elseif(isset($update['valide']) and $update['valide']<0){
      $sujet="Demande de récupération refusée";
      $message="Demande de récupération du ".dateFr($recup['date'])." refusée pour $prenom $nom";
      $message.="<br/><br/>".str_replace("\n","<br/>",$update['refus']);
    }
    else{
      $sujet="Demande de récupération modifiée";
      $message="Demande de récupération du ".dateFr($recup['date'])." modifiée pour $prenom $nom";
    }

    $destinataires=join(";",$destinataires);
    sendmail($sujet,$message,$destinataires);
  }
}

header("Location: ../../index.php?page=plugins/conges/recuperations.php&message=$msg");
?>