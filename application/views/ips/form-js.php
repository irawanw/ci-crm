<script type="text/javascript">
$(document).ready(function() 
{
	$('#template-modal-detail').on('shown.bs.modal', function (e) {
		$('#host').attr('readonly', true);
		$('#owner').attr('readonly', true);
	});
$('#template-modal-detail').on("click",".ajouters",function(e) {
	console.log('a');
	$('#ajouter_server').modal('show');
});
	$('#template-modal-detail').on("change","#serveur",function(e) {
		var serveurId = $(this).val();
		var raw = document.getElementById('template-modal-detail');
		var SvrId = raw.getAttribute('data-id');
		if(serveurId != "" && serveurId != "ajouter") {
			$.get("<?php echo site_url('ips/get_host');?>/"+ serveurId, function(data){
				$('#host').val(data.id);
			}, "json");
			$.get("<?php echo site_url('ips/get_owner');?>/"+ serveurId, function(data){
				$('#owner').val(data.id);
			}, "json");		
		} 
		else if(serveurId == "ajouter") 
		{
			//$(this).val("");
			$('#ajouter_server').modal('show');
			//getServer('<?php echo site_url('servers/nouveau').'/';?>'+SvrId+'/ajax');
		}
		else {
			$('#host').val("");
			$('#owner').val("");
		}
	});
});
function getServer(svrid)
{
	$.ajax({
		url:svrid,
		type:'get',
		data:'',
		dataType:'json',
		success:function(response)
		{
			$('.frm_server').html(response.data);
			$('#ajouter_server').modal('show');
			$("#form-servers-nouveau-1").submit(function(event)
			{
			  event.preventDefault();
			  var $form = $( this ),url = $form.attr( 'action' );
			  var posting = $.post( url,$form.serialize());
				posting.done(function(result)
				{
					if(result.success == true) {
						server_id = result.data.event.id;
						server_value = $('#nom_interne').val();
						//append the result back into server field
						$('#serveur').append('<option value="'+server_id+'">'+server_value+'</option>');						
						//setting server into the new one
						$('#serveur').val(server_id);
						//apply the change so other field is updated automatically
						$("#serveur").change();
					}
					else {
						notificationWidget.show(result.message, result.notif);
					}
				});
			});
		}
	});
}
$(document).ajaxComplete(function(){
	$('#form-submit-servers-nouveau').addClass("hide");
});
	
function getSavedServer()
{
	document.getElementById("form-submit-servers-nouveau").click();
	$('#ajouter_server').modal('hide');
}

$(document).on('hide.bs.modal','#ajouter_server', function (event) 
{
	setTimeout(function() {
		$('body').addClass("modal-open");
	},1000);
});
</script>
<div class="modal fade" id="ajouter_server" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Ajouter Serveur</h4>
			</div>
			<div class="modal-body">
				<div class="frm_server">
					<p>vous devez créer le serveur en allant sur la page serveur,</p>
					<p>puis revenez dans le menu domaine, après avoir enregistré le serveur</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-md btn-info" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>