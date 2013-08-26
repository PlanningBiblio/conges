<?php
/*
Planning Biblio, Plugin Congés Version 1.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/voir.php
Création : 24 juillet 2013
Dernière modification : 1er août 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant voir les congés
Accessible par le menu congés/Voir les congés ou par la page plugins/conges/index.php
Inclus dans le fichier index.php
*/

require_once "class.conges.php";

// Initialisation des variables
$agent=isset($_GET['agent'])?$_GET['agent']:null;
$tri=isset($_GET['tri'])?$_GET['tri']:"`debut`,`fin`,`nom`,`prenom`";
$debut=isset($_GET['debut'])?$_GET['debut']:null;
$fin=isset($_GET['fin'])?$_GET['fin']:null;
$admin=in_array(2,$droits)?true:false;

$c=new conges();
$c->agent=$agent;
$c->debut=$debut;
$c->fin=$fin;
$c->tri=$tri;
if(!$admin){
  $c->perso_id=$_SESSION['login_id'];
}
$c->fetch();

// Affichage du tableau
echo <<<EOD
<h3>Liste des congés</h3>
<form name='form' method='get' action='index.php'>
<input type='hidden' name='page' value='plugins/conges/voir.php' />
Début : <input type='text' name='debut' value='$debut' />&nbsp;<img src='img/calendrier.gif' onclick='calendrier("debut");' alt='calendrier' />
&nbsp;&nbsp;Fin : <input type='text' name='fin' value='$fin' />&nbsp;<img src='img/calendrier.gif' onclick='calendrier("fin");' alt='calendrier' />
EOD;
if($admin){
  echo "&nbsp;&nbsp;Agent : <input type='text' name='agent' value='$agent' />\n";
}
echo <<<EOD
&nbsp;&nbsp;<input type='submit' value='OK' />
&nbsp;&nbsp;<input type='button' value='Effacer' onclick='location.href="index.php?page=plugins/conges/voir.php"' />
</form>
<table border='0' cellspacing='0' style='width:100%;margin-top:20px;'>
<tr class='th' style='vertical-align:top;text-align:center;'><td>&nbsp;</td><td>Début</td><td>Fin</td>
EOD;
if($admin){
  echo "<td>Nom</td>";
}
echo "<td>Commentaires</td><td>Validation</td></tr>\n";

$class="tr1";
foreach($c->elements as $elem){
  $debut=str_replace("00h00","",dateFr($elem['debut'],true));
  $fin=str_replace("23h59","",dateFr($elem['fin'],true));
  $validation="<b>N'est pas validé</b>";
  if($elem['valide']<0){
    $validation="<font style='color:red;font-weight:bold;'>Refus&eacute;, ".nom(-$elem['valide']).", ".dateFr($elem['validation'],true)."</font>";
  }
  elseif($elem['valide']){
    $validation=nom($elem['valide']).", ".dateFr($elem['validation'],true);
  }
  $nom=$admin?"<td>".nom($elem['perso_id'])."</td>":null;
  $class=$class=="tr1"?"tr2":"tr1";
  echo <<<EOD
    <tr class='$class' style='text-align:center;'>
      <td><a href='index.php?page=plugins/conges/modif.php&amp;id={$elem['id']}'/>
      <img src='img/modif.png' alt='Voir' border='0'/></a></td>
      <td>$debut</td><td>$fin</td>$nom<td>{$elem['commentaires']}</td><td>$validation</td>
      </tr>
EOD;
}

echo <<<EOD
</table>
EOD;

?>
