<style type="text/css">
	.btn-ajouter {
		margin-top: 8px;
	}
	.btn-ajouter-link {
		margin-top: 8px;
	}
</style>
<script type="text/javascript">

	function generateBtnAjouter() {
		var btnAjouters = JSON.parse('<?php echo json_encode($liste_ajouter);?>');

		for(var i=0; i < btnAjouters.length; i++) {
			var classBtn = btnAjouters[i].ref == "#" ? "btn-ajouter" : "btn-ajouter-link"; 
			var targetBlank = btnAjouters[i].ref == "#" ? "" : 'target="_BLANK"';
			var button = '<a href="'+ btnAjouters[i].ref +'" '+ targetBlank +' class="btn btn-primary '+ classBtn +'" data-id="'+ btnAjouters[i].id +'"  data-name="'+ btnAjouters[i].champ +'">Ajouter</a>';

			$('#' + btnAjouters[i].id).parent().append(button);
		}
	}

	function addChamps() {
		//hide alert message
		$('#form-alert').hide();
		var urlAction = $('#form-add-champs').attr('action');
		var champName = $('input[name=champ_name]').val().trim();
		var champValue = $('input[name=champ_value]').val().trim();

		if(champName != "" && champValue != "") {
			var data = {
				champ_name: champName,
				champ_value: champValue
			};

			$.post( urlAction, data, function( response ) {
			  handleResponseFormChamps(response);
			}, "json");
		}
	}

	function handleResponseFormChamps(response) {
		if(response.status) {
			//append new value into dropdown
			var value = $('input[name=champ_value]').val();
			var id = $('input[name=champ_id]').val();
			var newOption = '<option>' + value + '</option>';
			$('#' + id).append(newOption);
			$('#' + id).val(value);
			//reset form and alert message
			$('input[name=champ_id]').val('');
			$('input[name=champ_name]').val('');
			$('input[name=champ_value]').val('');
			$('#form-alert').hide();
			//hide modal form
			$('#modal-form-ajouter').modal('hide');
		} else {
			$('#form-alert').html(response.error).fadeIn();
		}
	}

	$(document).ready(function() {
		generateBtnAjouter();

		$('.form-group').on('click', '.btn-ajouter', function(e) {
			e.preventDefault();
			var champName = $(this).attr('data-name');
			var champId = $(this).attr('data-id');
			$('input[name=champ_name]').val(champName);
			$('input[name=champ_id]').val(champId);
			$('#modal-form-ajouter').modal('show');
		});

		$('#form-add-champs').submit(function(e) {
			e.preventDefault();
			addChamps();
		});

		$('.btn-cancel').click(function(e) {
			$('#form-alert').html('').hide();
			$('input[name=champ_id]').val('');
			$('input[name=champ_name]').val('');
			$('input[name=champ_value]').val('');
			$('#modal-form-ajouter').modal('hide');
		});
	});
</script>