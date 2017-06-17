<script type="text/javascript">
	var ville = "<?php echo $group->ville;?>";
	var date_du_tri = "<?php echo $group->date_du_tri;?>";

	$(document).ready(function(e) {
		//detect response json if have date du tri and vil_nom
		$('.cdf').append('<p style="text-align:center">Ville : '+ ville +'<br>Date du Tri : '+ date_du_tri +'</p>');

		$('#feuilles_de_tri_creer_fdr').click(function(e) {
			e.preventDefault();
			var url = $('#feuilles_de_tri_creer_fdr a').attr('href');

			ids = [];
			i = 0;
			$('input[name="secteur_ids[]"]').each(function(){ 
				if($(this).is(':checked')){
					theid = $(this).val();
					ids.push(theid);
					i++;
				}
			})
			
			//console.log(ids);
			if(ids.length != 0){
				console.log(url);
				var paramIds = ids.join(",");
				var groupId = "<?php echo $group_id;?>";
				window.location = "<?php echo site_url('feuilles_de_tri/nouveau_fdr');?>" + "/" + groupId + "?ids=" + paramIds;
			}
		});
	});
</script>