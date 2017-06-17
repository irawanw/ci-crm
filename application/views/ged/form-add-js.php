<script>
	function generateClientCombobox() {
	var container = $('#insert-clientName');
	container.attr("disabled", true);

	$.get("<?php echo site_url('ged/get_client');?>", function(data) {
		container.attr("disabled", false);
		if(data) {
			var options = '<option>Aucun</option>';
			for(var i= 0; i < data.length; i++) {
				options += '<option value="'+data[i].id+'">'+data[i].value+'</option>';
			}

			container.html(options);
		} else {
			container.html('<option value=""></option>');
		}
	},"json");
}

function generateUserCombobox() {
	var container1 = $('#insert-createdby');
	var container2 = $('#insert-uploadedby');
	container1.attr("disabled", true);
	container2.attr("disabled", true);

	$.get("<?php echo site_url('ged/get_user');?>", function(data) {
		container1.attr("disabled", false);
		container2.attr("disabled", false);
		if(data) {
			var options = '<option>Aucun</option>';
			for(var i= 0; i < data.length; i++) {
				options += '<option value="'+data[i].id+'">'+data[i].value+'</option>';
			}

			container1.html(options);
			container2.html(options);
		} else {
			container1.html('<option value=""></option>');
			container2.html('<option value=""></option>');
		}
	},"json");
}

function  generateDevisCombobox() {
	//reset combobox devis & facture	
	$('#insert-facture').html("<option></option>");
	$('#insert-devis').html("<option></option>");

	var clientId = $('#insert-clientName').val();
	var container = $('#insert-devis');

	container.attr("disabled", true);
	if(clientId !== "") {
		$.get("<?php echo site_url('ged/get_devis');?>/" + clientId, function(data) {
			container.attr("disabled", false);
			if(data) {
				var options = '<option>Aucun</option>';
				for(var i= 0; i < data.length; i++) {
					options += '<option value="'+data[i].id+'">'+data[i].value+'</option>';					
				}

				container.html(options);
			} else {
				container.html('<option value=""></option>');
			}		
		},"json");
	} else {
		container.html('<option value=""></option>');
	}
}

function  generateFacturesCombobox() {
	var devisId = $('#insert-devis').val();
	var container = $('#insert-facture');

	container.attr("disabled", true);
	if(devisId !== "") {
		$.get("<?php echo site_url('ged/get_facture');?>/" + devisId, function(data) {
			container.attr("disabled", false);
			if(data) {
				var options = '<option>Aucun</option>';
				for(var i= 0; i < data.length; i++) {
					options += '<option value="'+data[i].id+'">'+data[i].value+'</option>';					
				}

				container.html(options);
			} else {
				container.html('<option value=""></option>');
			}		
		},"json");
	}else {
		container.html('<option value=""></option>');
	}
}

function showSubFormDocumentType(type) {
	$('.subform-documenttype').hide();
	$('.subform').hide();
	$('.subform-soustype-infographie').show();
	
	$('.subform').find('input, select, textarea').val("");

	switch(type) {
		case 'Infographie':
			$('.subform-infographie').show();
			break;
		case 'Plan':
			$('.subform-soustype-plan').show();
			break;
		case 'Piece Comptable':
			$('.subform-soustype-piece-comptable').show();
			break;
		case 'Facture Client':
			$('.subform-societe').show();
			$('.subform-numero').show();
			break;
		case 'Devis Client':
			$('.subform-societe').show();
			$('.subform-numero').show();
			break;
		case 'E-Mailing':			
			$('.subform-bass-de-donnee').show();		
			$('.subform-message').show();
			break;
		case 'Site Internet':
			$('.subform-infographie').show();
			$('.subform-soustype-infographie').hide();			
			$('.subform-soustype-site-internet').show();
			break;
		case 'Administratif Divers':
			$('.subform-soustype-administratifs-divers').show();
			break;
		case 'Developpement':
			$('.subform-infographie').show();
			$('.subform-soustype-infographie').hide();
			$('.submform-devis').hide();
			$('.subform-facture').hide();
			$('.subform-soustype-development').show();
			$('.subform-societe').show();
			break;
		case 'Ressources Humaines':
			$('.subform-soustype-ressources-humaines').show();
			break;
		case 'Commercial':
			$('.subform-soustype-commercial').show();
			break;		
		default:
			break;
	}
}

function showSubFormSousType(value) {
	$('.subform-soustype').hide();
	$('.nombre-pages').hide();
	$('.subform-soustype').find('input').val("");

	switch(value) {
	    case 'Depliant':
	        $('.subform-format').show();
	        break;
	    case 'Affiche':
	        $('.subform-largeur-longueur').show();
	        break;
	    case 'Bache':
	        $('.subform-largeur-longueur').show();
	        break;
	    case 'Carte de Visite':
	        $('.subform-largeur-longueur').show();
	        break;
	    case 'Akilux':
	        $('.subform-largeur-longueur').show();
        break;
	    case 'Panneau':
	        $('.subform-largeur-longueur').show();
	        break;
	    case 'Logo':
	        $('.subform-largeur-longueur').show();
	        break;
	    case 'Brochure':
	        $('.subform-largeur-longueur').show();
	        $('.nombre-pages').show();
	        break;
	    case 'Enveloppes':
	        $('.subform-largeur-longueur').show();
	        break;
	    case 'Drapeau':
	        $('.subform-largeur-longueur').show();
	        break;
	    case 'Blocs':
	        $('.subform-largeur-longueur').show();
	        break;
	    case 'Auto-Collant':
	        $('.subform-largeur-longueur').show();
	        break;
	    case 'Chemise':
	        $('.subform-largeur-longueur').show();
	        break;
	    case 'E-Mailing':
	        $('.subform-emailing').show();
	        break;
	    case 'Site Internet':
	        $('.subform-siteinternet').show();
	        break;	
	    case 'Photo':
	        $('.subform-photo').show();
	        break;    
	    default:
	        break;
	}
}

function showSubFormSousTypePlan(value) {
	$('.subform-ville').hide();
	$('.subform-numero').hide();
	$('.subform-mois').hide();

	$('#insert-ville').val("");
	$('#insert-numero').val("");
	$('#insert-mois').val("");

	switch(value) {
		case 'Plan General':
			$('.subform-ville').show();
			break;
		case 'Secteur':
			$('.subform-ville').show();
			$('.subform-numero').show();
			break;
		case 'Plan General Plaintes Ponctuelles':
			$('.subform-ville').show();
			$('.subform-mois').show();
			break;
		case 'Plan General Plaintes Permanentes':
			$('.subform-ville').show();			
			break;
		case 'Secteur Plaintes Permanentes':
			$('.subform-ville').show();
			$('.subform-numero').show();
			break;
		default:
			break;
	}
}

function showSubFormSousTypePieceComptable(value) {
	$('.subform-societe').hide();
	$('.subform-banque').hide();
	$('.subform-numero').hide();
	$('.subform-mois').hide();

	$('#insert-societe').val("");
	$('#insert-numero').val("");
	$('#insert-banque').val("");
	$('#insert-mois').val("");

	switch(value) {
		case 'Facture Fournisseur':
			$('.subform-societe').show();
			$('.subform-numero').show();
			break;
		case 'Releve De Compte':			
			$('.subform-banque').show();
			$('.subform-mois').show();
			break;
		default:
			break;
	}
}

function showSubFormSousTypeSiteInternet(value) {
	$('.subform-origine').hide();
	$('.subform-sujet').hide();

	$('#insert-origine-general').val("");
	$('#insert-sujet-general').val("");

	switch(value) {
		case 'Photo':
			$('.subform-origine').show();
			$('.subform-sujet').show();
			break;
		default:
			break;
	}
}

function showSubFormSousTypeAdministratifsDivers(value) {
	$('.subform-annee').hide();
	$('.subform-societe').hide();
	$('.subform-assemblees').hide();
	$('.subform-nom-organisme').hide();
	$('.subform-statuts-assemblees-type').hide();
	$('.subform-sous-traitants-type').hide();
	$('.subform-nom-organisme').hide();

	$('.insert-annee').val("");
	$('.insert-societe').val("");
	$('.insert-assemblees').val("");
	$('.insert-nom-organisme').val("");
	$('.insert-statuts-assemblees-type').val("");
	$('.insert-sous-traitants-type').val("");
	$('.insert-nom-organisme').val("");	

	switch(value) {
		case 'Caisses,Cotisation':
			$('.subform-annee').show();
			$('.subform-societe').show();
			$('.subform-nom-organisme').show();
			break;
		case 'Statuts Assemblees':
			$('.subform-annee').show();
			$('.subform-societe').show();
			$('.subform-assemblees').show();
			$('.subform-statuts-assemblees-type').show();
			break;
		case 'Sous-traitants':
			$('.subform-sous-traitants-type').show();
			break;
		default:
			break;
	}
}

function showSubFormSousTypeCommercial(value) {
	$('.subform-article').hide();
	$('.subform-departement').hide();

	$('#insert-article').val("");
	$('#insert-department').val("");
	$('#insert-clientName').val("");

	switch(value) {		
		case 'Argumentaires':		
			$('.subform-article').hide();
			$('.subform-departement').hide();
			$('.subform-infographie').hide();
			$('.subform-soustype-infographie').hide();
			$('.submform-devis').hide();
			$('.subform-facture').hide();
			break;
		case 'Prix':
			$('.subform-article').show();
			$('.subform-departement').hide();
			$('.subform-infographie').hide();			
			$('.subform-soustype-infographie').hide();
			$('.submform-devis').hide();
			$('.subform-facture').hide();
			break;
		case 'Quantites Boitauxlettres':
			$('.subform-article').hide();
			$('.subform-departement').show();
			$('.subform-infographie').hide();			
			$('.subform-soustype-infographie').hide();
			$('.submform-devis').hide();
			$('.subform-facture').hide();
			break;
		case 'Pieces Diverses Client':		
			$('.subform-article').hide();
			$('.subform-departement').hide();
			$('.subform-infographie').show();
			$('.subform-soustype-infographie').hide();
			$('.submform-devis').hide();
			$('.subform-facture').hide();
			break;
		default:
			break;
	}
}

function showSubFormSousTypeRessourcesHumaines(value) {
	$('.subform-salarie').hide();
	$('.subform-societe').hide();
	$('.subform-annee').hide();
	$('.subform-recruitment-type').hide();

	$('#insert-salarie').val("");
	$('#insert-annee').val("");
	$('#insert-societe').val("");
	$('#insert-recruitment-type').val("");

	switch(value) {							
		case 'Contrats De Travail':	
			$('.subform-salarie').show();
			$('.subform-societe').show();
			break;
		case 'Pieces Salarie':		
			$('.subform-salarie').show();
			$('.subform-societe').show();
			break;
		case 'Recrutement':		
			$('.subform-recruitment-type').show();
			break;
		case 'Objectifs Salaries':	
			$('.subform-salarie').show();
			$('.subform-societe').show();	
			break;
		case 'Compte-rendu':		
			$('.subform-salarie').show();
			$('.subform-societe').show();
			$('.subform-annee').show();
			break;
		case 'Licenciement':		
			$('.subform-salarie').show();
			$('.subform-societe').show();
			break;
		case 'Commissions':		
			$('.subform-salarie').show();
			$('.subform-societe').show();
			$('.subform-annee').show();
			break;
		case 'Objectifs,Reunions':		
			$('.subform-salarie').show();
			$('.subform-societe').show();
			$('.subform-annee').show();
			break;
		case 'Divers':		
			break;		
		default:
			break;
	}
}

function resetFormMetadataUpload() {
	$('input[type=text], textarea, select').val("");
	$('#insert-devis').html('<option></option>');
	$('#insert-facture').html('<option></option>');
	$('.subform-documenttype').hide();
	$('.subform-soustype').hide();
	$('.nombre-pages').hide();
	$('.subform-soustype').find('input').val("");

	//plan
	$('.subform').hide();
}


function submitForm() {
	$(".overlay").show();

	var formData = new FormData($('.InsertMetaData')[0]);

	formData.append('client-name', $('#insert-clientName option:selected').text());
	formData.append('devis-name', $('#insert-devis option:selected').text());
	formData.append('facture-name', $('#insert-facture option:selected').text());
	formData.append('createdby-name', $('#insert-createdby option:selected').text());
	formData.append('uploadedby-name', $('#insert-uploadedby option:selected').text());
	formData.append('message-name', $('#insert-message option:selected').text());

	$.ajax({
		url: $('.InsertMetaData').attr("action"),
		type: 'POST',
		dataType: 'json',
		data: formData,
		async: true,
		success: function (data) {
			$(".overlay").hide();
			if(data.status) {
				window.location.href = "<?php echo site_url('ged');?>";
			} else {
				location.reload();
			}
			console.log("success") 
		},
		error: function() {
			$(".overlay").hide();
			location.reload();
			console.log("error")
		},
		cache: false,
		contentType: false,
		processData: false
	});		
}

$(document).ready(function() {	
	generateUserCombobox();
	generateClientCombobox();
	
	//subtree form structure
	$('#insert-typedocument').change(function(){
		var documentType = $(this).val();
		
		showSubFormDocumentType(documentType);
	})

	$('#insert-clientName').change(function(e) {
		generateDevisCombobox();
	});

	$('#insert-devis').change(function(e) {
		generateFacturesCombobox();
	});		

	$('#insert-soustype').change(function(){
		var sousType = $(this).val();		
		showSubFormSousType(sousType);
	});

	$('#insert-soustype-plan').change(function(e) {		
		showSubFormSousTypePlan($(this).val());
	});

	$('#insert-soustype-piece-comptable').change(function(e) {		
		showSubFormSousTypePieceComptable($(this).val());
	});

	$('#insert-soustype-administratifs-divers').change(function(e) {		
		showSubFormSousTypeAdministratifsDivers($(this).val());
	});

	$('#insert-soustype-ressources-humaines').change(function(e) {		
		showSubFormSousTypeRessourcesHumaines($(this).val());
	});

	$('#insert-soustype-commercial').change(function(e) {		
		showSubFormSousTypeCommercial($(this).val());
	});

	$('.do-upload').click(function() {
		$( "#files" ).trigger( "click" );
	});

	$('#files').change(function() {
		submitForm();
	});
});

</script>