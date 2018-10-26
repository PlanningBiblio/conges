<?php
/**
Planning Biblio, Plugin Congés Version 2.8
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
@copyright 2013-2018 Jérôme Combes

Fichier : plugins/conges/install.php
Création : 24 juillet 2013
Dernière modification : 16 février 2018
@author Jérôme Combes <jerome@planningbiblio.fr>

Description :
Fichier permettant l'installation du plugin Congés. Ajoute les informations nécessaires dans la base de données
*/

session_start();

// Sécurité
if ($_SESSION['login_id']!=1) {
    echo "<br/><br/><h3>Vous devez vous connecter au planning<br/>avec le login \"admin\" pour pouvoir installer ce plugin.</h3>\n";
    echo "<a href='../../index.php'>Retour au planning</a>\n";
    exit;
}

$version="2.8";
include_once "../../include/config.php";

$sql=array();

// Configuration
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-SamediSeulement','boolean','0','Autoriser les demandes de récupération des samedis seulement','Cong&eacute;s','','20');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DeuxSamedis','boolean','0','Autoriser les demandes de récupération pour 2 samedis','Cong&eacute;s','','30');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiDefaut','text','7','Delai pour les demandes de récupération par d&eacute;faut (en jours)','Cong&eacute;s','','40');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiTitulaire1','enum','0','Delai pour les demandes de récupération des titulaires pour 1 samedi (en mois)','Cong&eacute;s','D&eacute;faut,0,1,2,3,4,5','50');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiTitulaire2','enum','0','Delai pour les demandes de récupération des titulaires pour 2 samedis (en mois)','Cong&eacute;s','D&eacute;faut,0,1,2,3,4,5','60');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiContractuel1','enum','0','Delai pour les demandes de récupération des contractuels pour 1 samedi (en semaines)','Cong&eacute;s','D&eacute;faut,0,1,2,3,4,5,6,7,8,9,10','70');";
$sql[]="INSERT INTO `{$dbprefix}config` VALUES (null,'Recup-DelaiContractuel2','enum','0','Delai pour les demandes de récupération des contractuels pour 2 samedis (en semaines)','Cong&eacute;s','D&eacute;faut,0,1,2,3,4,5,6,7,8,9,10','80');";

// Configuration : gestion des rappels
$sql[]="INSERT INTO `{$dbprefix}config` (`nom`, `type`, `valeur`, `categorie`, `commentaires`, `ordre` ) 
  VALUES ('Conges-Rappels', 'boolean', '0', 'Cong&eacute;s', 'Activer / D&eacute;sactiver l&apos;envoi de rappels s&apos;il y a des cong&eacute;s non-valid&eacute;s', '6');";
$sql[]="INSERT INTO `{$dbprefix}config` (`nom`, `type`, `valeur`, `categorie`, `commentaires`, `ordre` ) 
  VALUES ('Conges-Rappels-Jours', 'text', '14', 'Cong&eacute;s', 'Nombre de jours &agrave; contr&ocirc;ler pour l&apos;envoi de rappels sur les cong&eacute;s non-valid&eacute;s', '7');";
$sql[]="INSERT INTO `{$dbprefix}config` (`nom`, `type`, `valeur`, `valeurs`, `categorie`, `commentaires`, `ordre` ) VALUES ('Conges-Rappels-N1', 'checkboxes', '[\"Mail-Planning\"]', 
  '[[\"Mail-Planning\",\"La cellule planning\"],[\"mails_responsables\",\"Les responsables hi&eacute;rarchiques\"]]','Cong&eacute;s', 'A qui envoyer les rappels sur les cong&eacute;s non-valid&eacute;s au niveau 1', '8');";
$sql[]="INSERT INTO `{$dbprefix}config` (`nom`, `type`, `valeur`, `valeurs`, `categorie`, `commentaires`, `ordre` ) VALUES ('Conges-Rappels-N2', 'checkboxes', '[\"mails_responsables\"]', 
'[[\"Mail-Planning\",\"La cellule planning\"],[\"mails_responsables\",\"Les responsables hi&eacute;rarchiques\"]]','Cong&eacute;s', 'A qui envoyer les rappels sur les cong&eacute;s non-valid&eacute;s au niveau 2', '9');";

$sql[] = "INSERT INTO `{$dbprefix}config` (`nom`, `type`, `valeur`, `valeurs`, `categorie`, `commentaires`, `ordre` ) VALUES ('Conges-Recuperations', 'enum2', '1', '[[0,\"Assembler\"],[1,\"Dissocier\"]]', 'Cong&eacute;s', 'Traiter les r&eacute;cup&eacute;rations comme les cong&eacute;s (Assembler), ou les traiter s&eacute;par&eacute;ment (Dissocier)', '3');";

$sql[] = "INSERT INTO `{$dbprefix}config` (`nom`, `type`, `valeur`, `valeurs`, `categorie`, `commentaires`, `ordre` ) VALUES ('Conges-Validation-N2', 'enum2', '0', '[[0,\"Validation directe autoris&eacute;e\"],[1,\"Le cong&eacute; doit &ecirc;tre valid&eacute; au niveau 1\"]]', 'Cong&eacute;s', 'La validation niveau 2 des cong&eacute;s peut se faire directement ou doit attendre la validation niveau 1', '4');";

// Droits d'accès
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Cong&eacute;s - Index','100','plugins/conges/index.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Cong&eacute;s - Liste','100','plugins/conges/voir.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Cong&eacute;s - Enregistrer','100','plugins/conges/enregistrer.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Cong&eacute;s - Modifier','100','plugins/conges/modif.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe`,`groupe_id`,`categorie`,`ordre`) VALUES ('Gestion des cong&eacute;s, validation niveau 2','Gestion des cong&eacute;s, validation niveau 2',601,'Cong&eacute;s','76');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Cong&eacute;s - Infos','100','plugins/conges/infos.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Cong&eacute;s - r&eacute;cuperations','100','plugins/conges/recuperations.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Cong&eacute;s - R&eacute;cup&eacute;ration','100','plugins/conges/recuperation_modif.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`groupe`,`categorie`,`ordre`) VALUES ('Gestion des cong&eacute;s, validation niveau 1','401','Gestion des cong&eacute;s, validation niveau 1','Cong&eacute;s','75');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Cong&eacute;s - Compte &Eacute;pargne Temps','100','plugins/conges/cet.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`groupe`,`page`) VALUES ('Cong&eacute;s - Cr&eacute;dits','100','','plugins/conges/credits.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Cong&eacute;s - R&eacute;cup&eacute;rations','100','plugins/conges/recuperation_valide.php');";
$sql[]="INSERT INTO `{$dbprefix}acces` (`nom`,`groupe_id`,`page`) VALUES ('Cong&eacute;s - Poser des r&eacute;cup&eacute;rations','100','plugins/conges/recup_pose.php');";

// Création de la table conges
$sql[]="CREATE TABLE `{$dbprefix}conges` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `perso_id` INT(11) NOT NULL, 
  `debut` DATETIME NOT NULL, `fin` DATETIME NOT NULL, `commentaires` TEXT, `refus` TEXT, `heures` VARCHAR(20), `debit` VARCHAR(20), 
  `saisie` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `saisie_par` INT NOT NULL, `modif` INT(11) NOT NULL DEFAULT '0',`modification` TIMESTAMP, 
  `valide_n1` INT(11) NOT NULL DEFAULT '0',`validation_n1` TIMESTAMP,`valide` INT(11) NOT NULL DEFAULT '0',`validation` TIMESTAMP,
  `solde_prec` FLOAT(10), `solde_actuel` FLOAT(10),
  `recup_prec` FLOAT(10), `recup_actuel` FLOAT(10),`reliquat_prec` FLOAT(10), `reliquat_actuel` FLOAT(10), 
  `anticipation_prec` FLOAT(10), `anticipation_actuel` FLOAT(10), `supprime` INT(11) NOT NULL DEFAULT 0, 
  `suppr_date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00', `information` INT(11) NOT NULL DEFAULT 0, 
  `info_date` TIMESTAMP NULL DEFAULT NULL);";

// Création de la table conges_infos
$sql[]="CREATE TABLE `{$dbprefix}conges_infos` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `debut` DATE NULL, `fin` DATE NULL, `texte` TEXT NULL, `saisie` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);";

// Création de la table récupérations
$sql[]="CREATE TABLE `{$dbprefix}recuperations` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `perso_id` INT(11) NOT NULL, 
  `date` DATE NULL, `date2` DATE NULL, `heures` FLOAT(5), `etat` VARCHAR(20), `commentaires` TEXT, 
  `saisie` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `saisie_par` INT NOT NULL, `modif` INT(11) NOT NULL DEFAULT '0', 
  `modification` TIMESTAMP, `valide_n1` INT(11) NOT NULL DEFAULT 0, `validation_n1` DATETIME NULL DEFAULT NULL, `valide` INT(11) NOT NULL DEFAULT '0',`validation` TIMESTAMP, 
  `refus` TEXT, `solde_prec` FLOAT(10), `solde_actuel` FLOAT(10));";

// Menu
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,0,'Cong&eacute;s','plugins/conges/voir.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,10,'Liste des cong&eacute;s','plugins/conges/voir.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,20,'Poser des cong&eacute;s','plugins/conges/enregistrer.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,26,'R&eacute;cup&eacute;rations','plugins/conges/recuperations.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,30,'Informations','plugins/conges/infos.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`) VALUES (15,40,'Cr&eacute;dits','plugins/conges/credits.php');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`,`condition`) VALUES (15, 24, 'Poser des r&eacute;cup&eacute;rations', 'plugins/conges/recup_pose.php', 'config=Conges-Recuperations');";
$sql[]="INSERT INTO `{$dbprefix}menu` (`niveau1`,`niveau2`,`titre`,`url`,`condition`) VALUES (15, 15, 'Liste des r&eacute;cup&eacute;rations', 'plugins/conges/voir.php&amp;recup=1', 'config=Conges-Recuperations');";
      
// Modification de la table personnel
$sql[]="ALTER TABLE `{$dbprefix}personnel` ADD `conges_credit` FLOAT(10) DEFAULT 0, ADD `conges_reliquat` FLOAT(10) DEFAULT 0, ADD `conges_anticipation` FLOAT(10) DEFAULT 0, ADD `recup_samedi` FLOAT(10) DEFAULT 0, ADD `conges_annuel` FLOAT(10) DEFAULT 0;";

// Ajout des taches planifiées
$sql[]="INSERT INTO `{$dbprefix}cron` (m,h,dom,mon,dow,command,comments) VALUES (0,0,1,1,'*','plugins/conges/cron.jan1.php','Cron Cong&eacute;s 1er Janvier');";
$sql[]="INSERT INTO `{$dbprefix}cron` (m,h,dom,mon,dow,command,comments) VALUES (0,0,1,9,'*','plugins/conges/cron.sept1.php','Cron Cong&eacute;s 1er Septembre');";

// Inscription du plugin Congés dans la base
$sql[]="INSERT INTO `{$dbprefix}plugins` (`nom`,`version`) VALUES ('conges','$version');";

// Création de la table conges_cet
$sql[]="CREATE TABLE `{$dbprefix}conges_cet` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `perso_id` INT(11) NOT NULL, 
  `jours` INT(11) NOT NULL DEFAULT '0', `commentaires` TEXT, `saisie` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `saisie_par` INT NOT NULL, `modif` INT(11) NOT NULL DEFAULT '0', `modification` TIMESTAMP, `valide_n1` INT(11) NOT NULL DEFAULT '0', 
  `validation_n1` TIMESTAMP, `valide_n2` INT(11) NOT NULL DEFAULT '0',`validation_n2` TIMESTAMP, `refus` TEXT, 
  `solde_prec` FLOAT(10), `solde_actuel` FLOAT(10), `annee` VARCHAR(10));";

?>
<!-- Entête HTML -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Plugin Cong&eacute;s - Installation</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<?php
// Execution des requêtes
foreach ($sql as $elem) {
    $db=new db();
    $db->query($elem);
    if (!$db->error) {
        echo "$elem : <font style='color:green;'>OK</font><br/>\n";
    } else {
        echo "$elem : <font style='color:red;'>Erreur</font><br/>\n";
    }
}

echo "<br/><br/><a href='../../index.php'>Retour au planning</a>\n";
?>

</body>
</html>