<script type="text/javascript">
	$(document).ready(function() {

		$("#template-modal-detail").on("click", "li", function() {
			if($("#subtitle").length == 0) {
				$('#acces_contrat_url, #acces_contrat_login, #acces_contrat_pass, #acces_contrat_utilisateurs').attr('readonly', true);
				$('#acces_contrat_url').parent().parent().prepend(setTitle("Contrat"));
			}
		});

		$('#template-modal-detail').on('shown.bs.modal', function (e) {
			$('#host').attr('readonly', true);
			$('#compte').attr('readonly', true);
			$('#contrat').attr('readonly', true);
			$('#owner').attr('readonly', true);
		});

		$('#template-modal-detail').on("click",".ajouters",function(e) {
			$('#ajouter_server').modal('show');
		});
	
		//pull data when server select is changed
		$("#template-modal-detail").on("change","#server",function(){
			var serveurId = $(this).val();
			console.log(serveurId);
			
			if(serveurId != "" && serveurId != "ajouter") {
				$.get("<?php echo site_url('domains/host_option');?>/"+$("#server").val(), function(data){
					$("#host").html(data);
					//$('#host').attr('readonly', false);
				});
				$.get("<?php echo site_url('domains/owner_option');?>/"+$("#server").val(), function(data){
					$("#owner").html(data);
					//$('#host').attr('readonly', false);
				});
				$.get("<?php echo site_url('domains/compte_option');?>/"+$("#server").val(), function(data){
					$("#compte").html(data);
					//$('#compte').attr('readonly', false);
				});
				$.get("<?php echo site_url('domains/contrat_option');?>/"+$("#server").val(), function(data){
					$("#contrat").html(data);
					//$('#contrat').attr('readonly', false);
				});	
			} else if(serveurId == "ajouter") {
				var raw = document.getElementById('template-modal-detail');
				var SvrId = raw.getAttribute('data-id');
				//$(this).val("");
				//getServer('<?php echo site_url('servers/nouveau').'/';?>'+SvrId+'/ajax');
			} else {
				$('#host').val("");
				$('#owner').val("");
				$("#compte").html("");
				$("#contrat").html("");
			}
		});

		//pull contrat detail data when contrat select is changed
		$("#template-modal-detail").on("change","#contrat",function(){

			//var ownerId = $('#owner').val() ? $('#owner').val() : 0;
			var hostId =  $('#host').val() ? $('#host').val() : 0;
			var compte = $('#compte').val() ? $('#compte').val() : 0;
			var contrat = $('#contrat').val() ? $('#contrat').val() : 0;
			var ownerId = $('#owner').val() ? $('#owner').val() : 0;

			var data = {
				owner_id: ownerId,
				host_id: hostId,
				compte: compte,
				contrat: contrat,
			};

			if(contrat != "") {
				$.post("<?php echo site_url('domains/get_contrat_detail');?>", data, function(data){
					$('#acces_contrat_url').val(data.url);
					$('#acces_contrat_login').val(data.login);
					$('#acces_contrat_pass').val(data.pass);
					$('#acces_contrat_utilisateurs').val(data.utilisateurs);
				}, "json");			
			} else {
				$('#acces_contrat_url').val("");
					$('#acces_contrat_login').val("");
					$('#acces_contrat_pass').val("");
					$('#acces_contrat_utilisateurs').val("");
			}
		});
		/*
				
		//pull compte data when host select is changed
		$("#template-modal-detail").on("change","#host",function(){
			$('#compte').attr('readonly', true);
			$.get("<?php echo site_url('domains/compte_option');?>/"+$("#host").val(), function(data){
				$("#compte").html(data);
				$('#compte').attr('readonly', false);
			});			
		});

		//pull contrat data
		$("#template-modal-detail").on("change","#server,#host, #compte",function(){
			$('#contrat').attr('readonly', true);

			//var ownerId = $('#owner').val() ? $('#owner').val() : 0;
			var serverId = $('#server').val() ? $('#server').val() : 0;
			var hostId =  $('#host').val() ? $('#host').val() : 0;
			var compte = $('#compte').val() ? $('#compte').val() : 0;

			//$.get("<?php echo site_url('domains/contrat_option');?>/"+ ownerId + "/" + hostId + "/" + compte, function(data){
				$.get("<?php echo site_url('domains/contrat_option');?>/"+ serverId + "/" + hostId + "/" + compte, function(data){
				$("#contrat").html(data);
				$('#contrat').attr('readonly', false);
			});			
		});
		
		*/
	});

	function setTitle(name){
		subtitle = '<div id="subtitle"><div class="col-sm-3 control-label"><b>'+name+'</b></div><div class="col-sm-9"></div><div style="clear: both;margin-bottom: 15px;"></div></div>';
		return subtitle;
	}
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
						server_id = result.data.event.id;
						server_value = $('#nom_interne').val();

						//append the result back into server field
						$('#server').append('<option value="'+server_id+'">'+server_value+'</option>');						
						//setting server into the new one
						$('#server').val(server_id);						
						//apply the change so other field is updated automatically
						$("#server").change();
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

	$(document).on('hide.bs.modal', '#ajouter_server', function (event) 
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