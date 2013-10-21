<?php
/*
Planning Biblio, Plugin Congés Version 1.3.4
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/class.conges.php
Création : 24 juillet 2013
Dernière modification : 21 octobre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier regroupant les fonctions utiles à la gestion des congés
Inclus dans les autres fichiers PHP du dossier plugins/conges
*/

// pas de $version=acces direct aux pages de ce dossier => redirection vers la page index.php
if(!$version){
  header("Location: ../../index.php");
}

$path=substr($_SERVER['SCRIPT_NAME'],-9)=="index.php"?null:"../../";
require_once "{$path}plugins/planningHebdo/class.planningHebdo.php";
require_once "{$path}joursFeries/class.joursFeries.php";
require_once "{$path}personnel/class.personnel.php";

class conges{
  public $agent=null;
  public $admin=false;
  public $debut=null;
  public $elements=array();
  public $error=false;
  public $fin=null;
  public $heures=null;
  public $heures2=null;
  public $id=null;
  public $message=null;
  public $minutes=null;
  public $perso_id=null;
  public $recupId=null;
  public $samedis=array();
  public $valide=null;

  public function conges(){
  }

  public function add($data){
    $data['fin']=$data['fin']?$data['fin']:$data['debut'];
    $data['debit']=isset($data['debit'])?$data['debit']:"credit";
    $data['hre_debut']=$data['hre_debut']?$data['hre_debut']:"00:00:00";
    $data['hre_fin']=$data['hre_fin']?$data['hre_fin']:"23:59:59";
    $data['heures']=$data['heures'].".".$data['minutes'];

    $insert=array("debut"=>$data['debut']." ".$data['hre_debut'], "fin"=>$data['fin']." ".$data['hre_fin'],
      "commentaires"=>$data['commentaires'],"heures"=>$data['heures'],"debit"=>$data['debit'],"perso_id"=>$data['perso_id']);
    $db=new db();
    $db->insert2("conges",$insert);
  }

  public function calculCredit($debut,$hre_debut,$fin,$hre_fin,$perso_id){
    // Calcul du nombre d'heures correspondant aux congés demandés
    $current=$debut;
    $difference=0;
    // Pour chaque date
    while($current<=$fin){

      // On ignore les jours de fermeture
      $j=new joursFeries();
      $j->fetchByDate($current);
      if(!empty($j->elements)){
	foreach($j->elements as $elem){
	  if($elem['fermeture']){
	    $current=date("Y-m-d",strtotime("+1 day",strtotime($current)));
	    continue 2;
	  }
	}
      }

      // On consulte le planning de présence de l'agent
      $p=new planningHebdo();
      $p->perso_id=$perso_id;
      $p->debut=$current;
      $p->fin=$current;
      $p->valide=true;
      $p->fetch();
      // Si le planning n'est pas validé pour l'une des dates, on affiche un message d'erreur et on arrête le calcul
      if(empty($p->elements)){
	$this->error=true;
	$this->message="Impossible de déterminer le nombre d'heures correspondant aux congés demandés.";
	break;
      }

      // Sinon, on calcule les heures d'absence
      $d=new datePl($current);
      $semaine=$d->semaine3;
      $jour=$d->position?$d->position:7;
      $jour=$jour+(($semaine-1)*7)-1;
      $temps=$p->elements[0]['temps'][$jour];
      $temps[0]=strtotime($temps[0]);
      $temps[1]=strtotime($temps[1]);
      $temps[2]=strtotime($temps[2]);
      $temps[3]=strtotime($temps[3]);
      $debutConges=$current==$debut?$hre_debut:"00:00:00";
      $finConges=$current==$fin?$hre_fin:"23:59:59";
      $debutConges=strtotime($debutConges);
      $finConges=strtotime($finConges);


      // Calcul du temps du matin
      if($temps[0] and $temps[1]){
	$debutConges1=$debutConges>$temps[0]?$debutConges:$temps[0];
	$finConges1=$finConges<$temps[1]?$finConges:$temps[1];
	if($finConges1>$debutConges1){
	  $difference+=$finConges1-$debutConges1;
	}
      }

      // Calcul du temps de l'après-midi
      if($temps[2] and $temps[3]){
	$debutConges2=$debutConges>$temps[2]?$debutConges:$temps[2];
	$finConges2=$finConges<$temps[3]?$finConges:$temps[3];
	if($finConges2>$debutConges2){
	  $difference+=$finConges2-$debutConges2;
	}
      }

      // Calcul du temps de la journée s'il n'y a pas de pause le midi
      if($temps[0] and $temps[3] and !$temps[1] and !$temps[2]){
	$debutConges=$debutConges>$temps[0]?$debutConges:$temps[0];
	$finConges=$finConges<$temps[3]?$finConges:$temps[3];
	if($finConges>$debutConges){
	  $difference+=$finConges-$debutConges;
	}
      }

      $current=date("Y-m-d",strtotime("+1 day",strtotime($current)));
    }
    $this->minutes=$difference/60;
    $this->heures=number_format($difference/3600, 2, '.', ' ');
    $this->heures2=str_replace(array(".00",".25",".50",".75"),array("h00","h15","h30","h45"),$this->heures);
  }


  public function enregistreRecup($date,$date2,$heures){
    // Enregistrement de la demande de récupération
    $perso_id=$_SESSION['login_id'];
    $db=new db();
    $db->delete("recuperations","`perso_id`='$perso_id' AND `date`='$date'");
    $db=new db();
    $db->insert2("recuperations",array("perso_id"=>$perso_id,"date"=>$date,"date2"=>$date2,"heures"=>$heures,"etat"=>"Demande"));


    // Envoi d'un e-mail à l'agent et aux responsables
    $destinataires=array();
    $p=new personnel();
    $p->fetchById($perso_id);
    $nom=$p->elements[0]['nom'];
    $prenom=$p->elements[0]['prenom'];
    $mail=$p->elements[0]['mail'];
    if(verifmail($mail)){
      $destinataires[]=$mail;
    }
    $this->getResponsables($date,$date,$perso_id);
    $responsables=$this->responsables;
    foreach($responsables as $elem){
      if(verifmail($elem['mail']) and !in_array($elem['mail'],$destinataires)){
	$destinataires[]=$elem['mail'];
      }
    }
    if(!empty($destinataires)){
      $destinataires=join(";",$destinataires);
      $sujet="Nouvelle demande de récupération";
      $message="Nouvelle demande de récupération<br/>$prenom $nom a fait une nouvelle demande de récupération";
      sendmail($sujet,$message,$destinataires);
    }
  }



  public function fetch(){
    // Filtre de recherche
    $filter="1";

    // Perso_id
    if($this->perso_id){
      $filter.=" AND `perso_id`='{$this->perso_id}'";
    }

    // Date, debut, fin
    $debut=$this->debut;
    $fin=$this->fin;
    $date=date("Y-m-d");
    if($debut){
      $fin=$fin?$fin:$date;
      $filter.=" AND `debut`<='$fin' AND `fin`>='$debut'";
    }
    else{
      $filter.=" AND `fin`>='$date'";
    }


    // Recherche des agents actifs seulement
    $perso_ids=array(0);
    $p=new personnel();
    $p->fetch("nom");
    foreach($p->elements as $elem){
      $perso_ids[]=$elem['id'];
    }

    // Recherche avec le nom de l'agent
    if($this->agent){
      $perso_ids=array(0);
      $p=new personnel();
      $p->fetch("nom",null,$this->agent);
      foreach($p->elements as $elem){
	$perso_ids[]=$elem['id'];
      }
    }

    // Filtre pour agents actifs seulement et recherche avec nom de l'agent
    $perso_ids=join(",",$perso_ids);
    $filter.=" AND `perso_id` IN ($perso_ids)";

    // Valide
    if($this->valide){
      $filter.=" AND `valide`<>0";
    }
  
    // Filtre avec ID, si ID, les autres filtres sont effacés
    if($this->id){
      $filter="`id`='{$this->id}'";
    }

    $db=new db();
    $db->select("conges","*",$filter,"ORDER BY debut,fin,saisie");
    if($db->result){
      $this->elements=$db->result;
    }
  }


  public function fetchCredit(){
    if(!$this->perso_id){
      $this->elements=array("credit"=>null,"reliquat"=>null,"anticipation"=>null,"recupSamedi"=>null);
    }
    else{
      $db=new db();
      $db->select("personnel","congesCredit,congesReliquat,congesAnticipation,recupSamedi","`id`='{$this->perso_id}'");
      if($db->result){
	$this->elements=array("credit"=>$db->result[0]['congesCredit'],"reliquat"=>$db->result[0]['congesReliquat'],"anticipation"=>$db->result[0]['congesAnticipation'],"recupSamedi"=>$db->result[0]['recupSamedi']);
      }
    }
  }

  public function getRecup(){
    $debut=$this->debut?$this->debut:date("Y-m-d",strtotime("-1 month",time()));
    $fin=$this->fin?$this->fin:date("Y-m-d",strtotime("+1 year",time()));
    $filter="`date` BETWEEN '$debut' AND '$fin'";

    // Recherche avec l'id de l'agent
    if($this->admin and $this->perso_id){
      $filter.=" AND `perso_id`='{$this->perso_id}'";
    }

    if(!$this->admin){
      $filter.=" AND perso_id='{$_SESSION['login_id']}'";
    }

    // Recherche des agents actifs seulement
    $perso_ids=array(0);
    $p=new personnel();
    $p->fetch("nom");
    foreach($p->elements as $elem){
      $perso_ids[]=$elem['id'];
    }

    // Recherche avec le nom de l'agent
    if($this->agent){
      $perso_ids=array(0);
      $p=new personnel();
      $p->fetch("nom",null,$this->agent);
      foreach($p->elements as $elem){
	$perso_ids[]=$elem['id'];
      }
    }

    // Filtre pour agents actifs seulement et recherche avec nom de l'agent
    $perso_ids=join(",",$perso_ids);
    $filter.=" AND `perso_id` IN ($perso_ids)";

    // Si recupId, le filtre est réinitialisé
    if($this->recupId){
      $filter="id='{$this->recupId}'";
    }

    $db=new db();
    $db->select("recuperations","*",$filter,"order by date,saisie");
    if($db->result){
      $this->elements=$db->result;
    }
  }

  public function getResponsables($debut=null,$fin=null,$perso_id){
    $responsables=array();
    $droitsConges=array();
    //	Si plusieurs sites et agents autorisés à travailler sur plusieurs sites, vérifions dans l'emploi du temps quels sont les sites concernés par le conges
    if($GLOBALS['config']['Multisites-nombre']>1 and $GLOBALS['config']['Multisites-agentsMultisites']){
      $db=new db();
      $db->select("personnel","temps","id='$perso_id'");
      $temps=unserialize($db->result[0]['temps']);
      $date=$debut;
      while($date<=$fin){
	// Vérifions le numéro de la semaine de façon à contrôler le bon planning de présence hebdomadaire
	$d=new datePl($date);
	$jour=$d->position?$d->position:7;
	$semaine=$d->semaine3;
	// Récupération du numéro du site concerné par la date courante
	$offset=$jour-1+($semaine*7)-7;
	if(array_key_exists($offset,$temps)){
	  $site=$temps[$offset][4];
	  // Ajout du numéro du droit correspondant à la gestion des congés de ce site
	  if(!in_array("30".$site,$droitsConges) and $site){
	    $droitsConges[]="30".$site;
	  }
	}
	$date=date("Y-m-d",strtotime("+1 day",strtotime($date)));
      }
      // Si les jours de conges ne concernent aucun site, on ajoute les responsables des 2 sites par sécurité
      if(empty($droitsConges)){
	$droitsConges=array(301,302);
      }
    }
    //	Si plusieurs sites et agents non autorisés à travailler sur plusieurs sites, vérifions dans les infos générales quels sont les sites concernés par le conges
    elseif($GLOBALS['config']['Multisites-nombre']>1 and !$GLOBALS['config']['Multisites-agentsMultisites']){
      $db=new db();
      $db->select("personnel","site","id='$perso_id'");
      $site=$db->result[0]['site'];
      $droitsConges=array("30".$site);
    }
    // Si un seul site, le droit de gestion des conges est 2
    else{
      $droitsConges[]=2;
    }

    $db=new db();
    $db->select("personnel");
    foreach($db->result as $elem){
      $d=unserialize($elem['droits']);
      foreach($droitsConges as $elem2){
	if(is_array($d)){
	  if(in_array($elem2,$d) and !in_array($elem,$responsables)){
	    $responsables[]=$elem;
	  }
	}
      }
    }
    $this->responsables=$responsables;
  }

  public function getSaturday(){
    // Liste des samedis des 2 derniers mois
    $perso_id=isset($this->perso_id)?$this->perso_id:$_SESSION['login_id'];
    $samedis=array();
    $current=date("Y-m-d");
    while($current>date("Y-m-d",strtotime("-2 month",time()))){
      $d=new datePl($current);
      if($d->position==6){
	$samedis[$current]=array("date"=>$current,"heures"=>0,"recup"=>null);
      }
      $current=date("Y-m-d",strtotime("-1 day",strtotime($current)));
    }

    // Pour chaque samedi
    foreach($samedis as $samedi){
      // Vérifions si l'agent a travaillé et récupérons les heures correspondantes
      $db=new db();
      $db->select("pl_poste","*","date='{$samedi['date']}' AND perso_id='$perso_id' AND absent='0'");
      $heures=0;
      if($db->result){
	foreach($db->result as $elem){
	  $heures+=diff_heures($elem['debut'],$elem['fin'],"decimal");
	}
      }
      $samedis[$samedi['date']]['heures']=number_format($heures, 2, '.', ' ');

      // Vérifions si une demande de récupération à déjà été faite
      $db=new db();
      $db->select("recuperations","*","date='{$samedi['date']}' AND perso_id='$perso_id'");
      if($db->result){
	$samedis[$samedi['date']]['recup']=$db->result[0]['etat'];
	$samedis[$samedi['date']]['valide']=$db->result[0]['valide'];
	$samedis[$samedi['date']]['heures_validees']=$db->result[0]['heures'];
      }
    }
  $this->samedis=$samedis;
  }

  public function suppression_agents($liste){
    $db=new db();
    $db->update2("personnel",
      array("congesCredit"=>null,"congesReliquat"=>null,"congesAnticipation"=>null,"congesAnnuel"=>null,"recupSamedi"=>null),
      "id IN ($liste)");
    $db=new db();
    $db->delete("conges","perso_id IN ($liste)");
    $db=new db();
    $db->delete("recuperations","perso_id IN ($liste)");
  }

  public function update($data){
    $data['debit']=isset($data['debit'])?$data['debit']:"credit";
    $data['hre_debut']=$data['hre_debut']?$data['hre_debut']:"00:00:00";
    $data['hre_fin']=$data['hre_fin']?$data['hre_fin']:"23:59:59";
    $data['heures']=$data['heures'].".".$data['minutes'];
    $data['commentaires']=htmlentities($data['commentaires'],ENT_QUOTES|ENT_IGNORE,"UTF-8",false);
    $data['refus']=htmlentities($data['refus'],ENT_QUOTES|ENT_IGNORE,"UTF-8",false);

    $update=array("debut"=>$data['debut']." ".$data['hre_debut'], "fin"=>$data['fin']." ".$data['hre_fin'],
      "commentaires"=>$data['commentaires'],"refus"=>$data['refus'],"heures"=>$data['heures'],"debit"=>$data['debit'],
      "perso_id"=>$data['perso_id'],"modif"=>$_SESSION['login_id'],"modification"=>date("Y-m-d H:i:s"));
    
    if($data['valide']){
      // Validation Niveau 2
      if($data['valide']==-1 or $data['valide']==1){
	$update["valide"]=$data['valide']*$_SESSION['login_id']; // login_id positif si accepté, négatif si refusé
	$update["validation"]=date("Y-m-d H:i:s");
      }
      // Validation Niveau 1
      elseif($data['valide']==-2 or $data['valide']==2){
	$update["valideN1"]=($data['valide']/2)*$_SESSION['login_id']; // login_id positif si accepté, négatif si refusé
	$update["validationN1"]=date("Y-m-d H:i:s");
	$update['valide']=0;
      }
    }
    else{
      $update['valide']=0;
    }

    $db=new db();
    $db->update2("conges",$update,array("id"=>$data['id']));
  
    // En cas de validation, on débite les crédits dans la fiche de l'agent et on barre l'agent s'il est déjà placé dans le planning
    if($data['valide']=="1" and !$db->error){
      // On débite les crédits dans la fiche de l'agent
      // Recherche des crédits actuels
      $p=new personnel();
      $p->fetchById($data['perso_id']);
      $credit=$p->elements[0]['congesCredit'];
      $reliquat=$p->elements[0]['congesReliquat'];
      $recuperation=$p->elements[0]['recupSamedi'];
      $anticipation=$p->elements[0]['congesAnticipation'];
      $heures=$data['heures'];
      
      // Mise à jour des compteurs dans la table conges
      $updateConges=array("solde_prec"=>$credit, "recup_prec"=>$recuperation, "reliquat_prec"=>$reliquat, "anticipation_prec"=>$anticipation);

      // Calcul du reliquat après décompte
      $reste=0;
      $reliquat=$reliquat-$heures;
      if($reliquat<0){
	$reste=-$reliquat;
	$reliquat=0;
      }
      $reste2=0;
      // Calcul du crédit de récupération
      if($data["debit"]=="recuperation"){
	$recuperation=$recuperation-$reste;
	if($recuperation<0){
	  $reste2=-$recuperation;
	  $recuperation=0;
	}
      }
      // Calcul du crédit de congés
      else if($data["debit"]=="credit"){
	$credit=$credit-$reste;
	if($credit<0){
	  $reste2=-$credit;
	  $credit=0;
	}
      }
      // Si après tous les débits, il reste des heures, on débit le crédit restant
      $reste3=0;
      if($reste2){
	if($data["debit"]=="recuperation"){
	  $credit=$credit-$reste2;
	  if($credit<0){
	    $reste3=-$credit;
	    $credit=0;
	  }
	}
	else if($data["debit"]=="credit"){
	  $recuperation=$recuperation-$reste2;
	  if($recuperation<0){
	    $reste3=-$recuperation;
	    $recuperation=0;
	  }
	}
      }

      if($reste3){
	$anticipation=floatval($anticipation)+$reste3;
      }

      // Mise à jour des compteurs dans la table personnel
      $updateCredits=array("congesCredit"=>$credit,"congesReliquat"=>$reliquat,"recupSamedi"=>$recuperation,"congesAnticipation"=>$anticipation);
      $db=new db();
      $db->update2("personnel",$updateCredits,array("id"=>$data["perso_id"]));

      // Mise à jour des compteurs dans la table conges
      $updateConges=array_merge($updateConges,array("solde_actuel"=>$credit,"reliquat_actuel"=>$reliquat,"recup_actuel"=>$recuperation,"anticipation_actuel"=>$anticipation));
      $db=new db();
      $db->update2("conges",$updateConges,array("id"=>$data['id']));

      // On barre l'agent s'il est déjà placé dans le planning
      $debut_sql=$data['debut']." ".$data['hre_debut'];
      $fin_sql=$data['fin']." ".$data['hre_fin'];
      $req="UPDATE `{$GLOBALS['dbprefix']}pl_poste` SET `absent`='2' WHERE
	((CONCAT(`date`,' ',`debut`) < '$fin_sql' AND CONCAT(`date`,' ',`debut`) >= '$debut_sql')
	OR (CONCAT(`date`,' ',`fin`) > '$debut_sql' AND CONCAT(`date`,' ',`fin`) <= '$fin_sql'))
	AND `perso_id`='{$data['perso_id']}'";

      $db=new db();
      $db->query($req);
    }

  }


}
?>