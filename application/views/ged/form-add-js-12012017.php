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

	$('#insert-soustype-site-internet').change(function(e) {		
		showSubFormSousTypeSiteInternet($(this).val());
	});

	$('#insert-soustype-administratifs-divers').change(function(e) {		
		showSubFormSousTypeAdministratifsDivers($(this).val());
	});

	$('.do-upload').click(function() {
		$( "#files" ).trigger( "click" );
	});

	$('#files').change(function() {
		submitForm();
	});
});


function showSubFormDocumentType(type) {
	$('.subform-documenttype').hide();
	$('.subform').hide();
	$('.subform-soustype-infographie').show();
	//$('.subform-soustype, .subform-soustype-plan, .subform-soustype-piece-comptable, .subform-soustype-site-internet, subform-soustype-administratifs-divers').hide();
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
			$('.subform-format-emailing').show();
			$('.subform-bass-de-donnee').show();
			$('.subform-infographie').show();
			$('.subform-soustype-infographie').hide();
			$('.subform-objet').show();
			$('.subform-emailing').show();
			$('.subform-photo').show();
			break;
		case 'Site Internet':
			$('.subform-soustype-site-internet').show();
			break;
		case 'Administratif Divers':
			$('.subform-soustype-administratifs-divers').show();
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
			$('.subform-societe').show();
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
	$('.subform-nom, .subform-societe, .subform-piece, .subform-statuts, .subform-assemblees, .subform-descriptif').hide();
	$('.subform-nom, .subform-societe, .subform-piece, .subform-statuts, .subform-assemblees, .subform-descriptif').find('input,select,textarea').val("");

	switch(value) {
		case 'Salarie':
			$('.subform-nom').show();
			$('.subform-societe').show();
			$('.subform-piece').show();
			break;
		case 'Societe':
			$('.subform-nom').show();
			$('.subform-statuts').show();
			$('.subform-assemblees').show();
			break;
		case 'Divers':
			$('.subform-descriptif').show();
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
</script>