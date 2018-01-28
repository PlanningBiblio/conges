<?php
/**
Planning Biblio, Plugin Congés Version 2.8
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
@copyright 2013-2018 Jérôme Combes

Fichier : plugins/conges/credits.php
Création : 17 novembre 2014
Dernière modification : 28 janvier 2018
@author Jérôme Combes <jerome@planningbiblio.fr>

Description :
Affiche les crédits effectifs et prévisionnels de tous les agents
Accessible par le menu congés
Inclus dans le fichier index.php
*/

// Refuse l'accès aux personnes n'ayant pas le droit de gérer les congés et récupération des sites gérés
/*
40x : Congés validation niveau 1
60x : Congés validation niveau 2
*/

// Gestion des droits d'administration
// NOTE : Ici, pas de différenciation entre les droits niveau 1 et niveau 2
// NOTE : Les agents ayant les droits niveau 1 ou niveau 2 sont admin ($admin, droits 40x et 60x)
// TODO : différencier les niveau 1 et 2 si demandé par les utilisateurs du plugin

$admin = false;
$sites = array();

for($i = 1; $i <= $config['Multisites-nombre']; $i++ ){
  if(in_array((400+$i), $droits) or in_array((600+$i), $droits)){
    $admin = true;
    $sites[] = $i;
  }
}

if(!$admin){
  echo "<div id='acces_refuse'>Accès refusé</div>\n";
  include "include/footer.php";
  exit;
}

// Includes
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
if($config['Multisites-nombre']>1){
  $c->sites=$sites;
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

<table id='tableCredits' class='CJDataTable' data-sort='[[0]]'>
<thead>
  <tr>
    <th rowspan='2'>Agent</th>
    <th rowspan='2' class='dataTableHeureFR' >Cong&eacute;s / an</th>
    <th colspan='3'>Cr&eacute;dit cong&eacute;s</th>
    <th colspan='3'>Cr&eacute;dit reliquat</th>
    <th colspan='3'>Cr&eacute;dit r&eacute;cup&eacute;rations</th>
    <th colspan='3'>Solde d&eacute;biteur</th>
  </tr>
  <tr>
    <th class='dataTableHeureFR' >Initial</th>
    <th class='dataTableHeureFR' >Utilis&eacute;</th>
    <th class='dataTableHeureFR' >Restant</th>
    <th class='dataTableHeureFR' >Initial</th>
    <th class='dataTableHeureFR' >Utilis&eacute;</th>
    <th class='dataTableHeureFR' >Restant</th>
    <th class='dataTableHeureFR' >Initial</th>
    <th class='dataTableHeureFR' >Utilis&eacute;</th>
    <th class='dataTableHeureFR' >Restant</th>
    <th class='dataTableHeureFR' >Initial</th>
    <th class='dataTableHeureFR' >Utilis&eacute;</th>
    <th class='dataTableHeureFR' >Restant</th>
  </tr>
</thead>
<tbody>
EOD;

foreach($c->elements as $elem){
  if($credits_effectifs){
    echo "<tr style='vertical-align:top;'>\n";
    echo "<td>{$elem['agent']}</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['conge_annuel'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['conge_initial'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['conge_utilise'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['conge_restant'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['reliquat_initial'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['reliquat_utilise'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['reliquat_restant'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['recup_initial'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['recup_utilise'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['recup_restant'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['anticipation_initial'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['anticipation_utilise'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['anticipation_restant'])."</td></tr>\n";
  }

  if($credits_en_attente){
    echo "<tr style='vertical-align:top;' class='orange'>\n";
    echo "<td>{$elem['agent']}</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['conge_annuel'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['conge_initial'])."</td>\n";
    echo "<td class='aRight nowrap {$elem['conge_classe']}'>".heure4($elem['conge_demande'])."</td>\n";
    echo "<td class='aRight nowrap {$elem['conge_classe']}'>".heure4($elem['conge_en_attente'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['reliquat_initial'])."</td>\n";
    echo "<td class='aRight nowrap {$elem['reliquat_classe']}'>".heure4($elem['reliquat_demande'])."</td>\n";
    echo "<td class='aRight nowrap {$elem['reliquat_classe']}'>".heure4($elem['reliquat_en_attente'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['recup_initial'])."</td>\n";
    echo "<td class='aRight nowrap {$elem['recup_classe']}'>".heure4($elem['recup_demande'])."</td>\n";
    echo "<td class='aRight nowrap {$elem['recup_classe']}'>".heure4($elem['recup_en_attente'])."</td>\n";
    echo "<td class='aRight nowrap'>".heure4($elem['anticipation_initial'])."</td>\n";
    echo "<td class='aRight nowrap {$elem['anticipation_classe']}'>".heure4($elem['anticipation_demande'])."</td>\n";
    echo "<td class='aRight nowrap {$elem['anticipation_classe']}'>".heure4($elem['anticipation_en_attente'])."</td></tr>\n";
  }
}

?>
</tbody>
</table>