<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap-datepicker.min.css');?>">
<script src="<?php echo base_url('assets/js/bootstrap-datepicker.min.js');?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		// $('#date_du_controle').kendoDatePicker({format: "dd/MM/yyyy"});
		$('#heure_de_debut, #heure_de_fin, #date_du_controle').datepicker({
		    autoclose: true,
		    todayHighlight: true,
		    format: "dd/mm/yyyy"
		});		
		
        $("#controle_distribution_date").kendoDatePicker({format: "dd-MM-yyyy"});
        $("#controle_distribution_heure_de_debut,#controle_distribution_heure_de_fin").kendoTimePicker({format: "HH:mm"});		

		//pull devis data when client select is changed
		$("#client").change(function(){
			$('#devis').attr('readonly', true);
			$.get("<?php echo site_url('feuille_controle/devis_option');?>/"+$("#client").val(), function(data){
				$("#devis").html(data);
				$('#devis').attr('readonly', false);
			});			
		});

		//pull factures data when devis select is changed
		$("#devis").change(function(){
			$('#facture').attr('readonly', true);
			$.get("<?php echo site_url('feuille_controle/factures_option');?>/"+$("#devis").val(), function(data){
				$("#facture").html(data);
				$('#facture').attr('readonly', false);

			});			
		})
	});
</script>