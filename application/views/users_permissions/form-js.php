<div class="modal fade" id="modal-annuler-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-annuler-confirm" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close btn-close-annuler-confirm" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel-servers-index-1">Confirmation d'annuler</h4>
            </div>
            <div class="modal-body">
                êtes vous certain de vouloir annuler la saisie du manual sending ? 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-close-annuler-confirm">OUI ENREGISTRER</button>
                <button type="button" class="btn btn-warning" role="button" id="btn-close-form">NE PAS ENREGISTRER</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade template-modal" id="modification_users_permissions" tabindex="-1" role="dialog" data-id="">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">				
				<div class="frm_modification_users_permissions">
					
				</div>
			</div>
		
		</div>
	</div>
</div>

<script>
    $(document).ready(function(){
    	
		//pull usp_fields data when client select is changed
		$("#template-modal-detail,#modification_users_permissions").on("change",'#usp_table', function(){
			reset_fields();			
			console.log($("#usp_table").val());
		});

		$('#template-modal-detail,#modification_users_permissions').on("change","#usp_type", function() {
			get_fields();
		});
		
		/*$("#template-modal-detail").on('change',"#usp_fields", function(){
			passe_modul_change();		
		});
							
		passe_modul_change();*/	

		/**
		 * Append Button Enregister / Suivant & Annuler
		 * 
		 */
		$('#template-modal-detail').on('shown.bs.modal',function(e) {					
			var formId = $(this).find('form').prop('id');	

			if(formId == "form-users_permissions-nouveau-1") {
				var btn = '<button type="button" class="btn btn-primary" id="enregister-suivant">Enregister</button>';
				var btnAnnuler = '&nbsp;<button class="btn btn-default" id="annuler-users-permissions">Annuler</button>';

				//$('#template-modal-detail button[type="submit"]').hide();
				setTimeout(function() {
					/*if($("#enregister-suivant").length == 0) {
						$('#template-modal-detail form p.text-center').append(btn);
					}*/
					if($("#annuler-users-permissions").length == 0) {
						$('#template-modal-detail form p.text-center').append(btnAnnuler);
					}
				}, 20);
			} 
		});

		$('#template-modal-detail,#modification_users_permissions').on('hide.bs.modal',function(e) {	
			//$('#enregister-suivant').remove();
			$('#annuler-users-permissions').remove();
		});

		$(document).on('click', '.btn-close-annuler-confirm', function (event) {
		   $('#modal-annuler-confirm').modal('hide');
           setTimeout(function() {
                $('body').addClass("modal-open");
            },500);
        });

		/** Close Modal Form */
		$(document).on('click', "#btn-close-form", function(e) {
        	$('#modal-annuler-confirm').modal('hide');
        	$('.template-modal').modal('hide');
        });

		/** Open Confirmation Annuler */
		$('#template-modal-detail').on('click',"#annuler-users-permissions",function(e) {
			e.preventDefault();
			$('#modal-annuler-confirm').modal('show');
		});

				/**
		 * FORM EDIT
		 */
		$('#modification_users_permissions').on('shown.bs.modal',function(e) {							
			var formId = $(this).find('form').prop('id');					

			if(formId == "form-users_permissions-modification-1") {
				var btn = '<button type="button" class="btn btn-primary" id="enregister-suivant">Enregister</button>';
				var btnAnnuler = '&nbsp;<button class="btn btn-default" id="annuler-users-permissions">Annuler</button>';

				//$('#modification_users_permissions button[type="submit"]').hide();
				setTimeout(function() {
					/*if($("#enregister-suivant").length == 0) {
						$('#modification_users_permissions form p.text-center').append(btn);
					}*/
					if($("#annuler-users-permissions").length == 0) {
						$('#modification_users_permissions form p.text-center').append(btnAnnuler);
					}
				}, 20);
			} 

		});

		
		$('#modification_users_permissions').on('click',"#annuler-users-permissions",function(e) {
			e.preventDefault();
			$('#modal-annuler-confirm').modal('show');
		});

		$('#users_permissions_modification a').click(function(e) {
			e.preventDefault();
			var url = $(this).attr('href') + '/ajax';	

			$.ajax({
				url: url,
				type:'post',				
				dataType:'json',
				success:function(response)
				{
					$('.frm_modification_users_permissions').html(response.data);
					$('#modification_users_permissions').modal('show');			
				}
			});
		});

		$("#modification_users_permissions").on("click","#form-submit-users_permissions-modification", function(e) {
			e.preventDefault();
		
			var form = $('#modification_users_permissions').find('form');			
			var url = $(form).attr("action");
	        var data = $(form).serialize();	   

	        $.ajax({
	            type: "POST",
	            url: url,
	            data: data,
	            dataType: "json",
	            success: function(response) {
	            	var helper = actionMenuBar.datatable;
	            	if(response.success == true) {
	            		var event = (response.hasOwnProperty("data")) ? response.data.event : null;

	            		if(event){	            		
		            		if(event.id) {
		            			helper.reload(event.id);
		            		}
	            		}

	            		$('#modification_users_permissions').modal('hide');
	            	}

	            	notificationWidget.show(response.message, response.notif);
	            }
	        });
		});

	});

		
	
	/*function passe_modul_change(){
		if($("#usp_fields option:selected").val()=='-1'){
			$("#usp_fields").css('color', 'red');
			$("#usp_fields").css('font-weight', 'bold');
		} else {
			$("#usp_fields").css('color', '#555');
			$("#usp_fields").css('font-weight', 'normal');				
		}
	}*/

	function get_fields() {
		var table = $("#usp_table").val();
		var type = $("#usp_type").val();

		if(table != "" && type != 0) {
			$.get("<?php echo site_url('users_permissions/fields_option');?>/"+table+"/"+type, function(data){	
				$('#usp_fields').multiselect('dataprovider', data);
			},"json");
		} else {
			$('#usp_fields').multiselect('dataprovider', []);
		}
	}	

	function reset_fields() {
		$('#usp_type').val(0);
		$('#usp_fields').multiselect('dataprovider', []);
	}
</script>
