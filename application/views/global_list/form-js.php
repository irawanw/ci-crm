<!-- Modal Annuler Parent -->
<div class="modal fade" id="modal-annuler-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-annuler-confirm" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close btn-close-annuler-confirm" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel-servers-index-1">Confirmation d'annuler</h4>
            </div>
            <div class="modal-body">
                êtes vous certain de vouloir annuler la saisie du? 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-close-annuler-confirm">OUI ENREGISTRER</button>
                <button type="button" class="btn btn-warning" role="button" id="btn-close-form">NE PAS ENREGISTRER</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Parent -->
<div class="modal fade template-modal modal_global_list_parent" id="modal_nouveau_global_list" tabindex="-1" role="dialog" data-id="">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">				
				<div class="frm_global_list_parent">
					
				</div>
			</div>
		
		</div>
	</div>
</div>
<!-- Modal Annuler Child -->
<div class="modal fade" id="modal-annuler-confirm-child" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-annuler-confirm" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close btn-close-annuler-confirm-child" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel-servers-index-1">Confirmation d'annuler</h4>
            </div>
            <div class="modal-body">
                êtes vous certain de vouloir annuler la saisie du? 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-close-annuler-confirm-child">OUI ENREGISTRER</button>
                <button type="button" class="btn btn-warning" role="button" id="btn-close-form-child">NE PAS ENREGISTRER</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Form Child -->
<div class="modal fade template-modal modal_global_list_child" tabindex="-1" role="dialog" data-id="">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">				
				<div class="frm_global_list_child">
					
				</div>
			</div>
		
		</div>
	</div>
</div>

<script type="text/javascript">
/**
 * Declaration Variable
 * @type {Object}
 */
var defaultSoftwareIds = ['1','2','3','4','5','6','7','8'];


var SOFTWARE_IDS = {	
	"openemm":1,
	"pages_jaunes":2,
	"manual_sending":3,
	"max_bulk": 4,
	"sendgrid": 5,
	"sendinblue": 6,
	"airmail": 7,
	"mailchimp": 8,
};
var SITE_URL = "<?php echo site_url();?>";
var moduleName = "";

// function setModuleName(softwareId) {	
// 	console.log(softwareId)
// 	if(defaultSoftwareIds.indexOf(softwareId) != -1) {
// 		moduleName = Object.keys(SOFTWARE_IDS)[softwareId-1];
// 	} else {
// 		moduleName = "emailing";
// 	}
// }

var setModuleName = function(softwareId) {
    var defer = $.Deferred();
    if(defaultSoftwareIds.indexOf(softwareId) != -1) {
		moduleName = Object.keys(SOFTWARE_IDS)[softwareId-1];
	} else {
		moduleName = "emailing";
	}

    setTimeout(function() {
        defer.resolve(); // When this fires, the code in a().then(/..../); is executed.
    }, 1000);

    return defer;
};

	$(document).ready(function(e) {
		$(".modal_global_list_parent,.modal_global_list_child").on("focus", "input.form-date-field",  function() {
	        $(this).datetimepicker({
	            format:'d/m/Y',
	            formatDate:'d/m/Y',
	            timepicker: false,
	            todayButton: true,
	            allowBlank: !$(this).attr('required')
	        });
	    })

		/* ==================== OPERATION EMAILING PARENT ==================== */

		/** SHOW FORM NOUVEAU EMAILING */
		$('.btn-create-emailing').change(function(e) {
			e.preventDefault();

			if($(this).val() != "") {
				$('#modal-form-emailing').modal('hide');
				moduleName = $(this).val();
				var type = $('#modal-form-emailing').attr('data-type');

				if(type == 'parent') {
					getForm('nouveau', 0);
				} else {
					getFormChild('nouveau', 0);
				}
			}
		});

		$('#modal-form-emailing').on('hidden.bs.modal', function(e) {
			$('.btn-create-emailing').val("");
		});

		$('#global_list_modification').click(function(e) {
			e.preventDefault();
			var rowId = $('#datatable').find('tr.selected').attr('id');
			var parent = $('#datatable').find('tr.selected').attr('data-parent') ? $('#datatable').find('tr.selected').attr('data-parent') : null;

			if(parent) {
				var rowIdArr = rowId.split('-');
                var ids = rowIdArr[2];
                var idArr = ids.split('_');
                var id = idArr[0];
                var softwareId = idArr[1];
                setModuleName(softwareId).then(getFormChild('modification', id));   

                //getFormChild('modification', id);
			} else {
				var rowIdArr = rowId.split('_');
				var id = rowIdArr[0];
				var softwareId = rowIdArr[1];
				setModuleName(softwareId).then(getForm('modification', id));
				//getForm('modification',id);
			}			
		});

		/** PULL COMMANDE DATA WHEN INPUT CLIENT CHANGED */
		$("#modal_nouveau_global_list").on("change",'#client', function(){
			getCommande('#modal_nouveau_global_list');			
		});

		/** SET INFO CRITERIA WHEN INPUT SEGMENT NUMERO SELECTED */
		$("#modal_nouveau_global_list").on('change',"#segment_numero", function() {						
			getSegmentCriteria();
		});

		/** SET CRITERIA TEXTAREA READONLY */
		$('.modal_global_list_parent').on('shown.bs.modal', function(e) {
			appendBtn();
			$('#segment_criteria').attr("readonly", true);
			$('input.form-date-field').attr('autocomplete','off');
		});

		/** SHOW MODAL ANNULER CONFIRM */
		$('.modal_global_list_parent').on('click',".btn-annuler",function(e) {
			e.preventDefault();
			$('#modal-annuler-confirm').modal('show');
		});

		/** Close Modal Form */
		$(document).on('click', "#btn-close-form", function(e) {
        	$('#modal-annuler-confirm').modal('hide');
        	$('.modal_global_list_parent').modal('hide');
        });

        /** Action Btn Enregister/suivant go to next tab **/
		$('.modal_global_list_parent').on('click',"#enregister-suivant",function(e) {
			var tab = $(".modal_global_list_parent").find('ul.nav-tabs li.active a');
			var tabActive = $(tab).attr('aria-controls');		
			var totalTabs = $('ul.nav-tabs li').length;
			var indexLastTab = totalTabs - 1;
			var indexFinishTab  = totalTabs - 2;
			var lastTab = moduleName + "-tab-" + indexLastTab;
			var finishTab = moduleName + "-tab-" + indexFinishTab;	
					
			if(tabActive != lastTab) {
				e.preventDefault();
				var tabArray = tabActive.split("-");
				var tabId = Number(tabArray[2]) + 1;
				var nextTab = moduleName+ "-tab-" + tabId;

				$('ul.nav-tabs a[href="#' + nextTab + '"]').tab('show');

				if(tabActive == finishTab) {
					$(".modal_global_list_parent #enregister-suivant").hide();
					$('.modal_global_list_parent button[type="submit"]').show();
				}
			} else {
				
			}

		});

		/** Hide & Show Btn Enregister/Suivant **/
		$('.modal_global_list_parent').on('shown.bs.tab',"a",function(e) {
			var tab = $(".modal_global_list_parent").find('ul.nav-tabs li.active a');
			var tabActive = $(tab).attr('aria-controls');
			var totalTabs = $('ul.nav-tabs li').length;
			var indexLastTab = totalTabs - 1;
			var indexFinishTab  = totalTabs - 2;
			var lastTab = moduleName + "-tab-" + indexLastTab;
	
			if(tabActive == lastTab) {
				$(".modal_global_list_parent #enregister-suivant").hide();
				$('.modal_global_list_parent button[type="submit"]').show();
			} else {
				$(".modal_global_list_parent #enregister-suivant").show();
				$('.modal_global_list_parent button[type="submit"]').hide();
			}
			
		});

		/**
		 * Submit Form Nouveau
		 * 
		 */
		$(".modal_global_list_parent").on("click","button[type=submit]", function(e) {
			e.preventDefault();
		
			var form = $('.modal_global_list_parent').find('form');			
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
		            			$.post(SITE_URL + '/' + moduleName + '/index_json/' + event.id, {}, function(response) {
		            				if(response.data.length == 1) {
		            					var row = response.data[0];
		            					var globalId = event.id + '_' + row.software;
				            			if(event.type == 'recordadd'){
				            				helper.load(globalId);
				            			}else {
				            				helper.reload(globalId);
				            			}

		            				}
		            			});		            			
		            		}	          
		            		
	            		}

	            		$('.modal_global_list_parent').modal('hide');
	            	}
	            	notificationWidget.show(response.message, response.notif);
	            }
	        });
		});

		$('.modal_global_list_parent').on('hidden.bs.modal', function(e) {
			$('.frm_global_list_parent').html("");
		});

		/**
		 * Cancel Submit Form		 
		 */
		$(document).on('click', '.btn-close-annuler-confirm', function (event) {
		   $('#modal-annuler-confirm').modal('hide');
           setTimeout(function() {
                $('body').addClass("modal-open");
            },500);
        });

		/* ==================== END OF OPERATION EMAILING PARENT ==================== */

		/* ==================== OPERATION EMAILING CHILD ==================== */
		$('.modal_global_list_child').on('shown.bs.modal', function(e) {
			appendBtnChild();			
		});
		
		/** SHOW MODAL ANNULER CONFIRM */
		$('.modal_global_list_child').on('click',".btn-annuler-child",function(e) {
			e.preventDefault();
			$('#modal-annuler-confirm-child').modal('show');
		});

		/** Close Modal Form */
		$(document).on('click', "#btn-close-form-child", function(e) {
        	$('#modal-annuler-confirm-child').modal('hide');
        	$('.modal_global_list_child').modal('hide');
        });

		$('#global_list_nouveau_child').click(function(e){
            e.preventDefault();
            $('#modal-form-emailing').attr('data-type', 'child');
            var rowId = $('#datatable').find('tr.selected').attr('id');

            if(rowId){
                var rowIdArr = rowId.split('_');
                var id = rowIdArr[0];
                var softwareId = rowIdArr[1];
                setModuleName(softwareId).then(getFormChild('nouveau', id));  

                //getFormChild('nouveau', id);             
            } else {
                $('#modal-form-emailing').modal('show');
            }
        });

        $('.modal_global_list_child').on('hidden.bs.modal', function(e) {
			$('.frm_global_list_child').html("");
		});
		/** SHOW MODAL ANNULER CONFIRM */
		$('.modal_global_list_child').on('click',".btn-annuler-child",function(e) {
			e.preventDefault();
			$('#modal-annuler-confirm-child').modal('show');
		});

		/** Close Modal Form */
		$(document).on('click', "#btn-close-form-child", function(e) {
        	$('#modal-annuler-confirm-child').modal('hide');
        	$('.modal_global_list_child').modal('hide');
        });        

		/** Action Btn Enregister/suivant go to next tab **/
		$('.modal_global_list_child').on('click',"#enregister-suivant-child",function(e) {
			var tab = $(".modal_global_list_child").find('ul.nav-tabs li.active a');
			var tabActive = $(tab).attr('aria-controls');						
			var totalTabs = $('ul.nav-tabs li').length;
			var indexLastTab = totalTabs - 1;
			var indexFinishTab  = totalTabs - 2;
			var lastTab = moduleName + "-tab-" + indexLastTab;
			var finishTab = moduleName + "-tab-" + indexFinishTab;
					
			if(tabActive != lastTab) {
				e.preventDefault();
				var tabArray = tabActive.split("-");
				var tabId = Number(tabArray[2]) + 1;
				var nextTab = moduleName+ "-tab-" + tabId;

				$('ul.nav-tabs a[href="#' + nextTab + '"]').tab('show');

				if(tabActive == finishTab) {
					$(".modal_global_list_child #enregister-suivant-child").hide();
					$('.modal_global_list_child button[type="submit"]').show();
				}
			} else {
				
			}

		});
		
		/** Hide & Show Btn Enregister/Suivant **/
		$('.modal_global_list_child').on('shown.bs.tab',"a",function(e) {
			var tab = $(".modal_global_list_child").find('ul.nav-tabs li.active a');
			var tabActive = $(tab).attr('aria-controls');
			var totalTabs = $('ul.nav-tabs li').length;
			var indexLastTab = totalTabs - 1;
			var lastTab = moduleName+"-tab-" + indexLastTab;
		
			if(tabActive == lastTab) {
				$(".modal_global_list_child #enregister-suivant-child").hide();
				$('.modal_global_list_child button[type="submit"]').show();
			} else {
				$(".modal_global_list_child #enregister-suivant-child").show();
				$('.modal_global_list_child button[type="submit"]').hide();
			}
			
		});

		$(".modal_global_list_child").on("click","button[type=submit]", function(e) {
			e.preventDefault();
		
			var form = $('.modal_global_list_child').find('form');			
			var url = $(form).attr("action");
	        var data = $(form).serialize();	   
	        //var softwareId = SOFTWARE_IDS[moduleName];

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

	            			$.post(SITE_URL + '/' + moduleName + '/index_json/' + event.id, {}, function(response) {

	            				if(response.data.length == 1) {
	            					var row = response.data[0];
	            					var softwareId = row.software;

		            				if(event.type == 'recordadd') {	            				
				            			var parentId = event.parentId + '_' + softwareId;
				            			helper.reload(parentId);	
				            		} else {
				            			var parentId = $('#child-id-' + event.id + '_' + softwareId).attr('data-parent');
				            			var subText = $('#child-id-' + event.id + '_' + softwareId).find("td:first:eq(0)").text();
										var id = event.id + '_' + softwareId;		        
				            			
				            			$.ajax({
				            				type: 'POST',
				            				url: SITE_URL + '/global_list/index_child_json/' + id,
				            				data: {parentId: parentId},
				            				dataType: 'json',
				            				success: function(response) {
				            					var rows = response.data;            					
				            					
				            					if(rows.length > 0) {
													var row = rows[0];		            					
					            					reloadChildRow(id, subText, row);
					            					helper.reload(parentId);
				            					}
				            				}
				            			})
				            		}
			            		}
	            			});	     
	            		}

	            		$('.modal_global_list_child').modal('hide');
	            	}

	            	notificationWidget.show(response.message, response.notif);
	            }
	        });
		});

		/**
		 * Cancel Submit Form		 
		 */
		$(document).on('click', '.btn-close-annuler-confirm-child', function (event) {
		   $('#modal-annuler-confirm-child').modal('hide');
           setTimeout(function() {
                $('body').addClass("modal-open");
            },500);
        });

		/* ==================== END OF OPERATION EMAILING CHILD ==================== */

	});

	/** ================ METHOD FOR ACTION EMAILING PARENT DATA ================= **/
	function getForm(action,id) {
		var url = SITE_URL + '/' + moduleName + '/'+action+'/'+id+'/ajax';
		//console.log(url);
		$.ajax({
			url: url,
			type:'post',				
			dataType:'json',
			success:function(response)
			{
				$('.frm_global_list_parent').html(response.data);
				$('.modal_global_list_parent').modal('show');	
				//console.log(response.data);
				if(action == "modification") {
					getSegmentCriteria();
				}				
			}
		});
	}

	function getCommande(modalId) {
		$.get(SITE_URL + '/' + moduleName + "/commande_option/" + $(modalId + " #client").val(), function(data){
			$(modalId + " #commande").html(data);
		});
	}	

	function getSegmentCriteria() {		
		var segmentNumero = $("#segment_numero").val();
		$.get("<?php echo site_url('segments/get');?>/"+ segmentNumero +"/criteria", function(response){
			$('.modal_global_list_parent').find('#segment_criteria').html(response.data);
		},"json");
	}

	function appendBtn() {
		var btn = '<button type="button" class="btn btn-primary" id="enregister-suivant">Enregister / suivant</button>';
		var btnAnnuler = '&nbsp;<button class="btn btn-default btn-annuler" id="annuler-'+ moduleName +'">Annuler</button>';

		$('.modal_global_list_parent button[type="submit"]').hide();
		setTimeout(function() {
			if($("#enregister-suivant").length == 0) {
				$('.modal_global_list_parent form p.text-center').append(btn);
			}
			if($("#annuler-"+ moduleName).length == 0) {
				$('.modal_global_list_parent form p.text-center').append(btnAnnuler);
			}
		}, 20);
	}
	/** ================ EOF METHOD FOR ACTION EMAILING PARENT DATA ================= **/

	/** ================ METHOD FOR ACTION EMAILING CHILD DATA ================= **/
	function getFormChild(action, id) {
		console.log("getFormChild "+ moduleName);
		var url = SITE_URL + '/' + moduleName + '/'+action+'_child/'+id+'/ajax';

		$.ajax({
			url: url,
			type:'post',				
			dataType:'json',
			success:function(response)
			{
				$('.frm_global_list_child').html(response.data);
				$('.modal_global_list_child').modal('show');	

				if(action == "nouveau") {
					setTimeout(function() {
						$('#parent_id').val(id);
					},200);				
				}	
			}
		});
	}
	function appendBtnChild() {
		var btn = '<button type="button" class="btn btn-primary" id="enregister-suivant-child">Enregister / suivant</button>';
		var btnAnnuler = '&nbsp;<button class="btn btn-default btn-annuler-child" id="annuler-'+ moduleName +'-child">Annuler</button>';

		$('.modal_global_list_child button[type="submit"]').hide();
		setTimeout(function() {
			if($("#enregister-suivant-child").length == 0) {
				$('.modal_global_list_child form p.text-center').append(btn);
			}
			if($("#annuler-"+ moduleName+'-child').length == 0) {
				$('.modal_global_list_child form p.text-center').append(btnAnnuler);
			}
		}, 20);
	}

	function reloadChildRow (id, subText, data) {
		$('#child-id-' + id).html('<td colspan="22">'+subText+'</td>'+
				'<td>'+data.date_envoi+'</td>'+
                '<td>'+data.segment_part+'</td>'+                
                '<td>'+data.stats+'</td>'+
                '<td>'+data.quantite_envoyee+'</td>'+
                '<td>'+data.open+'</td>'+
                '<td>'+data.open_pourcentage+'</td>'+
                '<td>'+data.openemm_number_of_click+'</td>'+
                '<td>'+data.openemm_click_rate_pct+'</td>'+
                '<td>'+data.verification_number+'</td>'+
                '<td>'+data.number_sent_through+'</td>'+
                '<td>'+data.number_sent_mail+'</td>'+
                '<td>'+data.deliv_sur_test_orange+'</td>'+
                '<td>'+data.deliv_sur_test_free+'</td>'+
                '<td>'+data.deliv_sur_test_sfr+'</td>'+
                '<td>'+data.deliv_sur_test_gmail+'</td>'+
                '<td>'+data.deliv_sur_test_microsoft+'</td>'+
                '<td>'+data.deliv_sur_test_yahoo+'</td>'+
                '<td>'+data.deliv_sur_test_ovh+'</td>'+
                '<td>'+data.deliv_sur_test_oneandone+'</td>'+
                '<td>'+data.deliv_reelle_bounce+'</td>'+
                '<td>'+data.deliv_reelle_bounce_percentage_pct+'</td>'+
                '<td>'+data.deliv_reelle_hard_bounce_rate_pct+'</td>'+
                '<td>'+data.deliv_reelle_soft_bounce_rate_pct+'</td>'+
                '<td>'+data.deliv_reelle_orange+'</td>'+
                '<td>'+data.deliv_reelle_free+'</td>'+
                '<td>'+data.deliv_reelle_sfr+'</td>'+
                '<td>'+data.deliv_reelle_gmail+'</td>'+
                '<td>'+data.deliv_reelle_microsoft+'</td>'+
                '<td>'+data.deliv_reelle_yahoo+'</td>'+
                '<td>'+data.deliv_reelle_ovh+'</td>'+
                '<td>'+data.deliv_reelle_oneandone+'</td>'+
                '<td>'+data.operateur_qui_envoie+'</td>'+
                '<td>'+data.number_sent+'</td>'+
                '<td>'+data.physical_server+'</td>'+
                '<td>'+data.provider+'</td>'+
                '<td>'+data.ip+'</td>'+
                '<td>'+data.smtp+'</td>'+
                '<td>'+data.rotation+'</td>'+
                '<td>'+data.domain+'</td>'+
                '<td>'+data.computer+'</td>'+
                '<td>'+data.manual_sender+'</td>'+
                '<td>'+data.manual_sender_domain+'</td>'+
                '<td>'+data.copy_mail+'</td>'+
                '<td>'+data.speed_hours+'</td>'+
                '<td>'+data.number_hours+'</td>');  	
	}
	/** ================ EOF METHOD FOR ACTION EMAILING CHILD DATA ================= **/
</script>