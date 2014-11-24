<?php
/*
Planning Biblio, Plugin Congés Version 1.5.6
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/credits.php
Création : 17 novembre 2014
Dernière modification : 24 novembre 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Affiche les crédits effectifs et prévisionnels de tous les agents
Accessible par le menu congés
Inclus dans le fichier index.php
*/

require_once "class.conges.php";
require_once "personnel/class.personnel.php";

// Initialisation des variables
$agents_supprimes=isset($_SESSION['oups']['conges_agents_supprimes'])?$_SESSION['oups']['conges_agents_supprimes']:false;
$agents_supprimes=(isset($_GET['get']) and isset($_GET['supprimes']))?true:$agents_supprimes;
$agents_supprimes=(isset($_GET['get']) and !isset($_GET['supprimes']))?false:$agents_supprimes;

$credits_effectifs=isset($_SESSION['oups']['conges_credits_effectifs'])?$_SESSION['oups']['conges_credits_effectifs']:true;
$credits_effectifs=(isset($_GET['get']) and isset($_GET['effectifs']))?true:$credits_effectifs;
$credits_effectifs=(isset($_GET['get']) and !isset($_GET['effectifs']))?false:$credits_effectifs;

$credits_en_attente=isset($_SESSION['oups']['conges_credits_attente'])?$_SESSION['oups']['conges_credits_attente']:true;
$credits_en_attente=(isset($_GET['get']) and isset($_GET['attente']))?true:$credits_en_attente;
$credits_en_attente=(isset($_GET['get']) and !isset($_GET['attente']))?false:$credits_en_attente;

$_SESSION['oups']['conges_agents_supprimes']=$agents_supprimes;
$_SESSION['oups']['conges_credits_effectifs']=$credits_effectifs;
$_SESSION['oups']['conges_credits_attente']=$credits_en_attente;

$checked1=$agents_supprimes?"checked='checked'":null;
$checked2=$credits_effectifs?"checked='checked'":null;
$checked3=$credits_en_attente?"checked='checked'":null;

$c=new conges();
if($agents_supprimes){
  $c->agents_supprimes=array(0,1);
}
$c->fetchAllCredits();

// Affichage du tableau
echo <<<EOD
<h3>Cr&eacute;dits de cong&eacute;s</h3>

<form name='form' id='form' method='get' action='index.php' class='noprint'>
<input type='hidden' name='page' value='plugins/conges/credits.php' />
<input type='hidden' name='get' value='yes' />

<table class='tableauStandard'><tbody>
<tr>
  <td style='text-align:left;'>
    <span style='padding:5px 40px 5px 0;'>
    <input type='checkbox' $checked1 name='supprimes' onclick='$("#form").submit();'/>
    Agents supprim&eacute;s</span>
    <span style='padding:5px 40px 5px 0;'>
    <input type='checkbox' $checked2 name='effectifs' onclick='$("#form").submit();'/>
    Cr&eacute;dits effectifs</span>
    <span style='padding:5px 40px 5px 0;' class='orange'>
    <input type='checkbox' $checked3 name='attente' onclick='$("#form").submit();'/>
    Cr&eacute;dits en attente</span>
  </td>
</tr></tbody></table>

</form>
<br/>

<table id='tableCredits'>
<thead><tr>
  <th>&nbsp;</th><th>Nom</th><th>Pr&eacute;nom</th><th>Annuel</th><th>Cr&eacute;dits restant</th><th>R&eacute;cup&eacute;rations</th>
    <th>Reliquat</th><th>Solde d&eacute;biteur</th>
</tr></thead>
<tbody>
EOD;

foreach($c->elements as $elem){
  if($credits_effectifs){
    echo "<tr style='vertical-align:top;'><td>&nbsp;</td>";
    echo "<td>{$elem['nom']}</td>";
    echo "<td>{$elem['prenom']}</td>";
    echo "<td class='aRight'>".heure4($elem['congesAnnuel'])."</td>";
    echo "<td class='aRight'>".heure4($elem['congesCredit'])."</td>";
    echo "<td class='aRight'>".heure4($elem['recupSamedi'])."</td>";
    echo "<td class='aRight'>".heure4($elem['congesReliquat'])."</td>";
    echo "<td class='aRight'>".heure4($elem['congesAnticipation'])."</td></tr>\n";
  }

  if($credits_en_attente){
    echo "<tr style='vertical-align:top;' class='orange'><td>&nbsp;</td>";
    echo "<td><font>{$elem['nom']}</font></td>";
    echo "<td><font>{$elem['prenom']}</font></td>";
    echo "<td class='aRight'>".heure4($elem['congesAnnuel'])."</td>";
    echo "<td class='aRight'>".heure4($elem['credit_en_attente'])."</td>";
    echo "<td class='aRight'>".heure4($elem['recup_en_attente'])."</td>";
    echo "<td class='aRight'>".heure4($elem['reliquat_en_attente'])."</td>";
    echo "<td class='aRight'>".heure4($elem['anticipation_en_attente'])."</td></tr>";
  }
}

?>
</tbody>
</table>