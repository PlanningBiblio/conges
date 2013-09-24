<?php
/*
Planning Biblio, Plugin Congés Version 1.3.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/recuperations.php
Création : 27 août 2013
Dernière modification : 24 septembre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de voir les demandes de récupération
*/

include_once "class.conges.php";
include_once "personnel/class.personnel.php";

// Initialisation des variables
$agent=isset($_GET['agent'])?$_GET['agent']:null;
$tri=isset($_GET['tri'])?$_GET['tri']:"`debut`,`fin`,`nom`,`prenom`";
$debut=isset($_GET['debut'])?$_GET['debut']:(isset($_SESSION['recup_debut'])?$_SESSION['recup_debut']:null);
$fin=isset($_GET['fin'])?$_GET['fin']:(isset($_SESSION['recup_fin'])?$_SESSION['recup_fin']:null);
$agent=isset($_GET['agent'])?$_GET['agent']:(isset($_SESSION['recup_agent'])?$_SESSION['recup_agent']:null);
$_SESSION['recup_debut']=$debut;
$_SESSION['recup_fin']=$fin;
$_SESSION['recup_agent']=$agent;
$admin=in_array(2,$droits)?true:false;

// Recherche des demandes de récupérations enregistrées
$c=new conges();
$c->admin=$admin;
$c->debut=$debut;
$c->fin=$fin;
$c->agent=$agent;
$c->getRecup();
$recup=$c->elements;

// Recherche de tous les agents pour le menu déroulant des admins
$p=new personnel();
$p->fetch();
$agents=$p->elements;

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
Début : <input type='text' name='debut' value='$debut' />&nbsp;<img src='img/calendrier.gif' onclick='calendrier("debut");' alt='calendrier' />
&nbsp;&nbsp;Fin : <input type='text' name='fin' value='$fin' />&nbsp;<img src='img/calendrier.gif' onclick='calendrier("fin");' alt='calendrier' />
EOD;
if($admin){
  echo "&nbsp;&nbsp;Agent : <input type='text' name='agent' value='$agent' />\n";
}
echo <<<EOD
&nbsp;&nbsp;<input type='submit' value='OK' id='button-OK' />
&nbsp;&nbsp;<input type='button' value='Effacer' id='button-Effacer' onclick='location.href="index.php?page=plugins/conges/recuperations.php"' />
</form>
<table class='tableauStandard'>
<tr class='th'><td>&nbsp;</td>
EOD;
if($admin){
  echo "<td>Agent</td>";
}
echo "<td>Date</td><td>Heures</td><td>Commentaires</td><td>Validation</td></tr>\n";

$class="tr1";
foreach($recup as $elem){
  $class=$class=="tr1"?"tr2":"tr1";
  $validation="En attente";
  if($elem['valide']>0){
    $validation=nom($elem['valide']).", ".dateFr($elem['validation'],true);
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
  echo "<td>".str_replace("\n","<br/>",$elem['commentaires'])."</td><td>$validation</td></tr>\n";
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
    height: 420,
    width: 460,
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