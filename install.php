<?php
/*
Planning Biblio, Plugin Congés Version 1.4.5
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/install.php
Création : 24 juillet 2013
Dernière modification : 11 mars 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant l'installation du plugin Congés. Ajoute les informations nécessaires dans la base de données
*/

session_start();

// Sécurité
if($_SESSION['login_id']!=1){
  echo "<br/><br/><h3>Vous devez vous connecter au planning<br/>avec le login \"admin\" pour pouvoir installer ce plugin.</h3>\n";
  echo "<a href='../../index.php'>Retour au planning</a>\n";
  exit;
}

$version="1.4.5";
include_once "../../include/config.php";

$sql=array();

// Configuration
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-SamediSeulement','boolean','0','Autoriser les demandes de récupération des samedis seulement','Congés','','20');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DeuxSamedis','boolean','0','Autoriser les demandes de récupération pour 2 samedis','Congés','','30');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiDefaut','text','7','Delai pour les demandes de récupération par d&eacute;faut (en jours)','Congés','','40');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiTitulaire1','enum','0','Delai pour les demandes de récupération des titulaires pour 1 samedi (en mois)','Congés','D&eacute;faut,0,1,2,3,4,5','50');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiTitulaire2','enum','0','Delai pour les demandes de récupération des titulaires pour 2 samedis (en mois)','Congés','D&eacute;faut,0,1,2,3,4,5','60');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiContractuel1','enum','0','Delai pour les demandes de récupération des contractuels pour 1 samedi (en semaines)','Congés','D&eacute;faut,0,1,2,3,4,5,6,7,8,9,10','70');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiContractuel2','enum','0','Delai pour les demandes de récupération des contractuels pour 2 samedis (en semaines)','Congés','D&eacute;faut,0,1,2,3,4,5,6,7,8,9,10','80');";

// Droits d'accès
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - Index','100','plugins/conges/index.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - Liste','100','plugins/conges/voir.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - Enregistrer','100','plugins/conges/enregistrer.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - Modifier','100','plugins/conges/modif.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - CalculCredit','100','plugins/conges/ajax.calculCredit.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`groupe`,`page`) VALUES ('Congés - Infos','2','Gestion des cong&eacute;s, validation N2','plugins/conges/infos.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - r&eacute;cuperations','100','plugins/conges/recuperations.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - R&eacute;cup&eacute;ration','100','plugins/conges/recuperation_modif.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`groupe`) VALUES ('Congés - Validation niveau 1','7','Gestion des cong&eacute;s, validation N1');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Congés - Compte &Eacute;pargne Temps','100','plugins/conges/cet.php');";

// Création de la table conges
$sql[]="CREATE TABLE `{$dbprefix}conges` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `perso_id` INT(11) NOT NULL, 
  `debut` DATETIME NOT NULL, `fin` DATETIME NOT NULL, `commentaires` TEXT, `refus` TEXT, `heures` VARCHAR(20), `debit` VARCHAR(20), 
  `saisie` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `saisie_par` INT NOT NULL, `modif` INT(11) NOT NULL DEFAULT '0',`modification` TIMESTAMP, 
  `valide` INT(11) NOT NULL DEFAULT '0',`validation` TIMESTAMP,`solde_prec` FLOAT(10), `solde_actuel` FLOAT(10)),
  `recup_prec` FLOAT(10), `recup_actuel` FLOAT(10)),`reliquat_prec` FLOAT(10), `reliquat_actuel` FLOAT(10), 
  `anticipation_prec` FLOAT(10), `anticipation_actuel` FLOAT(10), `supprime` INT(11) NOT NULL DEFAULT 0, 
  `supprDate` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00', `information` INT(11) NOT NULL DEFAULT 0, 
  `infoDate` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00');";

// Création de la table conges_infos
$sql[]="CREATE TABLE `{$dbprefix}conges_infos` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `debut` DATE NULL, `fin` DATE NULL, `texte` TEXT NULL, `saisie` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);";

// Création de la table récupérations
$sql[]="CREATE TABLE `{$dbprefix}recuperations` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `perso_id` INT(11) NOT NULL, 
  `date` DATE NULL, `date2` DATE NULL, `heures` FLOAT(5), `etat` VARCHAR(20), `commentaires` TEXT, 
  `saisie` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `saisie_par` INT NOT NULL, `modif` INT(11) NOT NULL DEFAULT '0', 
  `modification` TIMESTAMP, `valide` INT(11) NOT NULL DEFAULT '0',`validation` TIMESTAMP,`refus` TEXT, 
  `solde_prec` FLOAT(10), `solde_actuel` FLOAT(10));";

// Menu
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,0,'Cong&eacute;s','plugins/conges/index.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,10,'Liste des cong&eacute;s','plugins/conges/voir.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,20,'Poser des cong&eacute;s','plugins/conges/enregistrer.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,26,'R&eacute;cup&eacute;rations','plugins/conges/recuperations.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,28,'Compte &Eacute;pargne Temps','plugins/conges/cet.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,30,'Informations','plugins/conges/infos.php');";

// Modification de la table personnel
$sql[]="ALTER TABLE `{$dbprefix}personnel` ADD `congesCredit` FLOAT(10), ADD `congesReliquat` FLOAT(10), ADD `congesAnticipation` FLOAT(10);";
$sql[]="ALTER TABLE `{$dbprefix}personnel` ADD `recupSamedi` FLOAT(10);";
$sql[]="ALTER TABLE `{$dbprefix}personnel` ADD `congesAnnuel` FLOAT(10);";

// Ajout des taches planifiées
$sql[]="INSERT INTO `{$dbprefix}cron` (m,h,dom,mon,dow,command,comments) VALUES (0,0,1,1,'*','plugins/conges/cron.jan1.php','Cron Congés 1er Janvier');";
$sql[]="INSERT INTO `{$dbprefix}cron` (m,h,dom,mon,dow,command,comments) VALUES (0,0,1,9,'*','plugins/conges/cron.sept1.php','Cron Congés 1er Septembre');";

// Inscription du plugin Congés dans la base
$sql[]="INSERT INTO `{$dbprefix}plugins` (`nom`) VALUES ('conges');";

// Création de la table conges_CET
$sql[]="CREATE TABLE `{$dbprefix}conges_CET` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `perso_id` INT(11) NOT NULL, 
  `jours` INT(11) NOT NULL DEFAULT '0', `commentaires` TEXT, `saisie` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `saisie_par` INT NOT NULL, `modif` INT(11) NOT NULL DEFAULT '0', `modification` TIMESTAMP, `valideN1` INT(11) NOT NULL DEFAULT '0', 
  `validationN1` TIMESTAMP, `valideN2` INT(11) NOT NULL DEFAULT '0',`validationN2` TIMESTAMP, `refus` TEXT, 
  `solde_prec` FLOAT(10), `solde_actuel` FLOAT(10), `annee` VARCHAR(10));";
?>
<!-- Entête HTML -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Plugin Congés - Installation</title>
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