<?php
/*
Planning Biblio, Plugin Congés Version 1.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/update.php
Création : 26 août 2013
Dernière modification : 26 août 2013
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
$sql[]="ALTER TABLE `{$dbprefix}conges` ADD `refus` TEXT;";

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