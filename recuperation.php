<?php
/*
Planning Biblio, Plugin Congés Version 1.2
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/recuperation.php
Création : 27 août 2013
Dernière modification : 29 août 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de faire un demande de récupération
*/

// Includes
include_once "class.conges.php";
include_once "include/horaires.php";

// Initialisation des variables
$perso_id=$_SESSION['login_id'];

$c=new conges();
$c->perso_id=$perso_id;
$c->getSaturday();
$samedis=$c->samedis;

// Affichage
?>
<h3>Demande de récupération</h3>

<p>Vérifiez le nombre d'heures et cliquez sur "Récupérer"</p>
<form name='form' method='get' action='#'>
<table cellspacing='0' style='text-align:center;'>
<tr class='th'>
  <td style='width:250px;'>Date</td><td style='padding-left:10px;width:350px;'>Heures</td></tr>
<?php
$class="tr1";
foreach($samedis as $samedi){
  $class=$class=="tr1"?"tr2":"tr1";
  echo "<tr class='$class'><td>".dateAlpha($samedi['date'])."</td>";

  if($samedi['recup']){
    echo "<td><b>Demande de récupération enregistrée.</b></td>\n";
  }
  elseif($samedi['heures']==0){
    echo "<td>Vous n'avez pas travaillé ce samedi.</td>\n";
  }
  else{
    echo "<td id='td_{$samedi['date']}'>";
//     echo "<input type='text' value='".heure4($samedi['heures'])."' style='text-align:center;' id='heures_{$samedi['date']}'/>\n";
    echo "<select id='heures_{$samedi['date']}' name='heures_{$samedi['date']}' style='text-align:center;' >\n";
    echo "<option value=''>&nbsp;</option>\n";
    for($i=0;$i<17;$i++){
      $select1=$samedi['heures']=="{$i}.00"?"selected='selected'":null;
      $select2=$samedi['heures']=="{$i}.25"?"selected='selected'":null;
      $select3=$samedi['heures']=="{$i}.50"?"selected='selected'":null;
      $select4=$samedi['heures']=="{$i}.75"?"selected='selected'":null;
      echo "<option value='{$i}.00' $select1>{$i}h00</option>\n";
      echo "<option value='{$i}.25' $select2>{$i}h15</option>\n";
      echo "<option value='{$i}.50' $select3>{$i}h30</option>\n";
      echo "<option value='{$i}.75' $select4>{$i}h45</option>\n";
    }
    echo "</select>\n";
    echo "<input type='button' value='Récupérer' style='margin-left:20px;' onclick='recuperation(\"{$samedi['date']}\");'/></td>\n";
  }

  echo "</tr>\n";

}
?>
</table>
</form>

