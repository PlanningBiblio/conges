<?php
/*
Planning Biblio, Plugin Conges Version 1.0.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/cron.sept1.php
Création : 13 août 2013
Dernière modification : 16 janvier 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier executant des taches planifiées au 1er septembre pour le plugin Conges.
Page appelée par le fichier include/cron.php
Met à jour les crédits de congés
*/

require_once "class.conges.php";
require_once "personnel/class.personnel.php";

// Ajout d'une ligne d'information dans le tableau des congés
$p=new personnel();
$p->supprime=array(0,1);
$p->fetch();
if($p->elements){
  foreach($p->elements as $elem){
    $credits=array();
    $credits['congesCredit']=floatval($elem['congesAnnuel'])-floatval($elem['congesAnticipation']);
    $credits['recupSamedi']=0;
    $credits['congesAnticipation']=0;
    $credits['congesReliquat']=$elem['congesCredit'];

    $c=new conges();
    $c->perso_id=$elem['id'];
    $c->maj($credits,"modif",true);
  }
}

// Modifie les crédits
$db=new db();
$db->update("personnel","congesReliquat=congesCredit");
$db=new db();
$db->update("personnel","recupSamedi='0.00'");
$db=new db();
$db->update("personnel","congesCredit=(congesAnnuel-congesAnticipation)");
$db=new db();
$db->update("personnel","congesAnticipation=0.00");
?>