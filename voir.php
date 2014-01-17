<?php
/*
Planning Biblio, Plugin Congés Version 1.4
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/voir.php
Création : 24 juillet 2013
Dernière modification : 16 janvier 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant voir les congés
Accessible par le menu congés/Voir les congés ou par la page plugins/conges/index.php
Inclus dans le fichier index.php
*/

require_once "class.conges.php";
require_once "personnel/class.personnel.php";

// Initialisation des variables
// Gestion des congés niveaux 1 et 2
$admin=in_array(7,$droits)?true:false;
$admin=in_array(2,$droits)?true:$admin;

$annee=isset($_GET['annee'])?$_GET['annee']:(isset($_SESSION['oups']['conges_annee'])?$_SESSION['oups']['conges_annee']:(date("m")<9?date("Y")-1:date("Y")));
$congesAffiches=isset($_GET['congesAffiches'])?$_GET['congesAffiches']:(isset($_SESSION['oups']['congesAffiches'])?$_SESSION['oups']['congesAffiches']:"aVenir");
if($admin){
  $perso_id=isset($_GET['perso_id'])?$_GET['perso_id']:(isset($_SESSION['oups']['conges_perso_id'])?$_SESSION['oups']['conges_perso_id']:$_SESSION['login_id']);
}
else{
  $perso_id=$_SESSION['login_id'];
}
if(isset($_GET['reset'])){
  $annee=date("m")<9?date("Y")-1:date("Y");
  $perso_id=$_SESSION['login_id'];
}
$_SESSION['oups']['conges_annee']=$annee;
$_SESSION['oups']['congesAffiches']=$congesAffiches;
$_SESSION['oups']['conges_perso_id']=$perso_id;


$debut=$annee."-09-01";
$fin=($annee+1)."-08-31";

if($congesAffiches=="aVenir"){
  $debut=date("Y-m-d");
}

$c=new conges();
$c->debut=$debut;
$c->fin=$fin;
if($perso_id!=0){
  $c->perso_id=$perso_id;
}
$c->fetch();

// Recherche des agents
if($admin){
  $p=new personnel();
  $p->fetch();
  $agents=$p->elements;
}

// Années universitaires
$annees=array();
for($d=date("Y")+2;$d>date("Y")-11;$d--){
  $annees[]=array($d,$d."-".($d+1));
}

// Affichage du tableau
echo "<h3 class='print_only'>Liste des congés de ".nom($perso_id,"prenom nom").", année $annee-".($annee+1)."</h3>\n";
echo <<<EOD
<h3 class='noprint'>Liste des congés</h3>
<form name='form' method='get' action='index.php' class='noprint'>
<input type='hidden' name='page' value='plugins/conges/voir.php' />
Ann&eacute;e : <select name='annee'>
EOD;
foreach($annees as $elem){
  $selected=$annee==$elem[0]?"selected='selected'":null;
  echo "<option value='{$elem[0]}' $selected >{$elem[1]}</option>";
}
echo "</select>\n";

$selected=$congesAffiches=="aVenir"?"selected='selected'":null;
echo "&nbsp;&nbsp;Congés : ";
echo "<select name='congesAffiches'>";
echo "<option value='tous'>Tous</option>";
echo "<option value='aVenir' $selected>A venir</option>";
echo "</select>\n";

if($admin){
  echo "&nbsp;&nbsp;Agent : ";
  echo "<select name='perso_id'>";
  $selected=$perso_id==0?"selected='selected'":null;
  echo "<option value='0' $selected >Tous</option>";
  foreach($agents as $agent){
    $selected=$agent['id']==$perso_id?"selected='selected'":null;
    echo "<option value='{$agent['id']}' $selected >{$agent['nom']} {$agent['prenom']}</option>";
  }
  echo "</select>\n";
}
echo <<<EOD
&nbsp;&nbsp;<input type='submit' value='OK' id='button-OK' class='ui-button'/>
&nbsp;&nbsp;<input type='button' value='Effacer' onclick='location.href="index.php?page=plugins/conges/voir.php&reset"' class='ui-button'/>
</form>
<br/>
<table id='tableConges'>
<thead><tr><th>&nbsp;</th><th>Début</th><th>Fin</th>
EOD;
if($admin){
  echo "<th>Nom</th>";
}
echo "<th>Validation</th><th>Crédits</th><th>Reliquat</th><th>Récupérations</th><th>Anticipation</th></tr></thead>\n";
echo "<tbody>\n";

foreach($c->elements as $elem){
  $debut=str_replace("00h00","",dateFr($elem['debut'],true));
  $fin=str_replace("23h59","",dateFr($elem['fin'],true));
  $validation="Demand&eacute;";
  $validationStyle="font-weight:bold;";
  $credits=null;
  $recuperations=null;
  $reliquat=null;
  $anticipation=null;

  if($elem['valide']<0){
    $validation="Refus&eacute;, ".nom(-$elem['valide']).", ".dateFr($elem['validation'],true);
    $validationStyle="color:red;";
  }
  elseif($elem['valide'] or $elem['information']){
    $validation="Valid&eacute;, ".nom($elem['valide']).", ".dateFr($elem['validation'],true);
    $validationStyle=null;
    if($elem['solde_prec']!=null and $elem['solde_actuel']!=null){
      $credits=heure4($elem['solde_prec'])." &rarr; ".heure4($elem['solde_actuel']);
    }
    if($elem['recup_prec']!=null and $elem['recup_actuel']!=null){
      $recuperations=heure4($elem['recup_prec'])." &rarr; ".heure4($elem['recup_actuel']);
    }
    if($elem['reliquat_prec']!=null and $elem['reliquat_actuel']!=null){
      $reliquat=heure4($elem['reliquat_prec'])." &rarr; ".heure4($elem['reliquat_actuel']);
    }
    if($elem['anticipation_prec']!=null and $elem['anticipation_actuel']!=null){
      $anticipation=heure4($elem['anticipation_prec'])." &rarr; ".heure4($elem['anticipation_actuel']);
    }
  }
  elseif($elem['valideN1']){
    $validation="En attente de validation hi&eacute;rarchique,<br/>".dateFr($elem['validationN1'],true);
    $validationStyle="font-weight:bold;";
  }
  if($elem['information']){
    $nom=$elem['information']<999999999?nom($elem['information']).", ":null;	// >999999999 = cron
    $validation="Mise à jour des cr&eacute;dits, $nom".dateFr($elem['infoDate'],true);
    $validationStyle=null;
  }
  elseif($elem['supprime']){
    $validation="Supprim&eacute;, ".nom($elem['supprime']).", ".dateFr($elem['supprDate'],true);
    $validationStyle=null;
  }

  $nom=$admin?"<td>".nom($elem['perso_id'])."</td>":null;
  
  echo "<tr><td>";
  if($elem['supprime'] or $elem['information']){
    echo "&nbsp;";
  }
  else{
    echo "<a href='index.php?page=plugins/conges/modif.php&amp;id={$elem['id']}'/>";
    echo "<img src='img/modif.png' alt='Voir' border='0'/></a>";
  }
  echo "</td>";
  echo "<td>$debut</td><td>$fin</td>$nom<td style='$validationStyle'>$validation</td><td>$credits</td><td>$reliquat</td>";
  echo "<td>$recuperations</td><td>$anticipation</td></tr>\n";
}

?>
</tbody>
</table>

<script type='text/JavaScript'>
$(document).ready(function() {
  $(".datepicker").datepicker();

  $("#tableConges").dataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "bStateSave": true,
    "aaSorting" : [[1,"asc"],[2,"asc"]],
    "aoColumns" : [{"bSortable":false},{"sType": "date-fr"},{"sType": "date-fr-fin"},{"bSortable":true},{"bSortable":true},{"bSortable":true},
      {"bSortable":true},{"bSortable":true},
    <?php
    if($admin){
      echo "{\"bSortable\":true},";
    }
    ?>
      ],
    "aLengthMenu" : [[25,50,75,100,-1],[25,50,75,100,"Tous"]],
    "iDisplayLength" : 25,
    "oLanguage" : {"sUrl" : "js/dataTables/french.txt"}
  });
});
</script>