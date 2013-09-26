<?php
/*
Planning Biblio, Plugin Congés Version 1.3.2
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/recuperations.php
Création : 27 août 2013
Dernière modification : 25 septembre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de voir les demandes de récupération
*/

include_once "class.conges.php";
include_once "personnel/class.personnel.php";

// Initialisation des variables
$admin=in_array(2,$droits)?true:false;
$agent=isset($_GET['agent'])?$_GET['agent']:null;
$tri=isset($_GET['tri'])?$_GET['tri']:"`debut`,`fin`,`nom`,`prenom`";
$annee=isset($_GET['annee'])?$_GET['annee']:(isset($_SESSION['oups']['recup_annee'])?$_SESSION['oups']['recup_annee']:(date("m")<9?date("Y")-1:date("Y")));
if($admin){
  $perso_id=isset($_GET['perso_id'])?$_GET['perso_id']:(isset($_SESSION['oups']['recup_perso_id'])?$_SESSION['oups']['recup_perso_id']:$_SESSION['login_id']);
}
else{
  $perso_id=$_SESSION['login_id'];
}
if(isset($_GET['reset'])){
  $annee=date("m")<9?date("Y")-1:date("Y");
  $perso_id=$_SESSION['login_id'];
}
$_SESSION['oups']['recup_annee']=$annee;
$_SESSION['oups']['recup_perso_id']=$perso_id;

$debut=$annee."-09-01";
$fin=($annee+1)."-08-31";
$admin=in_array(2,$droits)?true:false;

// Recherche des demandes de récupérations enregistrées
$c=new conges();
$c->admin=$admin;
$c->debut=$debut;
$c->fin=$fin;
if($perso_id!=0){
  $c->perso_id=$perso_id;
}
$c->getRecup();
$recup=$c->elements;

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

// Notifications
if(isset($_GET['message'])){
  switch($_GET['message']){
    case "Demande-OK" : $message="Votre demande a été enregistrée"; $class="MessageOK";	break;
    case "Demande-Erreur" : $message="Une erreur est survenue lors de l'enregitrement de votre demande."; $class="MessageErreur"; break;
    case "OK" : $message="Vos modifications ont été enregistrées"; $class="MessageOK";	break;
    case "Erreur" : $message="Une erreur est survenue lors de la validation de vos modifications."; $class="MessageErreur"; break;
    case "Refus" : $message="Accès refusé."; $class="MessageErreur";	break;
  }
  if($message){
    echo "<div class='$class' id='information'>$message</div>\n";
    echo "<script type='text/JavaScript'>setTimeout(\"document.getElementById('information').style.display='none'\",3000);</script>\n";
  }
}

// Affichage
echo <<<EOD
<h3>Récupérations</h3>

<div id='liste'>
<h4>Liste des demandes de récupération</h4>
<form name='form' method='get' action='index.php'>
<input type='hidden' name='page' value='plugins/conges/recuperations.php' />
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
&nbsp;&nbsp;<input type='submit' value='OK' id='button-OK' />
&nbsp;&nbsp;<input type='button' value='Reset' id='button-Effacer' onclick='location.href="index.php?page=plugins/conges/recuperations.php&reset"' />
</form>
<table class='tableauStandard'>
<tr class='th'><td>&nbsp;</td>
EOD;
if($admin){
  echo "<td>Agent</td>";
}
echo "<td>Date</td><td>Heures</td><td>Commentaires</td><td>Validation</td><td>Crédits</td></tr>\n";

$class="tr1";
foreach($recup as $elem){
  $class=$class=="tr1"?"tr2":"tr1";
  $validation="En attente";
  $credits=null;
  if($elem['valide']>0){
    $validation=nom($elem['valide']).", ".dateFr($elem['validation'],true);
    if($elem['solde_prec']!=null and $elem['solde_actuel']!=null){
      $credits=heure4($elem['solde_prec'])." &rarr; ".heure4($elem['solde_actuel']);
    }

  }
  elseif($elem['valide']<0){
    $validation="<font style='color:red;font-weight:bold;'>Refus&eacute;, ".nom(-$elem['valide']).", ".dateFr($elem['validation'],true)."</font>";
  }

  echo "<tr class='$class'>";
  echo "<td><a href='index.php?page=plugins/conges/recuperation_modif.php&amp;id={$elem['id']}'><img src='img/modif.png' alt='Modifier' /></a></td>\n";
  if($admin){
    echo "<td>".nom($elem['perso_id'])."</td>";
  }
  echo "<td>".dateFr($elem['date'])."</td><td>".heure4($elem['heures'])."</td>";
  echo "<td>".str_replace("\n","<br/>",$elem['commentaires'])."</td><td>$validation</td><td>$credits</td></tr>\n";
}

echo <<<EOD
</table>
</div> <!-- liste -->

<br/><button id='dialog-button'>Nouvelle demande</button>

<div id="dialog-form" title="Nouvelle demande">
  <p class="validateTips">Veuillez sélectionner le jour concerné par votre demande et le nombre d'heures à récuperer et un saisir un commentaire.</p>
  <form>
  <fieldset>
    <table class='tableauFiches'>
EOD;
if($admin){
  echo <<<EOD
    <tr><td><label for="agent">Agent</label></td>
    <td><select id='agent' name='agent' style='text-align:center;'>
      <option value=''>&nbsp;</option>
EOD;
  foreach($agents as $elem){
    $selected=$elem['id']==$_SESSION['login_id']?"selected='selected'":null;
    echo "<option value='{$elem['id']}' $selected >".nom($elem['id'])."</option>\n";
  }
  echo "</select></td></tr>\n";
}
echo <<<EOD
    <tr><td><label for="date">Date</label></td>
    <td><input type="text" name="date" id="date" class="text ui-widget-content ui-corner-all datepicker"/></td></tr>
    <tr><td><label for="heures">Heures</label></td>
    <td><select id='heures' name='heures' style='text-align:center;'>
      <option value=''>&nbsp;</option>
EOD;
    for($i=0;$i<17;$i++){
      echo "<option value='{$i}.00' >{$i}h00</option>\n";
      echo "<option value='{$i}.25' >{$i}h15</option>\n";
      echo "<option value='{$i}.50' >{$i}h30</option>\n";
      echo "<option value='{$i}.75' >{$i}h45</option>\n";
    }
echo <<<EOD
      </select></td></tr>
      <tr><td><label for="commentaires">Commentaire</label></td>
      <td><textarea name="commentaires" id="commentaires" ></textarea></td></tr>
    </table>
  </fieldset>
  </form>
</div>
EOD;
?>

<script type='text/JavaScript'>
$(function() {
  var date = $( "#date" ),
    heures = $( "#heures" ),
    allFields = $( [] ).add( date ).add( heures );

  $( "#dialog-form" ).dialog({
    autoOpen: false,
    height: 450,
    width: 480,
    modal: true,
    buttons: {
      "Enregistrer": function() {
	var limitJours=7;
	var bValid = true;
	allFields.removeClass( "ui-state-error" );
 	bValid = bValid && checkRegexp( date, /^[0-9]{2}\/[0-9]{2}\/[0-9]{4}/i, "La date doit être au format JJ/MM/AAAA" );
	bValid = bValid && checkLength( heures, "heures", 4, 5 );
	bValid = bValid && checkDateAge( date, limitJours, "La demande de récupération doit être effectuée dans les "+limitJours+" jours");

	if ( bValid ) {
	  if(verifRecup()){
	    $( this ).dialog( "close" );
	  }
	}
      },

      Annuler: function() {
	$( this ).dialog( "close" );
      }
    },

    close: function() {
      allFields.val( "" ).removeClass( "ui-state-error" );
    }
  });

  $( "#dialog-button" )
    .button()
    .click(function() {
      date.datepicker("disable");
      $( "#dialog-form" ).dialog( "open" );
      date.datepicker("enable");
      return false;
    });

  // Champ date
  $( ".datepicker" ).datepicker();
  $( ".datepicker" ).datepicker("option", "dateFormat", "dd/mm/yy");

});
</script>