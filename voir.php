<?php
/*
Planning Biblio, Plugin Congés Version 1.3.4
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/voir.php
Création : 24 juillet 2013
Dernière modification : 3 octobre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant voir les congés
Accessible par le menu congés/Voir les congés ou par la page plugins/conges/index.php
Inclus dans le fichier index.php
*/

require_once "class.conges.php";
require_once "personnel/class.personnel.php";

// Initialisation des variables
$admin=in_array(2,$droits)?true:false;
$tri=isset($_GET['tri'])?$_GET['tri']:"`debut`,`fin`,`nom`,`prenom`";
$annee=isset($_GET['annee'])?$_GET['annee']:(isset($_SESSION['oups']['conges_annee'])?$_SESSION['oups']['conges_annee']:(date("m")<9?date("Y")-1:date("Y")));
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
$_SESSION['oups']['conges_perso_id']=$perso_id;

$debut=$annee."-09-01";
$fin=($annee+1)."-08-31";

$c=new conges();
$c->debut=$debut;
$c->fin=$fin;
$c->tri=$tri;
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
&nbsp;&nbsp;<input type='submit' value='OK' id='button-OK'/>
&nbsp;&nbsp;<input type='button' value='Reset' onclick='location.href="index.php?page=plugins/conges/voir.php&reset"' />
</form>
<br/>
<table class='tableauStandard'>
<tr class='th'><td>&nbsp;</td><td>Début</td><td>Fin</td>
EOD;
if($admin){
  echo "<td>Nom</td>";
}
echo "<td>Validation</td><td>Crédits</td><td>Reliquat</td><td>Récupérations</td><td>Anticipation</td></tr>\n";

$class="tr1";
foreach($c->elements as $elem){
  $debut=str_replace("00h00","",dateFr($elem['debut'],true));
  $fin=str_replace("23h59","",dateFr($elem['fin'],true));
  $validation="<b>En attente</b>";
  $credits=null;
  $recuperations=null;
  $reliquat=null;
  $anticipation=null;

  if($elem['valide']<0){
    $validation="<font style='color:red;font-weight:bold;'>Refus&eacute;, ".nom(-$elem['valide']).", ".dateFr($elem['validation'],true)."</font>";
  }
  elseif($elem['valide']){
    $validation=nom($elem['valide']).", ".dateFr($elem['validation'],true);
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
  $nom=$admin?"<td>".nom($elem['perso_id'])."</td>":null;
  $class=$class=="tr1"?"tr2":"tr1";
  echo <<<EOD
    <tr class='$class'>
      <td><a href='index.php?page=plugins/conges/modif.php&amp;id={$elem['id']}'/>
      <img src='img/modif.png' alt='Voir' border='0'/></a></td>
      <td>$debut</td><td>$fin</td>$nom<td>$validation</td><td>$credits</td><td>$reliquat</td><td>$recuperations</td><td>$anticipation</td>
      </tr>
EOD;
}

?>
</table>