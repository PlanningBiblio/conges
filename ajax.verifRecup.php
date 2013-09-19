<?php
/*
Planning Biblio, Plugin Congés Version 1.3.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/ajax.verifRecup.php
Création : 18 septembre 2013
Dernière modification : 19 septembre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Vérifie si le jour demandé à déjà fait l'objet d'une demande de récuperation.
Dans le cas contraire, enregistre la demande.
Appelé en arrière plan par la fonction JS verifRecup()
*/

session_start();
$version="1.4";
include "../../include/config.php";
include "../../include/function.php";

$db=new db();
$db->select("recuperations",null,"`perso_id`='{$_SESSION['login_id']}' AND `date`='{$_GET['date']}'");
if($db->result){
  echo "###Demande###";
}
else{
  $db=new db();
  $db->insert2("recuperations",array("perso_id"=>$_SESSION['login_id'],"date"=>$_GET['date'],"heures"=>$_GET['heures']));
  if($db->error){
    echo "###Erreur###";
  }
  else{
    echo "###OK###";
  }
}
?>