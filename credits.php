<?php
/*
Planning Biblio, Plugin Congés Version 1.5.6
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/credits.php
Création : 17 novembre 2014
Dernière modification : 19 novembre 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Affiche les crédits effectifs et prévisionnels de tous les agents
Accessible par le menu congés
Inclus dans le fichier index.php
*/

require_once "class.conges.php";
require_once "personnel/class.personnel.php";

// Initialisation des variables
// $annee=isset($_GET['annee'])?$_GET['annee']:(isset($_SESSION['oups']['conges_annee'])?$_SESSION['oups']['conges_annee']:(date("m")<9?date("Y")-1:date("Y")));

$agents_supprimes=isset($_SESSION['oups']['conges_agents_supprimes'])?$_SESSION['oups']['conges_agents_supprimes']:false;
$agents_supprimes=(isset($_GET['get']) and isset($_GET['supprimes']))?true:$agents_supprimes;
$agents_supprimes=(isset($_GET['get']) and !isset($_GET['supprimes']))?false:$agents_supprimes;
// $agents_supprimes=(isset($_GET['annee']) and isset($_GET['supprimes']))?true:$agents_supprimes;
// $agents_supprimes=(isset($_GET['annee']) and !isset($_GET['supprimes']))?false:$agents_supprimes;

// $perso_id=isset($_GET['perso_id'])?$_GET['perso_id']:(isset($_SESSION['oups']['conges_perso_id'])?$_SESSION['oups']['conges_perso_id']:$_SESSION['login_id']);

/*if(isset($_GET['reset'])){
  $annee=date("m")<9?date("Y")-1:date("Y");
  $perso_id=$_SESSION['login_id'];
  $agents_supprimes=false;
}
$_SESSION['oups']['conges_annee']=$annee;
$_SESSION['oups']['conges_perso_id']=$perso_id;*/
$_SESSION['oups']['conges_agents_supprimes']=$agents_supprimes;

// $debut=$annee."-09-01";
// $fin=($annee+1)."-08-31";

$c=new conges();
/*$c->debut=$debut;
$c->fin=$fin;*/
/*if($perso_id!=0){
  $c->perso_id=$perso_id;
}*/
if($agents_supprimes){
  $c->agents_supprimes=array(0,1);
}
$c->fetchAllCredits();

// Recherche des agents
// $p=new personnel();
// if($agents_supprimes){
//   $p->supprime=array(0,1);
// }
// $p->fetch();
// $agents=$p->elements;

// Années universitaires
// $annees=array();
// for($d=date("Y")+2;$d>date("Y")-11;$d--){
//   $annees[]=array($d,$d."-".($d+1));
// }

// Affichage des notifications
// if(isset($_GET['information'])){
//   echo "<script type='text/JavaScript'>information(\"{$_GET['information']}\",\"highlight\");</script>\n";
// }

// Affichage du tableau
echo <<<EOD
<h3>Cr&eacute;dits de cong&eacute;s</h3>
<form name='form' id='form' method='get' action='index.php' class='noprint'>
<input type='hidden' name='page' value='plugins/conges/credits.php' />
<input type='hidden' name='get' value='yes' />
<table class='tableauStandard'><tbody>
<tr>
EOD;
// echo "<td>Ann&eacute;e : <select name='annee'>\n";
// foreach($annees as $elem){
//   $selected=$annee==$elem[0]?"selected='selected'":null;
//   echo "<option value='{$elem[0]}' $selected >{$elem[1]}</option>";
// }
// echo "</select></td>\n";

echo "<td style='text-align:left;'>\n";
// echo "<span style='padding:5px;'>Agents : ";
// echo "<select name='perso_id' id='perso_id'>";
// $selected=$perso_id==0?"selected='selected'":null;
// echo "<option value='0' $selected >Tous</option>";
// foreach($agents as $agent){
//   $selected=$agent['id']==$perso_id?"selected='selected'":null;
//   echo "<option value='{$agent['id']}' $selected >{$agent['nom']} {$agent['prenom']}</option>";
// }
// echo "</select>\n";
// echo "</span>\n";

$checked=$agents_supprimes?"checked='checked'":null;

// echo "<br/>\n";
echo "<span style='padding:5px;'>Agents supprim&eacute;s : ";
// echo "<input type='checkbox' $checked name='supprimes' onclick='updateAgentsList(this,\"perso_id\");$(\"#form\").submit();'/>\n";
echo "<input type='checkbox' $checked name='supprimes' onclick='$(\"#form\").submit();'/>\n";
echo "</span>\n";
echo "</td>\n";

// echo <<<EOD
// <td><input type='submit' value='OK' id='button-OK' class='ui-button'/></td>
// <td><input type='button' value='Effacer' onclick='location.href="index.php?page=plugins/conges/credits.php&reset"' class='ui-button'/></td>
// EOD;

echo <<<EOD
</tr></tbody></table>
</form>
<br/>
<table id='tableCredits'>
<thead><tr>
<th>&nbsp;</th><th>Nom</th><th>Pr&eacute;nom</th><th>Cr&eacute;dits</th><th>R&eacute;cup&eacute;rations</th><th>Reliquat</th>
  <th>Solde d&eacute;biteur</th>
</tr></thead>
<tbody>
EOD;

foreach($c->elements as $elem){
  echo "<tr style='vertical-align:top;'><td>&nbsp;</td>";
  echo "<td>{$elem['nom']}</td>";
  echo "<td>{$elem['prenom']}</td>";
  echo "<td style='text-align:right;'>".heure4($elem['congesCredit'])."<br/><font class='orange'>".heure4($elem['credit_en_attente'])."</font></td>";
  echo "<td style='text-align:right;'>".heure4($elem['recupSamedi'])."<br/><font class='orange'>".heure4($elem['recup_en_attente'])."</font></td>";
  echo "<td style='text-align:right;'>".heure4($elem['congesReliquat'])."<br/><font class='orange'>".heure4($elem['reliquat_en_attente'])."</font></td>";
  echo "<td style='text-align:right;'>".heure4($elem['congesAnticipation'])."<br/><font class='orange'>".heure4($elem['anticipation_en_attente'])."</font></td></tr>\n";
}

?>
</tbody>
</table>