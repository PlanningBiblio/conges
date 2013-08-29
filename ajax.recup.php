<?php
/*
Planning Biblio, Plugin Congés Version 1.2
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/js/ajax.recup.php
Création : 28 août 2013
Dernière modification : 28 août 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier enregistrant une récupération dans la base de données
Appelé en arrière plan par la fonction JS "recuperation" (plugins/conges/js/script.conges.js)
*/

include "class.conges.php";

$c=new conges();
$c->enregistreRecup($_GET['date'],$_GET['heures']);
echo $c->error?"###error###":"###OK###";
?>