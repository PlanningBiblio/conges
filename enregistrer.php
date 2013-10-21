<?php
/*
Planning Biblio, Plugin Congés Version 1.3.6
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/enregistrer.php
Création : 24 juillet 2013
Dernière modification : 3 octobre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de poser un congé
Accessible par le menu congés/Poser un congé ou par la page plugins/conges/index.php
Inclus dans le fichier index.php
*/

require_once "class.conges.php";
require_once "personnel/class.personnel.php";

// Initialisation des variables
$menu=isset($_GET['menu'])?$_GET['menu']:null;
$perso_id=isset($_GET['perso_id'])?$_GET['perso_id']:$_SESSION['login_id'];
if(!in_array(2,$droits)){
  $perso_id=$_SESSION['login_id'];
}
$debut=isset($_GET['debut'])?$_GET['debut']:null;
$fin=isset($_GET['fin'])?$_GET['fin']:null;
$quartDHeure=$config['heuresPrecision']=="quart d&apos;heure"?true:false;

echo <<<EOD
<h3>Poser des congés</h3>
<table border='0'>
<tr style='vertical-align:top'>
<td style='width:700px;'>
EOD;

if(isset($_GET['confirm'])){	// Confirmation
  // Initialisation des variables
  $fin=$fin?$fin:$debut;
  $hre_debut=$_GET['hre_debut']?$_GET['hre_debut']:"00:00:00";
  $hre_fin=$_GET['hre_fin']?$_GET['hre_fin']:"23:59:59";
  $commentaires=htmlentities($_GET['commentaires'],ENT_QUOTES|ENT_IGNORE,"UTF-8",false);

  // Récupération des adresses e-mails de l'agent et des responsables pour m'envoi des alertes
  $c=new conges();
  $c->getResponsables($debut,$fin,$perso_id);
  $responsables=$c->responsables;

  $db_perso=new db();
  $db_perso->query("select nom,prenom,mail from {$dbprefix}personnel where id=$perso_id;");
  $nom=$db_perso->result[0]['nom'];
  $prenom=$db_perso->result[0]['prenom'];
  $destinataires=$db_perso->result[0]['mail'];
  foreach($responsables as $elem){
    if(verifmail($elem['mail'])){
      $destinataires.=";{$elem['mail']}";
    }
  }

  $c=new conges();
  $c->add($_GET);

  $message="Nouveau congés: <br/>$prenom $nom<br/>Début : ".dateFr($debut);
  if($hre_debut!="00:00:00")
    $message.=" ".heure3($hre_debut);
  $message.="<br/>Fin : ".dateFr($fin);
  if($hre_fin!="23:59:59")
    $message.=" ".heure3($hre_fin);
  if($commentaires)
    $message.="Commentaire :<br/>$commentaires<br/>";
  sendmail("Nouveau congés",$message,$destinataires);
  if($menu=="off"){
    echo "<script type=text/JavaScript>parent.document.location.reload(false);</script>\n";
    echo "<script type=text/JavaScript>popup_closed();</script>\n";
  }
  else{
    echo "Le congé a été enregistré";
    echo "<br/><br/>";
    echo "<a href='index.php?page=plugins/conges/index.php'>Retour</a>";
  }
}

else{	// Formulaire
  // Initialisation des variables
  $perso_id=$perso_id?$perso_id:$_SESSION['login_id'];
  $p=new personnel();
  $p->fetchById($perso_id);
  $nom=$p->elements[0]['nom'];
  $prenom=$p->elements[0]['prenom'];
  $credit=number_format($p->elements[0]['congesCredit'], 2, '.', ' ');
  $reliquat=number_format($p->elements[0]['congesReliquat'], 2, '.', ' ');
  $anticipation=number_format($p->elements[0]['congesAnticipation'], 2, '.', ' ');
  $credit2=str_replace(array(".00",".25",".50",".75"),array("h00","h15","h30","h45"),$credit);
  $reliquat2=str_replace(array(".00",".25",".50",".75"),array("h00","h15","h30","h45"),$reliquat);
  $anticipation2=str_replace(array(".00",".25",".50",".75"),array("h00","h15","h30","h45"),$anticipation);
  $recuperation=number_format($p->elements[0]['recupSamedi'], 2, '.', ' ');
  $recuperation2=heure4($recuperation);

  // Affichage du formulaire
  echo "<form name='form' action='index.php' method='get' >\n";
  echo "<input type='hidden' name='page' value='plugins/conges/enregistrer.php' />\n";
  echo "<input type='hidden' name='menu' value='$menu' />\n";
  echo "<input type='hidden' name='confirm' value='confirm' />\n";
  echo "<input type='hidden' name='reliquat' value='$reliquat' />\n";
  echo "<input type='hidden' name='recuperation' value='$recuperation' />\n";
  echo "<input type='hidden' name='credit' value='$credit' />\n";
  echo "<input type='hidden' name='anticipation' value='$anticipation' />\n";
  echo "<table border='0'>\n";
  echo "<tr><td style='width:300px;'>\n";
  echo "Nom, prénom : \n";
  echo "</td><td>\n";
  if(in_array(2,$droits)){
    $db_perso=new db();
    $db_perso->query("select * from {$dbprefix}personnel where actif='Actif' order by nom,prenom;");
    echo "<select name='perso_id' onchange='document.location.href=\"index.php?page=plugins/conges/enregistrer.php&perso_id=\"+this.value;'>\n";
    foreach($db_perso->result as $elem){
      if($perso_id==$elem['id']){
	echo "<option value='".$elem['id']."' selected='selected'>".$elem['nom']." ".$elem['prenom']."</option>\n";
      }
      else{
	echo "<option value='".$elem['id']."'>".$elem['nom']." ".$elem['prenom']."</option>\n";
      }
    }
    echo "</select>\n";
  }
  else{
    echo "<input type='hidden' name='perso_id' value='{$_SESSION['login_id']}' />\n";
    echo $_SESSION['login_nom']." ".$_SESSION['login_prenom'];
  }
  echo "</td></tr>\n";
  echo "<tr><td style='padding-top:15px;'>\n";
  echo "Journée(s) entière(s) : \n";
  echo "</td><td style='padding-top:15px;'>\n";
  echo "<input type='checkbox' name='allday' checked='checked' onclick='all_day();'/>\n";
  echo "</td></tr>\n";
  echo "<tr><td>\n";
  echo "Date de début : \n";
  echo "</td><td>";
  echo "<input type='text' name='debut' value='$debut' />&nbsp;\n";
  echo "<img src='img/calendrier.gif' onclick='calendrier(\"debut\");' alt='début' />\n";
  echo "</td></tr>\n";
  echo "<tr id='hre_debut' style='display:none;'><td>\n";
  echo "Heure de début : \n";
  echo "</td><td>\n";
  echo "<select name='hre_debut' >\n";
  selectHeure(8,23,true,true);
  echo "</select>\n";
  echo "</td></tr>\n";
  echo "<tr><td>\n";
  echo "Date de fin : \n";
  echo "</td><td>";
  echo "<input type='text' name='fin' value='$fin' />&nbsp;\n";
  echo "<img src='img/calendrier.gif' onclick='calendrier(\"fin\");' alt='fin' />\n";
  echo "</td></tr>\n";
  echo "<tr id='hre_fin' style='display:none;'><td>\n";
  echo "Heure de fin : \n";
  echo "</td><td>\n";
  echo "<select name='hre_fin' >\n";
  selectHeure(8,23,true,true);
  echo "</select>\n";
  echo "</td></tr>\n";
  
  echo <<<EOD
    <tr><td style='padding-top:15px;'>Nombre d'heures : </td>
      <td style='padding-top:15px;'>
      <select name='heures' style='width:60px;' onchange='calculRestes();'>
EOD;
      for($i=0;$i<1000;$i++){
	echo "<option value='$i'>$i</option>\n";
      }
  echo <<<EOD
	</select>
      h
      <select name='minutes' style='width:60px;' onchange='calculRestes();'>
	<option value='00'>00</option>
	<option value='25'>15</option>
	<option value='50'>30</option>
	<option value='75'>45</option>
	</select>
      <input type='button' value='Calculer' onclick='calculCredit();'></td></tr>

  <tr><td colspan='2' style='padding-top:20px;'>
EOD;
  if($reliquat){
    echo "Ces heures seront débitées sur le réliquat de l'année précédente puis sur : ";
  }
  else{
    echo "Ces heures seront débitées sur : ";
  }
  echo <<<EOD
    </td></tr>
    <tr><td>&nbsp;</td>
    <td><select name='debit' style='width:100%;' onchange='calculRestes();'>
    <option value='recuperation'>Le crédit de récupérations</option>
    <option value='credit'>Le crédit de congés de l'année en cours</option>
    </select></td></tr>
  <tr><td colspan='2'>
    <table border='0'>
      <tr><td style='width:298px;'>Reliquat : </td><td style='width:130px;'>$reliquat2</td><td>(après débit : <font id='reliquat4'>$reliquat2</font>)</td></tr>
      <tr><td>Crédit de récupérations : </td><td>$recuperation2</td><td><font id='recup3'>(après débit : <font id='recup4'>$recuperation2</font>)</font></td></tr>
      <tr><td>Crédit de congés : </td><td>$credit2</td><td><font id='credit3'>(après débit : <font id='credit4'>$credit2</font>)</font></td></tr>
      <tr><td>Congés par anticipation : </td><td>$anticipation2</td><td><font id='anticipation3'>(après débit : <font id='anticipation4'>$anticipation2</font>)</font></td></tr>
    </table>
  </td></tr>
EOD;


  echo "<tr valign='top'><td style='padding-top:15px;'>\n";
  echo "Commentaires : \n";
  echo "</td><td style='padding-top:15px;'>\n";
  echo "<textarea name='commentaires' cols='16' rows='5' style='width:100%;'></textarea>\n";
  echo "</td></tr><tr><td>&nbsp;\n";
  echo "</td></tr><tr><td colspan='2'>\n";
  if($menu=="off"){
    echo "<input type='button' value='Annuler' onclick='popup_closed();' />";
  }
  else{
    echo "<input type='button' value='Annuler' onclick='document.location.href=\"index.php?page=plugins/conges/index.php\";' />";
  }
  echo "&nbsp;&nbsp;\n";
  echo "<input type='submit' value='Valider' />\n";

  echo "</td></tr></table>\n";
  echo "</form>\n";
}

echo "</td><td style='color:#FF5E0E;'>\n";

$date=date("Y-m-d");
$db=new db();
$db->query("SELECT * FROM `{$dbprefix}conges_infos` WHERE `fin`>='$date' ORDER BY `debut`,`fin`;");
if($db->result){
  echo "<b>Informations sur les congés :</b><br/><br/>\n";
  foreach($db->result as $elem){
    echo "Du ".dateFr($elem['debut'])." au ".dateFr($elem['fin'])." :<br/>".str_replace("\n","<br/>",$elem['texte'])."<br/><br/>\n";
  }
}
?>
</td></tr></table>