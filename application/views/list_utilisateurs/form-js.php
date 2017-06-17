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
                <button type="button" class="btn btn-warning" role="button" id="btn-close-form">NE PAS ENREGISTRER</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        // $("#utl_date_fin").kendoDatePicker({format: "dd/MM/yyyy"});
        // $("#emp_date_entree").kendoDatePicker({format: "dd/MM/yyyy"});
        // $("#emp_date_sortie").kendoDatePicker({format: "dd/MM/yyyy"});

        $('#template-modal-detail').on('shown.bs.modal',function(e) {
			var btn = '<button type="button" class="btn btn-primary" id="enregister-suivant">Enregister / Suivant</button>';
			var btnTerminee = '<button type="button" class="btn btn-success" id="enregister-terminee">Enregister et Terminer</button>&nbsp;';
			var btnAnnuler = '&nbsp;<a class="btn btn-default" id="annuler-btn">Annuler</a>';
			
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
				if($("#annuler-btn").length == 0) {
					$('#template-modal-detail form p.text-center').append(btnAnnuler);
				}
			}, 20);
			var formId = $(this).find('form').prop('id');
			$("<option value=''>(choisissez)</option>").insertBefore("#emp_commission option:first");
			if(formId == "form-list_utilisateurs-nouveau-1"){ 
				$("#emp_commission").val(''); 
			}
		});

		$(document).on('click', '.btn-close-annuler-confirm', function (event) {
			event.preventDefault();
		   $('#modal-annuler-confirm').modal('hide');
           setTimeout(function() {
                $('body').addClass("modal-open");
            },500);
        });

        $('#template-modal-detail').on('click',"#annuler-btn",function(e) {
			e.preventDefault();
			$('#modal-annuler-confirm').modal('show');
		});

		$(document).on('click', "#btn-close-form", function(e) {
        	$('#modal-annuler-confirm').modal('hide');
        	$('.template-modal').modal('hide');
        });

        $('#template-modal-detail').on('click',"#enregister-suivant",function(e) {
			var tab = $("#template-modal-detail").find('ul.nav-tabs li.active a');
			var tabActive = $(tab).attr('aria-controls');
			var totalTabs = $('ul.nav-tabs li').length;
			var indexLastTab = totalTabs - 1;
			var indexFinishTab  = totalTabs - 2;
			var lastTab = "list_utilisateurs-tab-" + indexLastTab;
			var finishTab = "list_utilisateurs-tab-" + indexFinishTab;

			var action = $('#template-modal-detail form').attr('action');
			var isModif = action.search('modification'); // nouveau or modification ?
			
			if(tabActive != lastTab) 
			{
				e.preventDefault();
				var tabArray = tabActive.split("-");
				var tabId = Number(tabArray[2]) + 1;
				var nextTab = "list_utilisateurs-tab-" + tabId;

				$('ul.nav-tabs a[href="#' + nextTab + '"]').tab('show');

				if(tabActive == finishTab) 
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

		/** Hide & Show Btn Enregister/Suivant **/
		$('#template-modal-detail').on('shown.bs.tab',"a",function(e) {
			var tab = $("#template-modal-detail").find('ul.nav-tabs li.active a');
			var tabActive = $(tab).attr('aria-controls');
			var totalTabs = $('ul.nav-tabs li').length;
			var indexLastTab = totalTabs - 1;
			var lastTab = "list_utilisateurs-tab-" + indexLastTab;
		
			if(tabActive == lastTab) {
				$("#template-modal-detail #enregister-terminee").hide();
				$("#template-modal-detail #enregister-suivant").hide();
				$('#template-modal-detail button[type="submit"]').show();
			} else {
				$("#template-modal-detail #enregister-terminee").show();
				$("#template-modal-detail #enregister-suivant").show();
				$('#template-modal-detail button[type="submit"]').hide();
			}
			
		});

		$(document).on('click',"#enregister-terminee",function(e){
			var action = $('#template-modal-detail form').attr('action');
			var isModif = action.search('modification'); // nouveau or modification ?
			if(!(isModif <= 0))
			{
				$('#template-modal-detail #form-submit-list_utilisateurs-modification').click();
			}else{
				$('#template-modal-detail #form-submit-list_utilisateurs-nouveau').click();
			}
			
		});
    });
</script>