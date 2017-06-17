<div class="modal fade" id="modal-annuler-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-annuler-confirm" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close btn-close-annuler-confirm" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel-servers-index-1">Confirmation d'annuler</h4>
            </div>
            <div class="modal-body">
                êtes vous certain de vouloir annuler la saisie du serveur ? 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-close-annuler-confirm">OUI ENREGISTRER</button>
                <button type="button" class="btn btn-warning" role="button" id="btn-close-form-server">NE PAS ENREGISTRER</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

	$(document).ready(function() {
		$(document).on('click','.btn_ips_numero', function(e) 
		{
			var id = this.id.split('_');
			var m = $('#hide_ips_numero_'+id[3]).attr('class');
			if (m.length > 0)
			{
				$('#hide_ips_numero_'+id[3]).removeClass('hide');
				$(this).removeClass('btn-success');
				$(this).addClass('btn-warning');
				$(this).val('Effondrer la liste');
			}
			else
			{
				$('#hide_ips_numero_'+id[3]).addClass('hide');
				$(this).removeClass('btn-warning');
				$(this).addClass('btn-success');
				$(this).val('Elargir la liste');
			}
		});
		
		$(document).on('click','.btn_domaines_name', function(e) 
		{
			var id = this.id.split('_');
			var m = $('#hide_domaines_name_'+id[3]).attr('class');
			if (m.length > 0)
			{
				$('#hide_domaines_name_'+id[3]).removeClass('hide');
				$(this).removeClass('btn-success');
				$(this).addClass('btn-warning');
				$(this).val('Effondrer la liste');
			}
			else
			{
				$('#hide_domaines_name_'+id[3]).addClass('hide');
				$(this).removeClass('btn-warning');
				$(this).addClass('btn-success');
				$(this).val('Elargir la liste');
			}
		});
		
		$(document).on('click', '.btn-close-annuler-confirm', function (event) {
		   $('#modal-annuler-confirm').modal('hide');
           setTimeout(function() {
                $('body').addClass("modal-open");
            },500);
        });

         $(document).on('click', "#btn-close-form-server", function(e) {
        	$('#modal-annuler-confirm').modal('hide');
        	$('.template-modal').modal('hide');
        });
		
		$(document).on('click',"#enregister-terminee",function(e){
			var action = $('#template-modal-detail form').attr('action');
			var isModif = action.search('modification'); // nouveau or modification ?
			if(!(isModif <= 0))
			{
				$('#template-modal-detail #form-submit-servers-modification').click();
			}else{
				$('#template-modal-detail #form-submit-servers-nouveau').click();
			}
			
		});
		
		$('#template-modal-detail').on('shown.bs.modal',function(e) {
			var btn = '<button type="button" class="btn btn-primary" id="enregister-suivant">Enregister / Suivant</button>';
			var btnTerminee = '<button type="button" class="btn btn-success" id="enregister-terminee">Enregister et Terminer</button>&nbsp;';
			//var btnAnnuler = '&nbsp;<button data-toggle="modal" data-target="#modal-annuler-confirm" class="btn btn-default" id="annuler-server">Annuler</button>';
			var btnAnnuler = '&nbsp;<button class="btn btn-default" id="annuler-server">Annuler</button>';
			
			$('#template-modal-detail button[type="submit"]').hide();
			var action = $('#template-modal-detail form').attr('action');
			//var isModif = action.search('modification'); // nouveau or modification ?
			
			setTimeout(function() {
				/*if(!(isModif <= 0) && $("#enregister-terminee").length == 0)
				{
					$('#template-modal-detail form p.text-center').append(btnTerminee);
				}*/

				if($("#enregister-terminee").length == 0)
				{
					$('#template-modal-detail form p.text-center').append(btnTerminee);
				}
				
				if($("#enregister-suivant").length == 0) {
					$('#template-modal-detail form p.text-center').append(btn);
				}
				if($("#annuler-server").length == 0) {
					$('#template-modal-detail form p.text-center').append(btnAnnuler);
				}
			}, 20);
			
		});

		$('#template-modal-detail').on('click',"#annuler-server",function(e) {
			e.preventDefault();
			$('#modal-annuler-confirm').modal('show');
		});
		
		$('#template-modal-detail').on('click',"#enregister-suivant",function(e) {
			var tab = $("#template-modal-detail").find('ul.nav-tabs li.active a');
			var tabActive = $(tab).attr('aria-controls');
			var action = $('#template-modal-detail form').attr('action');
			var isModif = action.search('modification'); // nouveau or modification ?
			
			if(tabActive != "servers-tab-5") 
			{
				e.preventDefault();
				var tabArray = tabActive.split("-");
				var tabId = Number(tabArray[2]) + 1;
				var nextTab = "servers-tab-" + tabId;

				$('ul.nav-tabs a[href="#' + nextTab + '"]').tab('show');

				if(tabActive == "servers-tab-4") 
				{
					/*if(!(isModif <= 0))
					{
						$("#template-modal-detail #enregister-terminee").hide();
					}*/
					$("#template-modal-detail #enregister-terminee").hide();
					$("#template-modal-detail #enregister-suivant").hide();
					$('#template-modal-detail button[type="submit"]').show();
				}
			} else {
				
			}
		});

		$('#template-modal-detail').on('shown.bs.tab',"a",function(e) {
			var tab = $("#template-modal-detail").find('ul.nav-tabs li.active a');
			var tabActive = $(tab).attr('aria-controls');

			var action = $('#template-modal-detail form').attr('action');
			var isModif = action.search('modification'); // nouveau or modification ?
			//console.log(tabActive)
			
			if(tabActive == "servers-tab-5") {
				/*if(!(isModif <= 0))
				{
					$("#template-modal-detail #enregister-terminee").hide();
				}*/
				$("#template-modal-detail #enregister-terminee").hide();
				$("#template-modal-detail #enregister-suivant").hide();
				$('#template-modal-detail button[type="submit"]').show();
			} else {
				/*if(!(isModif <= 0))
				{
					$("#template-modal-detail #enregister-terminee").show();
				}*/
				$("#template-modal-detail #enregister-terminee").show();
				$("#template-modal-detail #enregister-suivant").show();
				$('#template-modal-detail button[type="submit"]').hide();
			}
		});

		/**
		 * 
		 */
			$('#template-modal-detail').on("click",".ajouters",function(e) {
			e.preventDefault();
			var option = $(this).val();
			var module = $(this).attr('data-id');
			var raw = document.getElementById('template-modal-detail');
			var action = $('#template-modal-detail form').attr('action');
			var SvrId = raw.getAttribute('data-id');

			//console.log(SvrId);
			//console.log(module);

			switch(module) 
			{
				case 'host':
					$(this).val("");
					getHosts('<?php echo site_url('hosts/nouveau').'/';?>'+SvrId+'/ajax');
				break;
				case 'owner':
					$(this).val("");
					getOwners('<?php echo site_url('owners/nouveau').'/';?>'+SvrId+'/ajax');
				break;
				case 'ips':
				if( SvrId == 0 || SvrId == null)
				{
					$('#sservers-tab-4').removeClass('active');
					$('#servers-tab-4').removeClass('active');
					$('#sservers-tab-1').addClass('active');
					$('#servers-tab-1').addClass('active');

					if($('#nom_interne').val() =='')
					{
						$('#nom_interne').focus();
						$( "#nom_interne" ).parent().parent().addClass("has-error");
						$('#ips').val(0);
					}
					else
					{
						$('#sservers-tab-4').addClass('active');
						$('#servers-tab-4').addClass('active');
						$('#sservers-tab-1').removeClass('active');
						$('#servers-tab-1').removeClass('active');
					
						var form = $('#form-servers-nouveau-1');
						dataString = form.serialize();
						var url = form.attr('action');
						$.ajax({
							url:url,
							dataType:'json',
							type:'post',
							data:dataString,
							success:function(response)
							{
								if(response.success == false)
								{
									//console.log((response.data));
								}
								else
								{
									SvrId = response.data.event.id;
									getIPS('<?php echo site_url('ips/nouveau').'/';?>'+SvrId+'/ajax');
									$('#svrid').val(SvrId);
								}
							}
						})
					}
				}
				else
				{
					$(this).val("");
					getIPS('<?php echo site_url('ips/nouveau').'/';?>'+SvrId+'/ajax');
				}
				/*
					window.open("<?php echo site_url('ips/nouveau/0');?>", "_blank");
					$(this).val("");						
				*/
				break;
				case 'domaines':
				if( SvrId == 0 || SvrId == null)
				{
					$('#sservers-tab-4').removeClass('active');
					$('#servers-tab-4').removeClass('active');
					$('#sservers-tab-1').addClass('active');
					$('#servers-tab-1').addClass('active');

					if($('#nom_interne').val() =='')
					{
						$('#nom_interne').focus();
						$( "#nom_interne" ).parent().parent().addClass("has-error");
						$('#domaines').val(0);
					}
					else
					{
						$('#sservers-tab-4').addClass('active');
						$('#servers-tab-4').addClass('active');
						$('#sservers-tab-1').removeClass('active');
						$('#servers-tab-1').removeClass('active');	
					
						var form = $('#form-servers-nouveau-1');
						dataString = form.serialize();
						var url = form.attr('action');
						$.ajax({
							url:url,
							dataType:'json',
							type:'post',
							data:dataString,
							success:function(response)
							{
								if(response.success == false)
								{
									//console.log((response.data));
								}
								else
								{
									SvrId = response.data.event.id;
									getDomains('<?php echo site_url('domains/nouveau').'/';?>'+SvrId+'/ajax');
									$('#svrid').val(SvrId);
								}
							}
						})
					}
				}
				else
				{
					$(this).val("");
					getDomains('<?php echo site_url('domains/nouveau').'/';?>'+SvrId+'/ajax');
				}
				/*
					window.open("<?php echo site_url('domains/nouveau/0');?>", "_blank");
					$(this).val("");
					*/
				break;
				case 'cb_utilsée':
					getCB('<?php echo site_url('cartes_blues/nouveau').'/0/ajax';?>');
				break;
				default:
					break;
			}	
		});

		$("#template-modal-detail").on("click", "li", function() {
			if($("#subtitle-plesk").length == 0)
				$('#acces_plesk_url').parent().parent().prepend(setTitle("Plesk"));
			
			if($("#subtitle-compte-client").length == 0)
				$('#acces_compte_client_url').parent().parent().prepend(setTitle("Compte Client"));

			if($("#subtitle-contrat").length == 0)
				$('#acces_contrat_url').parent().parent().prepend(setTitle("Contrat"));

			if($("#subtitle-root").length == 0)
				$('#acces_root_url').parent().parent().prepend(setTitle("Root"));
			
		});
	});

	function setTitle(name){
		name = name.replace(" ", "-");
		var id = 'subtitle-' + name.toLowerCase();
		subtitle = '<div id="'+id+'"><div class="col-sm-3 control-label"><b>'+name+'</b></div><div class="col-sm-9"></div><div style="clear: both;margin-bottom: 15px;"></div></div>';
		return subtitle;
	}

	$(document).ajaxComplete(function(){
		$('#form-submit-ips-nouveau').addClass("hide");
		$('#form-submit-domains-nouveau').addClass("hide"); //$('#form-submit-domains-nouveau').addClass("hide");
		$('#form-submit-owners-nouveau').addClass("hide");
		$('#form-submit-hosts-nouveau').addClass("hide");
		$('#form-submit-cartes_blues-nouveau').addClass("hide");
	});
	
	/*
	* IPS
	*/
	function getIPS(targetUrl)
	{
		$.ajax({
			url:targetUrl,
			type:'post',
			data:'',
			dataType:'json',
			success:function(response)
			{
				$('.frm_ips').html(response.data);
				$('#ajouter_ips').modal('show');
				$("#form-ips-nouveau-1").submit(function(event)
				{
				  event.preventDefault();
				  var $form = $( this ),url = $form.attr( 'action' );
				  var posting = $.post( url,$form.serialize());
					posting.done(function( result )
					{
						if(result.success == true) {
							console.log(result.data);

							$('#ips').multiselect('destroy');
							$('#ips').multiselect({
									includeSelectAllOption: false,
						    		enableFiltering: true,
						    		numberDisplayed: 1,
						    		enableCaseInsensitiveFiltering: true,
							});
							ips_id = result.data.event.id;
							ips_value = $('#numero').val();
							//append and rebuild the result back into ips field
							$('#ips').append('<option value="'+ips_id+'">'+ips_value+'</option>');
							$('#ips').multiselect('rebuild');

							//add ips into the new one
							$('#ips').multiselect('select',[ips_id]);
						} else {
							notificationWidget.show(result.message, result.notif);
						}
					});
				});
			}
		});
	}
	
	function getSavedIPS()
	{
		document.getElementById("form-submit-ips-nouveau").click();
		$('#ajouter_ips').modal('hide');
	}

	/*
	* Domains
	*/
	function getDomains(svrid)
	{
		$.ajax({
			url:svrid,
			type:'post',
			data:'',
			dataType:'json',
			success:function(response)
			{
				$('.frm_domains').html(response.data);
				$('#ajouter_domains').modal('show');
				$("#form-domains-nouveau-1").submit(function(event)
				{
				  event.preventDefault();
				  var $form = $( this ),
					  url = $form.attr( 'action' );
				  var posting = $.post( url,$form.serialize());
					posting.done(function(result)
					{
						if(result.success == true) 
						{
							$('#domaines').multiselect('destroy');
							$('#domaines').multiselect({
									includeSelectAllOption: false,
						    		enableFiltering: true,
						    		numberDisplayed: 1,
									maxHeight:300,
						    		enableCaseInsensitiveFiltering: true,
							});
							domains_id = result.data.event.id;
							domains_value = $('#form-domains-nouveau-1 #nom').val();
							
							//append and rebuild the result back into domains field
							$('#domaines').append('<option value="'+domains_id+'">'+domains_value+'</option>');
							$('#domaines').multiselect('rebuild');

							//add domains into the new one
							$('#domaines').multiselect('select',[domains_id]);							
						} else {
							notificationWidget.show(result.message, result.notif);
						}
					});
				});
			}
		});
	}
	
	function getSavedDomains()
	{
		document.getElementById("form-submit-domains-nouveau").click();
		$('#ajouter_domains').modal('hide');		
	}

	/**
	 * Owner Ajouter
	 */
	function getOwners(svrid)
	{
		$.ajax({
			url:svrid,
			type:'post',
			data:'',
			dataType:'json',
			success:function(response)
			{
				$('.frm_owners').html(response.data);
				$('#ajouter_owners').modal('show');
				$("#form-owners-nouveau-1").submit(function(event)
				{
				  event.preventDefault();
				  var $form = $( this ),
					  url = $form.attr( 'action' );
				  var posting = $.post( url,$form.serialize());
					posting.done(function(result)
					{
						if(result.success == true) {
							ownerId = result.data.event.id;				
							$.get("<?php echo site_url('owners/index_json');?>/" + ownerId, function(response) {
								var data = response.data[0];
								var ownerValue =  data.nom + " (" + data.email + " -- " + data.telephone + " -- " + data.banque + " )";
								//append the result back into owner field
								$('#owner').append('<option value="'+ownerId+'">'+ownerValue+'</option>');						
								//setting owner into the new one
								$('#owner').val(ownerId);
								$('#ajouter_owners').modal('hide');
							},"json");							
						} else {							
							notificationWidget.show(result.message, result.notif);
						}
					});
				});
			}
		});
	}
	function getSavedOwners()
	{
		document.getElementById("form-submit-owners-nouveau").click();		
	}

	/**
	 * Host Ajouter
	 */
	function getHosts(svrid)
	{
		$.ajax({
			url:svrid,
			type:'post',
			data:'',
			dataType:'json',
			success:function(response)
			{
				$('.frm_hosts').html(response.data);
				$('#ajouter_hosts').modal('show');
				$("#form-hosts-nouveau-1").submit(function(event)
				{
				  event.preventDefault();
				  var $form = $( this ),
					  url = $form.attr( 'action' );
				  var posting = $.post( url,$form.serialize());
					posting.done(function(result)
					{
						if(result.success == true) {
							hostId = result.data.event.id;				
							$.get("<?php echo site_url('hosts/index_json');?>/" + hostId, function(response) {
								var data = response.data[0];
								var hostValue =  data.nom + " (" + data.pays + " -- " + data.type + " )";
								//append the result back into hosts field
								$('#host').append('<option value="'+hostId+'">'+hostValue+'</option>');						
								//setting hosts into the new one
								$('#host').val(hostId);
								$('#ajouter_hosts').modal('hide');
							},"json");							
						} else {
							notificationWidget.show(result.message, result.notif);
						}
					});
				});
			}
		});
	}
	function getSavedHosts()
	{
		document.getElementById("form-submit-hosts-nouveau").click();		
	}
	
	/*
	* Cartes Blues
	*/
	function getCB(targetUrl)
	{
		$.ajax({
			url:targetUrl,
			type:'post',
			data:'',
			dataType:'json',
			success:function(response)
			{
				$('.frm_CB').html(response.data);
				$('#ajouter_CB').modal('show');
				$("#form-cartes_blues-nouveau-1").submit(function(event)
				{
				  event.preventDefault();
				  var $form = $( this ),url = $form.attr( 'action' );
				  var posting = $.post( url,$form.serialize());
					posting.done(function( result )
					{
						if(result.success == true) {
							CB_id = result.data.event.id;
							CB_value = $('#banque').val()+' ('+$('#premiers_chiffres').val()+' -- '+$('#derniers_chiffres').val()+' -- '+$('#societe').val()+')';
							//append the result back into cartes_blues field
							$('#cb_utilsée').append('<option value="'+CB_id+'">'+CB_value+'</option>');
							//setting cartes_blues into the new one
							$('#cb_utilsée').val(CB_id);
							$('#ajouter_CB').modal('hide');
						} else {
							notificationWidget.show(result.message, result.notif);
						}
					});
				});
			}
		});
	}

	function getSavedCB()
	{
		document.getElementById("form-submit-cartes_blues-nouveau").click();		
	}
	
	$(document).on('hide.bs.modal', '#ajouter_ips,#ajouter_domains,#ajouter_owners,#ajouter_hosts,#ajouter_CB', function (event) 
	{
		setTimeout(function() {
			$('body').addClass("modal-open");
		},1000);
	});
	$(document).on("keyup","#nom_interne",function(){
		if($('#nom_interne').val() != '')
		{
			$( "#nom_interne" ).parent().parent().removeClass("has-error");
		}
	});
</script>
<div class="modal fade" id="ajouter_ips" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body"><div class="frm_ips"></div></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-primary" onclick="getSavedIPS()">Enregister</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="ajouter_domains" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body"><div class="frm_domains"></div></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-primary" onclick="getSavedDomains()">Enregister</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ajouter_owners" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body"><div class="frm_owners"></div></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-primary" onclick="getSavedOwners()">Enregister</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ajouter_hosts" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body"><div class="frm_hosts"></div></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-primary" onclick="getSavedHosts()">Enregister</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ajouter_CB" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body"><div class="frm_CB"></div></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-primary" onclick="getSavedCB()">Enregister</button>
			</div>
		</div>
	</div>
</div>