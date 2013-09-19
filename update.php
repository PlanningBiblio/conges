<?php
/*
Planning Biblio, Plugin Congés Version 1.3.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/update.php
Création : 26 août 2013
Dernière modification : 19 septembre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant la mise à jour du plugin Congés. Ajoute ou modifie des informations dans la base de données
*/

session_start();

// Sécurité
if($_SESSION['login_id']!=1){
  echo "<br/><br/><h3>Vous devez vous connecter au planning<br/>avec le login \"admin\" pour pouvoir installer ce plugin.</h3>\n";
  echo "<a href='../../index.php'>Retour au planning</a>\n";
  exit;
}

$version="1.1";
include_once "../../include/config.php";

$sql=array();

// Modification de la table conges

// Modification du 30 août 2013
/*
// Motif de refus des congés
$sql[]="ALTER TABLE `{$dbprefix}conges` ADD `refus` TEXT;";

// Droits d'accès aux pages de récupérations
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - r&eacute;cuperation','100','plugins/conges/recuperation.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - r&eacute;cuperations','100','plugins/conges/recuperations.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - Enregistre r&eacute;cup&eacute;ration','100','plugins/conges/ajax.recup.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - R&eacute;cup&eacute;ration','100','plugins/conges/recuperation_modif.php');";

// Affichage des récupérations dans le menu
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,24,'Demander une r&eacute;cup&eacute;ration','plugins/conges/recuperation.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,26,'Liste des r&eacute;cup&eacute;rations','plugins/conges/recuperations.php');";

// Création de la table récupérations
$sql[]="CREATE TABLE `{$dbprefix}recuperations` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `perso_id` INT(11) NOT NULL, `date` DATE NULL, `heures` FLOAT(5), `etat` VARCHAR(20), `commentaires` TEXT, `saisie` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `modif` INT(11) NOT NULL DEFAULT '0',`modification` TIMESTAMP, `valide` INT(11) NOT NULL DEFAULT '0',`validation` TIMESTAMP,`refus` TEXT);";
*/

// Modification du 19 septembre 2013
$sql[]="DELETE FROM `{$dbprefix}menu` WHERE `url`='plugins/conges/recuperation.php';";
$sql[]="UPDATE `{$dbprefix}menu` SET `titre`='R&eacute;cup&eacute;rations' WHERE `url`='plugins/conges/recuperations.php';";

?>
<!-- Entête HTML -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Plugin Congés - Mise à jour</title>
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