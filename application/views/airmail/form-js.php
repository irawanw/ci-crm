<div class="modal fade" id="modal-annuler-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-annuler-confirm" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close btn-close-annuler-confirm" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel-servers-index-1">Confirmation d'annuler</h4>
            </div>
            <div class="modal-body">
                êtes vous certain de vouloir annuler la saisie du Air mail ? 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-close-annuler-confirm">OUI ENREGISTRER</button>
                <button type="button" class="btn btn-warning" role="button" id="btn-close-form">NE PAS ENREGISTRER</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade template-modal" id="modification_airmail" tabindex="-1" role="dialog" data-id="">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">				
				<div class="frm_modification_airmail">
					
				</div>
			</div>
		
		</div>
	</div>
</div>

<script>
    $(document).ready(function(){
    	// Initialisation des champs date de formulaire dans la fenêtre modale
	    $("#modification_airmail").on("focus", "input.form-date-field",  function() {
	        $(this).datetimepicker({
	            format:'d/m/Y',
	            formatDate:'d/m/Y',
	            timepicker: false,
	            todayButton: true,
	            allowBlank: !$(this).attr('required')
	        });
	    })

		//pull commande data when client select is changed
		$("#template-modal-detail,#modification_airmail").on("change",'#client', function(){
			get_commande();			
		});
		
		$("#template-modal-detail").on('change',"#commande", function(){
			passe_cmd_change();		
		});

		$("#template-modal-detail").on('change',"#segment_numero", function() {
			get_segment_criteria($(this).val(), false);
		});

		$("#modification_airmail").on('change',"#segment_numero", function() {
			get_segment_criteria($(this).val(), true);
		});
							
		passe_cmd_change();	

		/**
		 * Append Button Enregister / Suivant & Annuler
		 * 
		 */
		$('#template-modal-detail').on('shown.bs.modal',function(e) {	
			$('#form-airmail-nouveau-1').find('#segment_criteria').attr("readonly", true);					
			var formId = $(this).find('form').prop('id');	

			if(formId == "form-airmail-nouveau-1") {
				var btn = '<button type="button" class="btn btn-primary" id="enregister-suivant">Enregister / suivant</button>';
				var btnAnnuler = '&nbsp;<button class="btn btn-default" id="annuler-airmail">Annuler</button>';

				$('#template-modal-detail button[type="submit"]').hide();
				setTimeout(function() {
					if($("#enregister-suivant").length == 0) {
						$('#template-modal-detail form p.text-center').append(btn);
					}
					if($("#annuler-airmail").length == 0) {
						$('#template-modal-detail form p.text-center').append(btnAnnuler);
					}
				}, 20);
			} else {
				var btnChild = '<button type="button" class="btn btn-primary" id="enregister-suivant-child">Enregister / suivant</button>';
				var btnAnnulerChild = '&nbsp;<button class="btn btn-default" id="annuler-airmail-child">Annuler</button>';

				$('#template-modal-detail button[type="submit"]').hide();
				setTimeout(function() {
					if($("#enregister-suivant-child").length == 0) {
						$('#template-modal-detail form p.text-center').append(btnChild);
					}
					if($("#annuler-airmail-child").length == 0) {
						$('#template-modal-detail form p.text-center').append(btnAnnulerChild);
					}
				}, 20);
			}
			var table = $('#datatable').DataTable();
			if ( table.rows( '.selected' ).any() ){
				var td_id = $('#datatable > tbody > tr.selected').find('td:nth-child(2)').text();
				$('#parent_id option[value='+td_id+']').prop('selected', true);
			}
			
		});

		$('#template-modal-detail,#modification_airmail').on('hide.bs.modal',function(e) {	
			$('#enregister-suivant').remove();
			$('#enregister-suivant-child').remove();
			$('#annuler-airmail').remove();
			$('#annuler-airmail-child').remove();
		});

		/**
		 * Eof Append Button Enregister / Suivant & Annuler
		 * 
		 */

		/**
		 * Cancel Submit Form		 
		 */
		$(document).on('click', '.btn-close-annuler-confirm', function (event) {
		   $('#modal-annuler-confirm').modal('hide');
           setTimeout(function() {
                $('body').addClass("modal-open");
            },500);
        });
        /**
		 * EOF Cancel Submit Form		 
		 */

		/** Close Modal Form */
		$(document).on('click', "#btn-close-form", function(e) {
        	$('#modal-annuler-confirm').modal('hide');
        	$('.template-modal').modal('hide');
        });

		/** Open Confirmation Annuler */
		$('#template-modal-detail').on('click',"#annuler-airmail,#annuler-airmail-child",function(e) {
			e.preventDefault();
			$('#modal-annuler-confirm').modal('show');
		});

		/** Action Btn Enregister/suivant go to next tab **/
		$('#template-modal-detail').on('click',"#form-airmail-nouveau-1 #enregister-suivant",function(e) {
			var tab = $("#template-modal-detail").find('ul.nav-tabs li.active a');
			var tabActive = $(tab).attr('aria-controls');			
					
			if(tabActive != "airmail-tab-4") {
				e.preventDefault();
				var tabArray = tabActive.split("-");
				var tabId = Number(tabArray[2]) + 1;
				var nextTab = "airmail-tab-" + tabId;

				$('ul.nav-tabs a[href="#' + nextTab + '"]').tab('show');

				if(tabActive == "airmail-tab-3") {
					$("#template-modal-detail #enregister-suivant").hide();
					$('#template-modal-detail button[type="submit"]').show();
				}
			} else {
				
			}

		});

		/** Action Btn Enregister/suivant form child go to next tab **/
		$('#template-modal-detail').on('click',"#form-airmail-nouveau_child-1 #enregister-suivant-child",function(e) {
			var tabChild = $("#template-modal-detail").find('ul.nav-tabs li.active a');
			var tabActiveChild = $(tabChild).attr('aria-controls');			
					
			if(tabActiveChild != "airmail-tab-5") {
				e.preventDefault();
				var tabArrayChild = tabActiveChild.split("-");
				var tabIdChild = Number(tabArrayChild[2]) + 1;
				var nextTabChild = "airmail-tab-" + tabIdChild;

				$('ul.nav-tabs a[href="#' + nextTabChild + '"]').tab('show');

				if(tabActiveChild == "airmail-tab-4") {
					$("#template-modal-detail #enregister-suivant-child").hide();
					$('#template-modal-detail button[type="submit"]').show();
				}
			} else {
				
			}

		});

		$(document).on("click","#modification_airmail .modal-body li a",function()
	    {
	        tab = $(this).attr("href");
	        $(".modal-body .tab-content div").each(function(){
	            $(this).removeClass("active");
	        });
	        $(".modal-body .tab-content "+tab).addClass("active");
	    });

		/** Hide & Show Btn Enregister/Suivant **/
		$('#template-modal-detail').on('shown.bs.tab',"a",function(e) {
			var tab = $("#template-modal-detail").find('ul.nav-tabs li.active a');
			var tabActive = $(tab).attr('aria-controls');
			var formId = $('#template-modal-detail').find('form').prop('id');


			if(formId == "form-airmail-nouveau-1") {
				if(tabActive == "airmail-tab-4") {
					$("#template-modal-detail #enregister-suivant").hide();
					$('#template-modal-detail button[type="submit"]').show();
				} else {
					$("#template-modal-detail #enregister-suivant").show();
					$('#template-modal-detail button[type="submit"]').hide();
				}
			} else {
				if(tabActive == "airmail-tab-5") {
					$("#template-modal-detail #enregister-suivant-child").hide();
					$('#template-modal-detail button[type="submit"]').show();
				} else {
					$("#template-modal-detail #enregister-suivant-child").show();
					$('#template-modal-detail button[type="submit"]').hide();
				}
			}
		});

		/**
		 * FORM EDIT
		 */
		$('#modification_airmail').on('shown.bs.modal',function(e) {	
			$('#form-airmail-modification-1').find('#segment_criteria').attr("readonly", true);						
			var formId = $(this).find('form').prop('id');					

			if(formId == "form-airmail-modification-1") {
				var btn = '<button type="button" class="btn btn-primary" id="enregister-suivant">Enregister / suivant</button>';
				var btnAnnuler = '&nbsp;<button class="btn btn-default" id="annuler-airmail">Annuler</button>';

				$('#modification_airmail button[type="submit"]').hide();
				setTimeout(function() {
					if($("#enregister-suivant").length == 0) {
						$('#modification_airmail form p.text-center').append(btn);
					}
					if($("#annuler-airmail").length == 0) {
						$('#modification_airmail form p.text-center').append(btnAnnuler);
					}
				}, 20);
			} else {
				var btnChild = '<button type="button" class="btn btn-primary" id="enregister-suivant-child">Enregister / suivant</button>';
				var btnAnnulerChild = '&nbsp;<button class="btn btn-default" id="annuler-airmail-child">Annuler</button>';				

				$('#modification_airmail button[type="submit"]').hide();
				setTimeout(function() {
					if($("#enregister-suivant-child").length == 0) {
						$('#modification_airmail form p.text-center').append(btnChild);
					}
					if($("#annuler-airmail-child").length == 0) {
						$('#modification_airmail form p.text-center').append(btnAnnulerChild);
					}
				}, 20);
			}

			setTimeout(function() {
				get_segment_criteria($('#form-airmail-modification-1').find('#segment_numero').val(),true);
			},100);
		});

		$(document).on('click',"#modification_airmail #form-airmail-modification-1 #enregister-suivant",function(e) {
			var templateId = "#modification_airmail";
			var tab = $(templateId).find('ul.nav-tabs li.active a');
			var tabActive = $(tab).attr('aria-controls');			
					
			if(tabActive != "airmail-tab-4") {
				e.preventDefault();
				var tabArray = tabActive.split("-");
				var tabId = Number(tabArray[2]) + 1;
				var nextTab = "airmail-tab-" + tabId;

				$('ul.nav-tabs a[href="#' + nextTab + '"]').tab('show');

				$("#modification_airmail .modal-body .tab-content div").each(function(){
		            $(this).removeClass("active");
		        });
		        $("#modification_airmail #"+nextTab).addClass("active");

				if(tabActive == "airmail-tab-3") {
					$(templateId + " #enregister-suivant").hide();
					$(templateId + ' button[type="submit"]').show();
				}
			} else {
				
			}

		});

		$(document).on('click',"#modification_airmail #enregister-suivant-child",function(e) {
			var templateId = "#modification_airmail";
			var tabChild = $(templateId).find('ul.nav-tabs li.active a');
			var tabActiveChild = $(tabChild).attr('aria-controls');			
					
			if(tabActiveChild != "airmail-tab-5") {
				e.preventDefault();
				var tabArrayChild = tabActiveChild.split("-");
				var tabIdChild = Number(tabArrayChild[2]) + 1;
				var nextTabChild = "airmail-tab-" + tabIdChild;

				$('ul.nav-tabs a[href="#' + nextTabChild + '"]').tab('show');

				$("#modification_airmail .modal-body .tab-content div").each(function(){
		            $(this).removeClass("active");
		        });
		        $("#modification_airmail #"+nextTabChild).addClass("active");

				if(tabActiveChild == "airmail-tab-4") {
					$(templateId + " #enregister-suivant-child").hide();
					$(templateId + ' button[type="submit"]').show();
				}
			} else {
				
			}

		});

		$('#modification_airmail').on('shown.bs.tab',"a",function(e) {
			var templateId = "#modification_airmail";
			var tab = $(templateId).find('ul.nav-tabs li.active a');
			var tabActive = $(tab).attr('aria-controls');
			var formId = $(templateId).find('form').prop('id');
			
			if(formId == "form-airmail-modification-1") {
				if(tabActive == "airmail-tab-4") {
					$(templateId + " #enregister-suivant").hide();
					$(templateId + ' button[type="submit"]').show();
				} else {
					$(templateId + " #enregister-suivant").show();
					$(templateId + ' button[type="submit"]').hide();
				}
			} else {
				if(tabActive == "airmail-tab-5") {
					$(templateId + " #enregister-suivant-child").hide();
					$(templateId + ' button[type="submit"]').show();
				} else {
					$(templateId + " #enregister-suivant-child").show();
					$(templateId + ' button[type="submit"]').hide();
				}
			}
		});

		$('#modification_airmail').on('click',"#annuler-airmail,#annuler-airmail-child",function(e) {
			e.preventDefault();
			$('#modal-annuler-confirm').modal('show');
		});

		$('#airmail_modification a').click(function(e) {
			e.preventDefault();
			var url = $(this).attr('href') + '/ajax';	

			$.ajax({
				url: url,
				type:'post',				
				dataType:'json',
				success:function(response)
				{
					$('.frm_modification_airmail').html(response.data);
					$('#modification_airmail').modal('show');			
				}
			});
		});

		/**
		 * Submit Form
		 * 
		 */
		$('#template-modal-detail').on("click","#form-submit-airmail-nouveau_child", function(e) {
			e.preventDefault();

			var form = $('#form-airmail-nouveau_child-1');			
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
	            			var parentId = event.parentId;
	            			helper.reload(parentId);	
	            			$('.child-' + parentId).remove();		            		
	            		}

	            		$('#template-modal-detail').modal('hide');
	            	}

	            	notificationWidget.show(response.message, response.notif);
	            }
	        });
		});

		$("#modification_airmail").on("click","#form-submit-airmail-modification,#form-submit-airmail-modification_child", function(e) {
			e.preventDefault();
		
			var form = $('#modification_airmail').find('form');			
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
		            		var isChild = event.hasOwnProperty("isChild") ? true : false;		            		

		            		if(!isChild) {
			            		if(event.id) {
			            			helper.reload(event.id);
			            		}	          
		            		} else {
		            			var parentId = $('#child-id-' + event.id).attr('data-parent');
		            			var subText = $('#child-id-' + event.id).find("td:first:eq(0)").text();
		            			
		            			$.ajax({
		            				type: 'POST',
		            				url: "<?php echo site_url('airmail/index_child_json');?>/" + event.id,
		            				data: {parentId: parentId},
		            				dataType: 'json',
		            				success: function(response) {
		            					var rows = response.data;            					
		            					
		            					if(rows.length > 0) {
											var row = rows[0];		            					
			            					reloadChildRow(event.id, subText, row);
			            					helper.reload(parentId);
		            					}
		            				}
		            			})
		            		}
	            		}

	            		$('#modification_airmail').modal('hide');
	            	}

	            	notificationWidget.show(response.message, response.notif);
	            }
	        });
		});

    });

	function reloadChildRow (id, subText, data) {
		$('#child-id-' + id).html('<td colspan="23">'+subText+'</td>'+				
	            '<td>'+data.date_envoi+'</td>'+
	            '<td>'+data.segment_part+'</td>'+                                     
                //'<td>'+data.date_limite_de_fin+'</td>'+
                '<td>'+data.stats+'</td>'+
                '<td>'+data.quantite_envoyer+'</td>'+
                '<td>'+data.quantite_envoyee+'</td>'+
                //'<td>'+data.airmail_number_of_open+'</td>'+
                //'<td>'+data.airmail_open_rate+'</td>'+
                '<td>'+data.airmail_number_of_click+'</td>'+
                '<td>'+data.airmail_click_rate+'</td>'+
                '<td>'+data.deliv_sur_test_orange+'</td>'+
                '<td>'+data.deliv_sur_test_free+'</td>'+
                '<td>'+data.deliv_sur_test_sfr+'</td>'+
                '<td>'+data.deliv_sur_test_gmail+'</td>'+
                '<td>'+data.deliv_sur_test_yahoo+'</td>'+
                '<td>'+data.deliv_sur_test_microsoft+'</td>'+
                '<td>'+data.deliv_sur_test_ovh+'</td>'+
                '<td>'+data.deliv_sur_test_oneandone+'</td>'+
                '<td>'+data.operateur_qui_envoie_name+'</td>'+
                '<td>'+data.physical_server+'</td>'+
                '<td>'+data.smtp+'</td>'+
                '<td>'+data.rotation+'</td>');	
	}
	
	function passe_cmd_change(){
		if($("#commande option:selected").val()=='-1'){
			$("#commande").css('color', 'red');
			$("#commande").css('font-weight', 'bold');
		} else {
			$("#commande").css('color', '#555');
			$("#commande").css('font-weight', 'normal');				
		}
	}

	function get_commande() {
		$.get("<?php echo site_url('airmail/commande_option');?>/"+$("#client").val(), function(data){
				$("#commande").html(data);
		});
	}	

	function get_segment_criteria(segmentNumero, isUpdate) {
		$.get("<?php echo site_url('segments/get');?>/"+segmentNumero+"/criteria", function(response){
			if(isUpdate) {
				$("#form-airmail-modification-1").find('#segment_criteria').html(response.data);
			} else {
				$("#form-airmail-nouveau-1").find('#segment_criteria').html(response.data);
			}
		},"json");
	}
</script>
