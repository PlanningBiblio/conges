<?php
/*
Planning Biblio, Plugin Congés Version 1.5.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
@copyright 2013-2018 Jérôme Combes

Fichier : plugins/conges/ajax.verifConges.php
Création : 12 février 2014
Dernière modification : 3 juin 2014
@author Jérôme Combes <jerome@planningbiblio.fr>

Description :
Vérifie si la période demandée a déjà fait l'objet d'une demande de congés.
Appelé en arrière plan par la fonction JS verifConges()
*/

session_start();
ini_set('display_errors',0);
ini_set('error_reporting',E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

$version="1.5.1";
include "../../include/config.php";
include "../../personnel/class.personnel.php";
include "class.conges.php";

$debut=$_GET['debut'];
$fin=$_GET['fin']?$_GET['fin']:$debut;
$fin=$fin;
$hre_debut=$_GET['hre_debut']?$_GET['hre_debut']:"00:00:00";
$hre_fin=$_GET['hre_fin']?$_GET['hre_fin']:"23:59:59";
$perso_id=$_GET['perso_id'];
$id=$_GET['id'];

$db=new db();
$db->select("conges",null,"`id`<>'$id' AND `perso_id`='$perso_id' AND `debut` < '$fin $hre_fin' AND `fin` > '$debut $hre_debut' AND `supprime`='0' AND `information`='0' AND `valide`>='0' ","ORDER BY `debut`,`fin`");
if(!$db->result){
  echo "Pas de congé";
}else{
  echo "du ".dateFr($db->result[0]['debut'],true)." au ".dateFr($db->result[0]['fin'],true);
}
?>