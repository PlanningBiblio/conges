<?php
/*
Planning Biblio, Plugin Congés Version 2.6.4
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
@copyright 2013-2017 Jérôme Combes

Fichier : plugins/conges/uninstall.php
Création : 24 juillet 2013
Dernière modification : 21 avril 2017
@author Jérôme Combes <jerome@planningbiblio.fr>

Description :
Fichier permettant la désinstallation du plugin Congés. Supprime les informations LDAP de la base de données
*/

session_start();

ini_set('display_errors','on');
error_reporting(999);

$confirm = filter_input(INPUT_POST, 'confirm', FILTER_SANITIZE_NUMBER_INT);
$transfer = filter_input(INPUT_POST, 'transfer', FILTER_SANITIZE_NUMBER_INT);

$version="1.4.5";
include_once "../../include/config.php";

?>

<!-- Entête HTML -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Plugin Congés - Désinstallation</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<h3>Désintallation du plugin "Cong&eacute;s"</h3>

<?php

// Sécurité : compte admin seulement
if($_SESSION['login_id']!=1){
  echo <<<EOD
  <p>
  <h3>Vous devez vous connecter au planning<br/>avec le login "admin" pour pouvoir d&eacute;sinstaller ce plugin.</h3>
  <a href='../../index.php'>Retour au planning</a>
  </p>
EOD;
  exit;
}

// Demande de confirmation
if(!$confirm){
  echo <<<EOD
  <p>
  Vous &ecirc;tes sur le point de d&eacute;sintaller le plugin cong&eacute;s.<br/>
  Voulez-vous continuer ?
  </p>
  <p>
  <form method='post' action='uninstall.php' name='form'>
  <input type='hidden' name='confirm' value='1' />
  <input type='button' value='Retour au planning' onclick='location.href="../../index.php";' />
  <input type='submit' value='Continuer la d&eacute;sinstallation' style='margin-left:20px;' />
  </form>
  </p>
EOD;
  exit;
}

// Demande si transfert des congés dans la table absence
if($confirm == 1 and !$transfer){
  echo <<<EOD
  <p>
  Voulez-vous transf&eacute;rer les cong&eacute;s enregistr&eacute;s dans la table "absence"
  </p>
  <p>
  <form method='post' action='uninstall.php' name='form'>
  <input type='hidden' name='confirm' value='2' />
  <input type='hidden' name='transfer' value='1' />
  <input type='button' value='Non' onclick='document.forms["form"].elements["transfer"].value="0"; document.form.submit();' />
  <input type='submit' value='Oui' style='margin-left:20px;' />
  </form>
  </p>
EOD;
  exit;
}

// Transfert des données
if($confirm == 2 and $transfer){
  echo "<p><strong>Transfert des donn&eacute;es</strong></p>";
  
  // récupération des congés
  $db = new db();
  $db->select2('conges',null,array('supprime'=>0, 'information'=>0));
  $insert = array();
  
  // Préparation des requêtes
  $req = "INSERT INTO `{$dbprefix}absences` (`perso_id`, `debut`, `fin`, `commentaires`, `demande`, `valide`, `validation`, `valide_n1`, `validation_n1`, `motif`, `motif_autre`) 
    VALUES (:perso_id, :debut, :fin, :commentaires, :demande, :valide, :validation, :valide_n1, :validation_n1, :motif, :motif_autre);";
  $dbh=new dbh();
  $dbh->prepare($req);

  echo $req."<br/>\n";
  
  if($db->result){
    foreach($db->result as $elem){
      $insert[] = array(':perso_id'=>$elem['perso_id'], ':debut'=>$elem['debut'], ':fin'=>$elem['fin'], ':commentaires'=>$elem['commentaires'], ':demande'=>$elem['saisie'], ':valide'=>$elem['valide'], 
        ':validation'=>$elem['validation'], ':valide_n1'=>$elem['valide_n1'], ':validation_n1'=>$elem['validation_n1'], ':motif'=>'Cong&eacute;s Pay&eacute;s', ':motif_autre'=>'Cong&eacute;s Pay&eacute;s');
    }
    
      
    
    // Execution des requêtes
    foreach($insert as $elem){
      $dbh->execute($elem);
      print_r($elem);
      if(!$dbh->error){
        echo " : <font style='color:green;'>OK</font><br/>\n";
      }else{
        echo " : <font style='color:red;'>Erreur</font><br/>\n";
      }
    }
  }
}

// Désintallation
if($confirm == 2){
  echo "<p><strong>Suppression du plugin</strong></p>";
  
  $sql=array();
  
  // Droits d'accès
  $sql[]="DELETE FROM `{$dbprefix}acces` WHERE `page` LIKE 'plugins/conges%';";
  $sql[]="DELETE FROM `{$dbprefix}acces` WHERE `groupe` = 'Gestion des cong&eacute;s';";

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
  $sql[]="DELETE FROM `{$dbprefix}config` WHERE `nom`='Recup-DelaiDefaut';";



  // Execution des requêtes
  foreach($sql as $elem){
    $db=new db();
    $db->query($elem);
    if(!$db->error)
      echo "$elem : <font style='color:green;'>OK</font><br/>\n";
    else
      echo "$elem : <font style='color:red;'>Erreur</font><br/>\n";
  }
}

echo "<br/><br/><a href='../../index.php'>Retour au planning</a>\n";
?>

</body>
</html>