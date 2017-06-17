<script type="text/javascript">
	$(document).ready(function() {
		/** action set id to form valider ce tri */
		/* 
        $('#commandes_trier_valider').click(function(e) {
          	e.preventDefault();
          	var villeId = "<?php echo $this->uri->segment(3);?>";
          	var url = $('#commandes_trier_valider').find('a').attr('data-cible');

          	console.log(url);

          	$('#btn-yes-valider-tri').prop('href', url + "/" + villeId);
          	$('#modal-form-valider-tri').modal('show'); 
        });
		*/
		
		var villeId = "<?php echo $this->uri->segment(3);?>";
		
		$('#commandes_trier_valider').click(function(e) {
			e.preventDefault();
			$('#modal-form-valider-tri').modal('show'); 			
		})
		
		$('#btn-yes-valider-tri').click(function(){
			//for each checked box
			ids = {};
			i = 0;
			$('input[name="ids[]"]').each(function(){ 
				if($(this).is(':checked')){
					theid = $(this).val();
					ids[i] = theid;
					i++;
				}
			})
			
			//console.log(ids);
			if(Object.keys(ids).length != 0){
				$.post('<?php echo site_url('commandes_trier/valider_ce_tri/'); ?>/'+villeId, ids).done(function( data ) {					
					window.location = '<?php echo rtrim(site_url('feuilles_de_tri/?new_id='), '/'); ?>'+data;
					//window.location = '<?php echo site_url('feuilles_de_tri/group/'); ?>/'+data;
				});
			}
		});
	})
</script>