<script>
	$(document).ready(function(){		
        //pull commande data when client select is changed
		$("#template-modal-detail").on("change","#client",function(){
			$.get("<?php echo site_url('amalgame'); ?>/commande_option/"+$("#client").val(), function(data){
				$("#commande").html(data);
			});
		});
		
		$('#template-modal-detail').on("focusout","#qty, #largeur, #longueur",function(){
			calculate_a5();
		});				
		
		$('#template-modal-detail').on("change","#denomination_taille, #denomination_taille_ferme", function(){
			calculate_a5();
		});
    });					
	
	function calculate_a5(){
		size = $('#denomination_taille option:selected').val();	
		if(size == 'A1'){ 		$('#largeur').val(59.4);	$('#longueur').val(84.1) }
		if(size == 'A2'){ 		$('#largeur').val(42);		$('#longueur').val(59.4) }
		if(size == 'A3'){ 		$('#largeur').val(29.7);	$('#longueur').val(42) }
		if(size == 'A4'){ 		$('#largeur').val(21);		$('#longueur').val(29.7) }
		if(size == 'A5'){ 		$('#largeur').val(14.8);	$('#longueur').val(21) }
		if(size == 'DIN LONG'){ $('#largeur').val(14.8);	$('#longueur').val(21) }
		if(size == 'A6'){ 		$('#largeur').val(10.5);	$('#longueur').val(14.8) }
		if(size == 'A7'){ 		$('#largeur').val(7.4);		$('#longueur').val(10.5) }
		
		size = $('#denomination_taille_ferme option:selected').val();	
		if(size == 'A1'){ 		$('#largeur_ferme').val(59.4);	$('#longueur_ferme').val(84.1) }
		if(size == 'A2'){ 		$('#largeur_ferme').val(42);	$('#longueur_ferme').val(59.4) }
		if(size == 'A3'){ 		$('#largeur_ferme').val(29.7);	$('#longueur_ferme').val(42) }
		if(size == 'A4'){ 		$('#largeur_ferme').val(21);	$('#longueur_ferme').val(29.7) }
		if(size == 'A5'){ 		$('#largeur_ferme').val(14.8);	$('#longueur_ferme').val(21) }
		if(size == 'DIN LONG'){ $('#largeur_ferme').val(14.8);	$('#longueur_ferme').val(21) }
		if(size == 'A6'){ 		$('#largeur_ferme').val(10.5);	$('#longueur_ferme').val(14.8) }
		if(size == 'A7'){ 		$('#largeur_ferme').val(7.4);	$('#longueur_ferme').val(10.5) }
		
		var width 	= $('#qty').val();
		var length 	= $('#largeur').val();
		var qty		= $('#longueur').val();
		var eq_a5	= (width*length*qty/15540000).toFixed(2);
		
		if(width != 0 && length != 0 && qty != 0)
			$('#eq_t50_a5').val(eq_a5);
	}
</script>