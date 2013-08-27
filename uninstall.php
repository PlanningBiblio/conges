<?php
/*
Planning Biblio, Plugin Congés Version 1.2
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/uninstall.php
Création : 24 juillet 2013
Dernière modification : 20 août 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

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


$version="1.0";
include_once "../../include/config.php";
$sql=array();

// Droits d'accès
$sql[]="DELETE FROM `{$dbprefix}acces` WHERE `page`='plugins/conges/index.php';";
$sql[]="DELETE FROM `{$dbprefix}acces` WHERE `page`='plugins/conges/voir.php';";
$sql[]="DELETE FROM `{$dbprefix}acces` WHERE `page`='plugins/conges/enregistrer.php';";
$sql[]="DELETE FROM `{$dbprefix}acces` WHERE `page`='plugins/conges/modif.php';";
$sql[]="DELETE FROM `{$dbprefix}acces` WHERE `page`='plugins/conges/ajax.calculCredit.php';";
$sql[]="DELETE FROM `{$dbprefix}acces` WHERE `groupe_id`='2';";

// Suppression de la table conges
$sql[]="DROP TABLE `{$dbprefix}conges`;";

// Suppression de la table conges_infos
$sql[]="DROP TABLE `{$dbprefix}conges_infos`;";

// Suppression du menu
$sql[]="DELETE FROM `{$dbprefix}menu` WHERE `url` LIKE 'plugins/conges/%';";

// Modification de la table personnel
$sql[]="ALTER TABLE `{$dbprefix}personnel` DROP `congesCredit`, DROP `congesReliquat`, DROP `congesAnticipation`, DROP `recupSamedi`;";
$sql[]="ALTER TABLE `{$dbprefix}personnel` DROP `congesAnnuel`;";

// Suppression des tâches planifiées
$sql[]="DELETE FROM `{$dbprefix}cron` WHERE command LIKE 'plugins/conges/';";

//	Suppression du plugin Congés dans la base
$sql[]="DELETE FROM `{$dbprefix}plugins` WHERE `nom`='conges';";

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