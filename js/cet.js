/*
Planning Biblio, Plugin Congés Version 1.4.5
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/conges/js/cet.js
Création : 6 mars 2014
Dernière modification : 11 mars 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier regroupant les functions JavaScript utiles à la gestion des CET (page plugins/conges/cet.php)
*/

$(document).ready(function(){
  // DataTable
  aoColumns=[{"bSortable":false},{"bSortable":true},{"sType": "date-fr"},{"bSortable":true},{"bSortable":true},{"bSortable":true}];
  if($("#adminN1").val()==1){
    aoColumns[aoColumns.length]={"bSortable":true};
  }
  $("#tableCET").dataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "bStateSave": true,
    "aaSorting" : [[1,"asc"],[2,"asc"]],
    "aoColumns" : aoColumns,
    "aLengthMenu" : [[25,50,75,100,-1],[25,50,75,100,"Toutes"]],
    "iDisplayLength" : 25,
    "oLanguage" : {"sUrl" : "js/dataTables/french.txt"}
  });

  
  // DialogBox
  // Affichage du reliquat dans la boite de dialogue au chargment de la page
  cetReliquat($("#perso_id").val());

  var commentaires = $( "#cet-commentaires" ),
    id=$("#cet-id"),
    agent=$("#cet-agent"),
    jours=$("#cet-jours"),
    validation=$("#cet-validation"),
    allFields = $([]).add(agent).add(jours).add(validation).add(id);

  $( "#cet-dialog-form" ).dialog({
    autoOpen: false,
    height: 480,
    width: 650,
    modal: true,
    buttons: {
      "Enregistrer": function() {
	// Calcul du delai limit pour la demande de récup en fonction de la catégorie de l'agent
	var adminN1=$("#adminN1").val();
	if($("#cet-agent option:selected").val()){
	  perso_id=$("#cet-agent option:selected").val();
	}else{
	  perso_id=$("#perso_id").val();
	}

	var bValid = true;
	allFields.removeClass( "ui-state-error" );
	commentaires.removeClass( "ui-state-error" );

	if(adminN1){
	  bValid = bValid && checkInt(agent, "cet-agent", 1, 99999, "Veuillez sélectionner un agent.");
	}
	
	bValid = bValid && checkInt(jours, "cet-jours", 1, 99999, "Veuillez sélectionner le nombre de jours.");

	if ( bValid ) {
	  // Enregistre la demande
	  $.ajax({
	    url: "plugins/conges/ajax.enregistreCET.php",
	    type: "get",
	    data: "id="+id.val()+"&perso_id="+perso_id+"&jours="+jours.val()+"&commentaires="+commentaires.val()+"&validation="+validation.val(),
	    success: function(){
	      location.href="index.php?page=plugins/conges/cet.php&message=Demande-OK";
	      $( this ).dialog( "close" );
	    },
	    error: function(){
	      updateTips("Une erreur est survenue lors de l'enregistrement de votre demande");
	    }
	  });
	}
      },

      Annuler: function() {
	$( this ).dialog( "close" );
      }
    },

    close: function() {
      allFields.val("").removeClass( "ui-state-error" );
      commentaires.html("").removeClass( "ui-state-error" );
    }
  });

  $("#cet-dialog-button")
    .click(function() {
      $("#cet-reliquat").html("&nbsp;");
      $("#cet-jours").html("");
      $( "#cet-dialog-form" ).dialog( "open" );
      return false;
    });
    
    
  // Affichage du reliquat dans la boite de dialogue lors du changement du select agent
  $("#cet-agent").change(function(){
    cetReliquat($("#cet-agent option:selected").val());
  });

});

function cetReliquat(perso_id){
  $.ajax({
    url: "plugins/conges/ajax.getReliquat.php",
    type: "get",
    data: "perso_id="+perso_id,
    success: function(result){
      data=JSON.parse(result);
      $("#cet-reliquat").text(data["reliquatHeures"]+"h (soit "+data["reliquatJours"]+" jours)");
      options=["<option value='0'>&nbsp;</option>"];
      for(i=1;i<=parseInt(data["reliquatJours"]);i++){
	options.push("<option value='"+i+"'>"+i+"</option>");
      }
      options.join(" ");
      $("#cet-jours").html(options);
    }
  });
}

// Affiche les informations d'un CET dans le formulaire afin de le modifier ou de le valider
function getCET(id){
  $.ajax({
    url: "plugins/conges/ajax.getCET.php",
    type: "get",
    data: "id="+id,
    success: function(result){
      data=JSON.parse(result);
      $("#cet-id").val(id);
      if($("#cet-agent").length){
	$("#cet-agent").val(data["perso_id"]);
      }
      if($("#adminN1").val()==1){
	cetReliquat($("#cet-agent option:selected").val());
      }else{
	cetReliquat($("#perso_id").val());
      }
      jours=data["jours"];
      setTimeout(function(){$("#cet-jours").val(jours);},500);
      $("#cet-commentaires").html(data["commentaires"]);
      $("#cet-dialog-form").dialog("open");
    }
  });
}