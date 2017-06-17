<script type="text/javascript">
    $(document).ready(function(){
		//pull commande data when client select is changed
		$("#template-modal-detail").on("change","#client", function(){
			$.get("<?php echo site_url('livraisons/commande_option');?>/"+$("#client").val(), function(data){
				$("#commande").html(data);
			});			
		});
		
		
		$("#template-modal-detail").on("change","#commande", function(){
			passe_cmd_change();
		})
		
		$('#template-modal-detail').on('shown.bs.modal', function (e) {		
			passe_cmd_change();
		});
    });
	
	function passe_cmd_change(){
		if($("#commande option:selected").val()=='-1'){
			$("#commande").css('color', 'red');
			$("#commande").css('font-weight', 'bold');
		} else {
			$("#commande").css('color', '#555');
			$("#commande").css('font-weight', 'normal');				
		}
	}
</script>