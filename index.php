<?php
/*
Planning Biblio, Plugin Congés Version 1.3.7
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/index.php
Création : 24 juillet 2013
Dernière modification : 25 septembre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier Index du dossier congés, Affiche les liens vers les autres pages du dossier
Accessible par le menu congés
Inclus dans le fichier index.php
*/

require_once "class.conges.php";
?>

<h3>Les congés</h3>
<table>
<tr style='vertical-align:top;'>
<td style='width:400px;'>
<ul style='margin-top:0px;'>
<li><a href='index.php?page=plugins/conges/voir.php'>Liste des congés</a></li>
<li><a href='index.php?page=plugins/conges/enregistrer.php'>Poser des congés</a></li>
<li><a href='index.php?page=plugins/conges/recuperations.php'>R&eacute;cup&eacute;rations</a></li>
<li><a href='index.php?page=plugins/conges/infos.php'>Informations</a></li>
<?php
$admin=in_array(2,$droits)?true:false;
if($admin){
  echo "<li><a href='index.php?page=plugins/conges/infos.php'>Ajouter une information</a></li>\n";
}
echo "</ul>\n";
echo "</td>\n";
echo "<td style='color:#FF5E0E;'>\n";

$date=date("Y-m-d");
$db=new db();
$db->query("SELECT * FROM `{$dbprefix}conges_infos` WHERE `fin`>='$date' ORDER BY `debut`,`fin`;");
if($db->result){
  echo "<b>Informations sur les congés :</b><br/><br/>\n";
  foreach($db->result as $elem){
    if($admin){
      echo "<a href='index.php?page=plugins/conges/infos.php&amp;id={$elem['id']}'><img src='img/modif.png' border='0' alt='modifier' /></a>&nbsp;";
    }
    echo "Du ".dateFr($elem['debut'])." au ".dateFr($elem['fin'])." : <br/>".str_replace("\n","<br/>",$elem['texte'])."<br/><br/>\n";
  }	
}
?>
</td></tr></table>