<?php
/*
Planning Biblio, Plugin Congés Version 1.3
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/recuperations.php
Création : 27 août 2013
Dernière modification : 30 août 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de voir les demandes de récupération
*/

include_once "class.conges.php";

// Initialisation des variables
$admin=in_array(2,$droits)?true:false;

$c=new conges();
$c->admin=$admin;
$c->getRecup();
$recup=$c->elements;

// Notifications
if(isset($_GET['message'])){
  switch($_GET['message']){
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
<h3>Récupérations des samedis</h3>

<table class='tableauStandard'>
<tr class='th'><td>&nbsp;</td><td>Date</td><td>Agent</td><td>Heures</td><td>Validation</td></tr>
EOD;
$class="tr1";
foreach($recup as $elem){
  $class=$class=="tr1"?"tr2":"tr1";
  $validation="En attente";
  if($elem['valide']>0){
    $validation=nom($elem['valide']).", ".dateFr($elem['validation'],true);
  }
  elseif($elem['valide']<0){
    $validation="Refusé, ".nom(-$elem['valide']).", ".dateFr($elem['validation']);
  }

  echo "<tr class='$class'>";
  echo "<td><a href='index.php?page=plugins/conges/recuperation_modif.php&amp;id={$elem['id']}'><img src='img/modif.png' alt='Modifier' /></a></td>\n";
  echo "<td>".dateFr($elem['date'])."</td><td>".nom($elem['perso_id'])."</td><td>".heure4($elem['heures'])."</td><td>$validation</td></tr>\n";
}

?>
</table>