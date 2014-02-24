/*
Planning Biblio, Plugin Congés Version 1.4.5
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/js/script.conges.js
Création : 2 août 2013
Dernière modification : 24 février 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier regroupant les fonctions JavaScript utiles à la gestion des congés
*/

function afficheRefus(me){
  if(me.value=="-1" || me.value=="-2"){
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
    alert("Impossible de calculer le nombre d'heures correspondant au congé demandé.");
  }

  calculRestes();
}

function calculRestes(){
  heures=document.form.elements["heures"].value+"."+document.form.elements["minutes"].value;
  reliquat=document.form.elements["reliquat"].value;
  recuperation=document.form.elements["recuperation"].value;
  credit=document.form.elements["credit"].value;
  anticipation=document.form.elements["anticipation"].value;
  debit=document.form.elements["debit"].value
  jours=heures/7;
  $("#nbJours").text(jours.toFixed(2));

  // Calcul du reliquat après décompte
  reste=0;
  reliquat=reliquat-heures;
  if(reliquat<0){
    reste=-reliquat;
    reliquat=0;
  }

  reste2=0;
  // Calcul du crédit de récupération
  if(debit=="recuperation"){
    recuperation=recuperation-reste;
    if(recuperation<0){
      reste2=-recuperation;
      recuperation=0;
    }
  }
  
  // Calcul du crédit de congés
  else if(debit=="credit"){
    credit=credit-reste;
    if(credit<0){
      reste2=-credit;
      credit=0;
    }
  }
  
  // Si après tous les débits, il reste des heures, on débit le crédit restant
  reste3=0;
  if(reste2){
    if(debit=="recuperation"){
      credit=credit-reste2;
      if(credit<0){
	reste3=-credit;
	credit=0;
      }
    }
    else if(debit=="credit"){
      recuperation=recuperation-reste2;
      if(recuperation<0){
	reste3=-recuperation;
	recuperation=0;
      }
    }
  }
  
  if(reste3){
    anticipation=parseFloat(anticipation)+reste3;
  }
  
  // Affichage
  document.getElementById("reliquat4").innerHTML=heure4(reliquat);
  document.getElementById("recup4").innerHTML=heure4(recuperation);
  document.getElementById("credit4").innerHTML=heure4(credit);
  document.getElementById("anticipation4").innerHTML=heure4(anticipation);
}

function supprimeConges(){
  conf=confirm("Etes-vous sûr(e) de vouloir supprimer ce congé ?");
  if(conf){
    $.ajax({
      url: "plugins/conges/ajax.supprime.php",
      type: "get",
      data: "id="+$("#id").val(),
      success: function(){
	location.href="index.php?page=plugins/conges/voir.php";
      },
      error: function(){
	information("Une erreur est survenue lors de la suppresion du congé.","error");
      }
    });
  }
}

function valideConges(){
  document.form.elements["valide"].value="1";
  document.form.submit();
}

function verifConges(){
  // Variable, convertion des dates au format YYYY-MM-DD
  var debut=dateFr($("#debut").val());
  var fin=dateFr($("#fin").val());
  var hre_debut=$("#hre_debut_select").val();
  var hre_fin=$("#hre_fin_select").val();
  var perso_id=$("#perso_id").val();
  var id=$("#id").val();
  if(hre_fin==""){
    hre_fin="23:59:59";
  }
  // Vérifions si les dates sont correctement saisies
  if($("#debut").val()==""){
    information("Veuillez choisir la date de début","error");
    return false;
  }

  // Vérifions si les dates sont cohérentes
  if(debut+" "+hre_debut >= fin+" "+hre_fin){
    information("La date de fin doit être supérieure à la date de début","error");
    return false;
  }
    
  // Vérifions si un autre congé a été demandé ou validé
  var result=$.ajax({
    url: "plugins/conges/ajax.verifConges.php",
    type: "get",
    data: "perso_id="+perso_id+"&debut="+debut+"&fin="+fin+"&hre_debut="+hre_debut+"&hre_fin="+hre_fin+"&id="+id,
    success: function(){
      if(result.responseText != "Pas de congé"){
	information("Un congé a déjà été demandé "+result.responseText,"error");
      }else{
	$("#form").submit();
      }
    },
    error: function(){
      information("Une erreur est survenue lors de l'enregistrement du congé","error");
    },
  });
}

function verifRecup(o){
  var perso_id=$("#agent").val();

  f=file("plugins/conges/ajax.verifRecup.php?date="+o.val()+"&perso_id="+perso_id);
  tmp=f.split("###");
  if(tmp[1]=="Demande"){
    o.addClass( "ui-state-error" );
    updateTips( "Une demande a déjà été enregistrée pour le "+o.val()+"." );
    return false;
  }
  return true;
}


// Dialog, récupérations

function updateTips( t ) {
  var tips=$( ".validateTips" );
  tips
    .text( t )
    .addClass( "ui-state-highlight" );
  setTimeout(function() {
    tips.removeClass( "ui-state-highlight", 1500 );
  }, 500 );
}

function checkLength( o, n, min, max ) {
  if ( o.val().length > max || o.val().length < min ) {
    o.addClass( "ui-state-error" );
    updateTips( "Veuillez sélectionner le nombre d'heures.");
  return false;
  } else {
    return true;
  }
}

function checkRegexp( o, regexp, n ) {
  if ( !( regexp.test( o.val() ) ) ) {
    o.addClass( "ui-state-error" );
    updateTips( n );
    return false;
  } else {
    return true;
  }
}

function checkDate2( date1, date2, n ) {
  var d1=new Date();
  tmp=date1.val().split("/");
  d1.setDate(parseInt(tmp[0]));
  d1.setMonth(parseInt(tmp[1])-1);
  d1.setFullYear(parseInt(tmp[2]));

  var d2=new Date();
  tmp=date2.val().split("/");
  d2.setDate(parseInt(tmp[0]));
  d2.setMonth(parseInt(tmp[1])-1);
  d2.setFullYear(parseInt(tmp[2]));

  diff=dateDiff(d1,d2);
  if(diff.day<0){
    date2.addClass( "ui-state-error" );
    updateTips( n );
    return false;
  } else {
    return true;
  }
}

function checkDateAge( o, limit, n, tip ) {
  // Calcul de la différence entre aujourd'hui et la date demandée
  if(tip==undefined){
    tip=true;
  }
  var today=new Date();
  var d=new Date();
  tmp=o.val().split("/");
  d.setDate(parseInt(tmp[0]));
  d.setMonth(parseInt(tmp[1])-1);
  d.setFullYear(parseInt(tmp[2]));
  diff=dateDiff(d,today);
  if(diff.day>limit){
    if(tip){
      o.addClass( "ui-state-error" );
      updateTips( n );
    }
    return false;
  } else {
    return true;
  }
}

function checkSamedi( o, n ) {
  var d=new Date();
  tmp=o.val().split("/");
  d.setDate(parseInt(tmp[0]));
  d.setMonth(parseInt(tmp[1])-1);
  d.setFullYear(parseInt(tmp[2]));
  if(d.getDay()!=6){
    o.addClass( "ui-state-error" );
    updateTips( n );
    return false;
  } else {
    return true;
  }
}
