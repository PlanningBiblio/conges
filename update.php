<?php
/*
Planning Biblio, Plugin Congés Version 1.3.7
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/update.php
Création : 26 août 2013
Dernière modification : 11 octobre 2013
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

$version="1.3.7";
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
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - R&eacute;cup&eacute;ration','100','plugins/conges/recuperation_modif.php');";

// Affichage des récupérations dans le menu
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,24,'Demander une r&eacute;cup&eacute;ration','plugins/conges/recuperation.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,26,'Liste des r&eacute;cup&eacute;rations','plugins/conges/recuperations.php');";

// Création de la table récupérations
$sql[]="CREATE TABLE `{$dbprefix}recuperations` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `perso_id` INT(11) NOT NULL, `date` DATE NULL, `heures` FLOAT(5), `etat` VARCHAR(20), `commentaires` TEXT, `saisie` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `modif` INT(11) NOT NULL DEFAULT '0',`modification` TIMESTAMP, `valide` INT(11) NOT NULL DEFAULT '0',`validation` TIMESTAMP,`refus` TEXT);";
*/

// Modification du 19 septembre 2013
/*
$sql[]="DELETE FROM `{$dbprefix}menu` WHERE `url`='plugins/conges/recuperation.php';";
$sql[]="UPDATE `{$dbprefix}menu` SET `titre`='R&eacute;cup&eacute;rations' WHERE `url`='plugins/conges/recuperations.php';";
*/

// Modification du 25 septembre 2013, version 1.3.4
// $sql[]="ALTER TABLE `{$dbprefix}conges` ADD `solde_prec` FLOAT(10), ADD `solde_actuel` FLOAT(10);";
// $sql[]="ALTER TABLE `{$dbprefix}recuperations` ADD `solde_prec` FLOAT(10), ADD `solde_actuel` FLOAT(10);";
// $sql[]="ALTER TABLE `{$dbprefix}conges` ADD `recup_prec` FLOAT(10), ADD `recup_actuel` FLOAT(10), ADD `reliquat_prec` FLOAT(10), ADD `reliquat_actuel` FLOAT(10);";

// Modification du 3 octobre 2013
// $sql[]="ALTER TABLE `{$dbprefix}conges` ADD `anticipation_prec` FLOAT(10), ADD `anticipation_actuel` FLOAT(10);";
// $sql[]="ALTER TABLE `{$dbprefix}personnel` CHANGE `congesCredit` `congesCredit` FLOAT(10), CHANGE `congesReliquat` `congesReliquat` FLOAT(10), CHANGE `congesAnticipation` `congesAnticipation` FLOAT(10);";
// $sql[]="ALTER TABLE `{$dbprefix}personnel` CHANGE `recupSamedi` `recupSamedi` FLOAT(10);";
// $sql[]="ALTER TABLE `{$dbprefix}personnel` CHANGE `congesAnnuel` `congesAnnuel` FLOAT(10);";
// $sql[]="DELETE FROM `{$dbprefix}acces` WHERE `page`='plugins/conges/recuperation.php';";

// Modification du 3 octobre 2013, version 1.3.5
// $sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`groupe`) VALUES ('Congés - Validation niveau 1','7','Gestion des cong&eacute;s, validation N1');";
// $sql[]="UPDATE `{$dbprefix}acces` SET `groupe`='Gestion des cong&eacute;s, validation N2' WHERE groupe_id='2';";
// $sql[]="ALTER TABLE `{$dbprefix}conges` ADD `valideN1` INT(11), ADD `validationN1` TIMESTAMP;";


// Modification du 11 octobre 2013
// Configuration
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-SamediSeulement','boolean','0','Autoriser les demandes de récupération des samedis seulement','Congés','','40');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DeuxSamedis','boolean','0','Autoriser les demandes de récupération pour 2 samedis','Congés','','40');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiTitulaire1','enum','0','Delai pour les demandes de récupération des titulaires pour 1 samedi (en mois)','Congés','0,1,2,3,4,5','40');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiTitulaire2','enum','0','Delai pour les demandes de récupération des titulaires pour 2 samedis (en mois)','Congés','0,1,2,3,4,5','40');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiContractuel1','enum','0','Delai pour les demandes de récupération des contractuels pour 1 samedi (en semaines)','Congés','0,1,2,3,4,5,6,7,8,9,10','40');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiContractuel2','enum','0','Delai pour les demandes de récupération des contractuels pour 2 samedis (en semaines)','Congés','0,1,2,3,4,5,6,7,8,9,10','40');";
$sql[]="ALTER TABLE `{$dbprefix}recuperations` ADD `date2` DATE ;";
$sql[]="DELETE FROM `{$dbprefix}acces` WHERE `page`='plugins/conges/ajax.recup.php';";


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