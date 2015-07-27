<?php
/*
Planning Biblio, Plugin Conges Version 2.0
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2015 - Jérôme Combes

Fichier : plugins/conges/ajax.planning.updateCell.php
Création : 25 novembre 2014
Dernière modification : 27 juillet 2015
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier intégré au fichier planning/poste/ajax.updateCell.php permettant la mise à jour de la cellule modifiée
Vérifie si les agents de la cellule modifiée sont en congés et les marques si c'est le cas
*/

/*
Variables en entrée
  $site,	$ajouter,	$perso_id,	$perso_id_origine,	$date
  $debut,	$fin,		$absent,	$poste,			$barrer
  $tab :
    [0] => Array (
      [nom] => Nom
      [prenom] => Prénom
      [statut] => Statut
      [service] => Service
      [perso_id] => 86
      [absent] => 0
      [supprime] => 0
      )
    [1] => Array (
      ...

Variable modifiée
  $tab :
    [0] => Array (
      [nom] => Nom
      [prenom] => Prénom
      [statut] => Statut
      [service] => Service
      [perso_id] => 86
      [absent] => 0
      [supprime] => 0
      [conges] => 0/1
      )
    [1] => Array (
      ...
*/
$perso_ids=array();
foreach($tab as $elem){
  $perso_ids[]=$elem['perso_id'];
}
$perso_ids=join(",",$perso_ids);

$db=new db();
$db->select2("conges","*",array("debut"=>"<$date $fin", "fin"=>">$date $debut", "perso_id"=>"IN$perso_ids", "valide"=>">0"));

if($db->result){
  for($i=0;$i<count($tab);$i++){
    $tab[$i]['conges']=0;
    foreach($db->result as $elem){
      if($tab[$i]['perso_id']==$elem['perso_id']){
	$tab[$i]['conges']=1;
	continue;
      }
    }
  }
}
?>