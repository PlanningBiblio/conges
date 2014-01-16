<?php
/*
Planning Biblio, Plugin Conges Version 1.0.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/cron.jan1.php
Création : 13 août 2013
Dernière modification : 16 janvier 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier executant des taches planifiées au 1er janvier pour le plugin Conges.
Page appelée par le fichier include/cron.php
Supprime le reliquat à tous les agents
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
    $credits['congesCredit']=$elem['congesCredit'];
    $credits['recupSamedi']=$elem['recupSamedi'];
    $credits['congesAnticipation']=$elem['congesAnticipation'];
    $credits['congesReliquat']=0;

    $c=new conges();
    $c->perso_id=$elem['id'];
    $c->maj($credits,"modif",true);
  }
}

// Modifie les crédits
$db=new db();
$db->update("personnel","congesReliquat='0.00'");
?>