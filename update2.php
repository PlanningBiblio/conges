<?php
/*
Planning Biblio, Plugin Congés Version 1.3.4
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/update.php
Création : 26 août 2013
Dernière modification : 9 octobre 2013
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

$version="1.3.5";
include_once "../../include/config.php";

$sql=array();

// Modification du 3 octobre 2013
$sql[]="ALTER TABLE `{$dbprefix}conges` ADD `anticipation_prec` FLOAT(10), ADD `anticipation_actuel` FLOAT(10);";
$sql[]="ALTER TABLE `{$dbprefix}personnel` CHANGE `congesCredit` `congesCredit` FLOAT(10), CHANGE `congesReliquat` `congesReliquat` FLOAT(10), CHANGE `congesAnticipation` `congesAnticipation` FLOAT(10);";
$sql[]="ALTER TABLE `{$dbprefix}personnel` CHANGE `recupSamedi` `recupSamedi` FLOAT(10);";
$sql[]="ALTER TABLE `{$dbprefix}personnel` CHANGE `congesAnnuel` `congesAnnuel` FLOAT(10);";
$sql[]="DELETE FROM `{$dbprefix}acces` WHERE `page`='plugins/conges/recuperation.php';";
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