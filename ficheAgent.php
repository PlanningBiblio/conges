<?php
/*
Planning Biblio, Plugin Congés Version 1.3.9
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/ficheAgent.php
Création : 26 juillet 2013
Dernière modification : 13 août 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant d'ajouter les informations sur les congés dans la fiche des agents
Inclus dans le fichier personnel/modif.php
*/

require_once "class.conges.php";

// Recherche des informations sur les congés
$c=new conges();
$c->perso_id=$id;
$c->fetchCredit();
$conges=$c->elements;
$conges['credit']=heure4($conges['credit']);
$conges['reliquat']=heure4($conges['reliquat']);
$conges['anticipation']=heure4($conges['anticipation']);
$conges['recupSamedi']=heure4($conges['recupSamedi']);

$p=new personnel();
$p->fetchById($id);
$conges['annuel']=heure4($p->elements[0]['congesAnnuel']);

// Affichage

// Nombre d'heures de congés par an
echo "<tr><td>";
echo "Nombre d'heures de congés par an :";
echo "</td><td>";
if(in_array(21,$droits)){
  echo "<input type='text' name='congesAnnuel' value='{$conges['annuel']}'  style='width:400px'>\n";
}
else{
  echo $conges['credit'];
}
echo "</td></tr>";

// Crédit d'heures de congés
echo "<tr><td>";
echo "Crédit d'heures de congés actuel :";
echo "</td><td>";
if(in_array(21,$droits)){
  echo "<input type='text' name='congesCredit' value='{$conges['credit']}'  style='width:400px'>\n";
}
else{
  echo $conges['credit'];
}
echo "</td></tr>";

// Reliquat
echo "<tr><td>";
echo "Reliquat de congés :";
echo "</td><td>";
if(in_array(21,$droits)){
  echo "<input type='text' name='congesReliquat' value='{$conges['reliquat']}'  style='width:400px'>\n";
}
else{
  echo $conges['reliquat'];
}
echo "</td></tr>";

// Anticipation
echo "<tr><td>";
echo "Congés pris par anticipation :";
echo "</td><td>";
if(in_array(21,$droits)){
  echo "<input type='text' name='congesAnticipation' value='{$conges['anticipation']}'  style='width:400px'>\n";
}
else{
  echo $conges['anticipation'];
}
echo "</td></tr>";

// Anticipation
echo "<tr><td>";
echo "Récupération du samedi :";
echo "</td><td>";
if(in_array(21,$droits)){
  echo "<input type='text' name='recupSamedi' value='{$conges['recupSamedi']}'  style='width:400px'>\n";
}
else{
  echo $conges['recupSamedi'];
}
echo "</td></tr>";

?>