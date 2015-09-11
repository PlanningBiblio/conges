<?php
/*
Planning Biblio, Plugin Congés Version 1.5.5
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2015 - Jérôme Combes

Fichier : plugins/conges/uninstall.php
Création : 24 juillet 2013
Dernière modification : 12 septembre 2014
Auteur : Jérôme Combes, jerome@planningbiblio.fr

Description :
Fichier permettant la désinstallation du plugin Congés. Supprime les informations LDAP de la base de données
*/

session_start();

// Sécurité
if($_SESSION['login_id']!=1){
  echo "<br/><br/><h3>Vous devez vous connecter au planning<br/>avec le login \"admin\" pour pouvoir d&eacute;sinstaller ce plugin.</h3>\n";
  echo "<a href='../../index.php'>Retour au planning</a>\n";
  exit;
}


$version="1.4.5";
include_once "../../include/config.php";
$sql=array();

// Droits d'accès
$sql[]="DELETE FROM `{$dbprefix}acces` WHERE `page` LIKE 'plugins/conges%';";

// Suppression de la table conges
$sql[]="DROP TABLE `{$dbprefix}conges`;";

// Suppression de la table conges_infos
$sql[]="DROP TABLE `{$dbprefix}conges_infos`;";

// Suppression de la table conges_CET
$sql[]="DROP TABLE `{$dbprefix}conges_CET`;";

// Suppression de la table recuperations
$sql[]="DROP TABLE `{$dbprefix}recuperations`;";

// Suppression du menu
$sql[]="DELETE FROM `{$dbprefix}menu` WHERE `url` LIKE 'plugins/conges/%';";

// Modification de la table personnel
$sql[]="ALTER TABLE `{$dbprefix}personnel` DROP `congesCredit`, DROP `congesReliquat`, DROP `congesAnticipation`, DROP `recupSamedi`;";
$sql[]="ALTER TABLE `{$dbprefix}personnel` DROP `congesAnnuel`;";

// Suppression des tâches planifiées
$sql[]="DELETE FROM `{$dbprefix}cron` WHERE command LIKE 'plugins/conges/';";

// Suppression du plugin Congés dans la base
$sql[]="DELETE FROM `{$dbprefix}plugins` WHERE `nom`='conges';";

// Suppression de  la config
$sql[]="DELETE FROM `{$dbprefix}config` WHERE `nom`='Recup-SamediSeulement';";
$sql[]="DELETE FROM `{$dbprefix}config` WHERE `nom`='Recup-DeuxSamedis';";
$sql[]="DELETE FROM `{$dbprefix}config` WHERE `nom`='Recup-DelaiTitulaire1';";
$sql[]="DELETE FROM `{$dbprefix}config` WHERE `nom`='Recup-DelaiTitulaire2';";
$sql[]="DELETE FROM `{$dbprefix}config` WHERE `nom`='Recup-DelaiContractuel1';";
$sql[]="DELETE FROM `{$dbprefix}config` WHERE `nom`='Recup-DelaiContractuel2';";

?>
<!-- Entête HTML -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Plugin Congés - Désinstallation</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<?php
// Execution des requêtes
foreach($sql as $elem){
  $db=new db();
  $db->query($elem);
  if(!$db->error)
    echo "$elem : <font style='color:green;'>OK</font><br/>\n";
  else
    echo "$elem : <font style='color:red;'>Erreur</font><br/>\n";
}

echo "<br/><br/><a href='../../index.php'>Retour au planning</a>\n";
?>

</body>
</html>