/*
Planning Biblio, Plugin Congés Version 1.3.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/conges/js/script.conges.js
Création : 2 août 2013
Dernière modification : 19 septembre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier regroupant les fonctions JavaScript utiles à la gestion des congés
*/

function afficheRefus(me){
  if(me.value=="-1"){
    document.getElementById("tr_refus").style.display="";
  }
  else{
    document.getElementById("tr_refus").style.display="none";
  }
}

function calculCredit(){
  debut=document.form.elements["debut"].value;
  fin=document.form.elements["fin"].value;
  hre_debut=document.form.elements["hre_debut"].value;
  hre_fin=document.form.elements["hre_fin"].value;
  perso_id=document.form.elements["perso_id"].value;

  if(!fin){
    fin=debut;
    document.form.elements["fin"].value=fin;
  }
  if(!debut){
    alert("Veuillez saisir les dates de début et de fin");
    return;
  }
    
  hre_debut=hre_debut?hre_debut:"00:00:00";
  hre_fin=hre_fin?hre_fin:"23:59:59";
  
  tmp=file("index.php?page=plugins/conges/ajax.calculCredit.php&debut="+debut+"&fin="+fin+"&hre_debut="+hre_debut+"&hre_fin="+hre_fin+"&perso_id="+perso_id);
  tmp=tmp.split("###");
  msg=tmp[1];
  heures=tmp[3];
  tmp=heures.split(".");
  heures=tmp[0];
  minutes=tmp[1];
  document.form.elements["heures"].value=heures;
  document.form.elements["minutes"].value=minutes;
  if(msg=="error"){
    document.form.elements["heures"].value=0;
    document.form.elements["minutes"].value=0;
    alert("Impossible de calculer le nombre d'heures correspondant au congé demandé");
  }

  calculRestes();
}

function calculRestes(){
  heures=document.form.elements["heures"].value+"."+document.form.elements["minutes"].value;
  reliquat=document.form.elements["reliquat"].value;
  recuperation=document.form.elements["recuperation"].value;
  credit=document.form.elements["credit"].value;

  // Calcul du reliquat après décompte
  reste=0;
  reliquat=reliquat-heures;
  if(reliquat<0){
    reste=-reliquat;
    reliquat=0;
  }

  reste2=0;
  // Calcul du crédit de récupération
  if(document.form.elements["debit"].value=="recuperation"){
    recuperation=recuperation-reste;
    if(recuperation<0){
      reste2=-recuperation;
      recuperation=0;
    }
  }
  
  // Calcul du crédit de congés
  else if(document.form.elements["debit"].value=="credit"){
    credit=credit-reste;
    if(credit<0){
      reste2=-credit;
      credit=0;
    }
  }
  
  // Si après tous les débits, il reste des heures, on débit le crédit restant
  if(reste2){
    if(document.form.elements["debit"].value=="recuperation"){
      credit=credit-reste2;
    }
    else if(document.form.elements["debit"].value=="credit"){
      recuperation=recuperation-reste2;
    }
  }
  
  // Affichage
  document.getElementById("reliquat4").innerHTML=heure4(reliquat);
  document.getElementById("recup4").innerHTML=heure4(recuperation);
  document.getElementById("credit4").innerHTML=heure4(credit);
}

function recuperation(date){
  heures=$("#heures_"+date).val();
  recup=file("index.php?page=plugins/conges/ajax.recup.php&date="+date+"&heures="+heures);
  msg=recup.split("###");
  if(msg[1]=="OK"){
    $("#td_"+date).html("<b>Demande de récupération enregistrée.</b>");
  }
}

function valideConges(){
  document.form.elements["valide"].value="1";
  document.form.submit();
}

function verifRecup(){
  date=document.forms['form1'].elements['date'].value;
  heures=document.forms['form1'].elements['heures'].value;
  f=file("plugins/conges/ajax.verifRecup.php?date="+date+"&heures="+heures);
  tmp=f.split("###");
  if(tmp[1]=="Demande"){
    alert("Une demande de récupération est déjà enregistrée pour le "+dateFr(date));
    return false;
  }
  else{
    document.location.href="index.php?page=plugins/conges/recuperations.php&message="+tmp[1];
    return false;
  }
}