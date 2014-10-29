<?php
/*
Planning Biblio, Plugin Congés Version 1.5.5
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/ajax.updateAgentsList.php
Création : 29 octobre 2014
Dernière modification : 29 octobre 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Met à jour la liste des agents dans la page plugins/conges/voir.php
Affiche dans cette liste les agents supprimés ou non en fonction de la variable $_GET['deleted']
Appelé en Ajax via la fonction updateAgentsList à partir de la page voir.php
*/

ini_set('display_errors',0);
error_reporting(0);

include "../../include/config.php";
include "../../personnel/class.personnel.php";

$p=new personnel();
if($_GET['deleted']=="yes"){
  $p->supprime=array(0,1);
}
$p->fetch();
$p->elements;

$tab=array();
foreach($p->elements as $elem){
  $tab[]=array("id"=>$elem['id'],"nom"=>$elem['nom'],"prenom"=>$elem['prenom']);
}
  
echo json_encode($tab);
?>