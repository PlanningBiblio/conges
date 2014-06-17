<?php
/*
Planning Biblio, Plugin Congés Version 1.5.3
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/infos.php
Création : 24 juillet 2013
Dernière modification : 12 juin 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Page permettant d'ajouter des informations relatives aux congés.
Accessible aux agents ayant le droit de gérer les congés.
Accessible par le menu congés/infos ou par la page plugins/conges/index.php
Appelée par le fichier index.php
*/

require_once "class.conges.php";

echo "<h3>Informations sur les congés</h3>\n";

// Initialisation des variables
$id=isset($_GET['id'])?$_GET['id']:null;

// Suppression
if(isset($_GET['suppression']) and isset($_GET['validation'])){
  $db=new db();
  $db->delete("conges_infos","id='$id'");
  echo "<b>L'information a été supprimée</b>";
  echo "<br/><br/><a href='index.php?page=plugins/conges/index.php'>Retour</a>\n";
}
elseif(isset($_GET['suppression'])){
  echo "<h4>Etes vous sûr de vouloir supprimer cette information ?</h4>\n";
  echo "<form method='get' action='#' name='form'>\n";
  echo "<input type='hidden' name='page' value='plugins/conges/infos.php'/>\n";
  echo "<input type='hidden' name='suppression' value='oui'/>\n";
  echo "<input type='hidden' name='validation' value='oui'/>\n";
  echo "<input type='hidden' name='id' value='".$_GET['id']."'/>\n";
  echo "<input type='button' value='Non' onclick='history.back();' class='ui-button'/>\n";
  echo "&nbsp;&nbsp;&nbsp;";
  echo "<input type='submit' value='Oui' class='ui-button'/>\n";
  echo "</form>\n";
}

// Validation du formulaire
elseif(isset($_GET['validation'])){
  echo "<b>Votre demande a été enregistrée</b>\n";
  echo "<br/><br/><a href='index.php?page=plugins/conges/index.php'>Retour</a>\n";
  $db=new db();
  if(isset($_GET['id']) and $_GET['id']!=null){
    $db->update2("conges_infos",array("debut"=>dateSQL($_GET['debut']),"fin"=>dateSQL($_GET['fin']),"texte"=>$_GET['texte']),array("id"=>$_GET['id']));
  }
  else{
    $db->insert2("conges_infos",array("debut"=>dateSQL($_GET['debut']),"fin"=>dateSQL($_GET['fin']),"texte"=>$_GET['texte']));
  }
}
// Vérification
elseif(isset($_GET['debut'])){
  $texte=htmlentities($_GET['texte'],ENT_QUOTES|ENT_IGNORE,"UTF-8");
  $_GET['fin']=$_GET['fin']?$_GET['fin']:$_GET['debut'];
  echo "<h4>Confirmation</h4>";
  echo "Du {$_GET['debut']} au {$_GET['fin']}<br/>";
  echo str_replace("\n","<br/>",$texte);
  echo "<br/><br/>";
  echo "<form method='get' action='index.php' name='form'>";
  echo "<input type='hidden' name='page' value='plugins/conges/infos.php'/>\n";
  echo "<input type='hidden' name='debut' value='".$_GET['debut']."'/>\n";
  echo "<input type='hidden' name='fin' value='".$_GET['fin']."'/>\n";
  echo "<input type='hidden' name='texte' value='$texte'/>\n";
  echo "<input type='hidden' name='id' value='".$_GET['id']."'/>\n";
  echo "<input type='hidden' name='validation' value='validation'/>\n";
  echo "<input type='button' value='Annuler' onclick='history.back();' class='ui-button'/>";
  echo "&nbsp;&nbsp;&nbsp;\n";
  echo "<input type='submit' value='Valider' class='ui-button'/>\n";
  echo "</form>";
}
// FIN Validation du formulaire
else{
  if(isset($_GET['id'])){
    $db=new db();
    $db->select("conges_infos","*","id='$id'");
    $debut=dateFr($db->result[0]['debut']);
    $fin=dateFr($db->result[0]['fin']);
    $texte=$db->result[0]['texte'];
    echo "<h4>Modifications des informations sur les congés</h4>\n";
  }
  else{
    $debut=null;
    $fin=null;
    $texte=null;
    $texte=null;
    echo "<h4>Ajout d'une information</h4>\n";
  }

  echo "
  <form method='get' action='index.php' name='form' onsubmit='return verif_form(\"debut=date1;fin=date2;texte\");'>\n
  <input type='hidden' name='page' value='plugins/conges/infos.php'/>\n
  <input type='hidden' name='id' value='$id'/>\n
  <table class='tableauFiches'>
  <tr><td>
  Date de début :
  </td><td>
  <input type='text' name='debut' value='$debut' class='datepicker'/>
  </td></tr><tr><td>
  Date de fin :
  </td><td>
  <input type='text' name='fin' value='$fin' class='datepicker'/>
  </td></tr><tr><td>
  Texte : 
  </td><td>
  <textarea name='texte' rows='3' cols='16'>$texte</textarea>
  </td></tr><tr><td>&nbsp;
  </td></tr>
  <tr><td colspan='2'>\n";
  if(isset($_GET['id'])){
    echo "<input type='button' value='Supprimer' onclick='document.location.href=\"index.php?page=plugins/conges/infos.php&amp;id=$id&amp;suppression=oui\";'  class='ui-button'/>\n";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
  }
  echo "<input type='button' value='Annuler' onclick='document.location.href=\"index.php?page=plugins/conges/index.php\";' class='ui-button'/>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <input type='submit' value='Valider' class='ui-button'/>
  </td></tr></table>
  </form>";
}
?>