<?php
	
?>

<!-- Modal Form Confirmation Delete -->
<div id="modal-form-remove" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <form>
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation Delete</h4>
      </div>
      <div class="modal-body">
            <p>Etes-vous certain de vouloir supprimer le champ?</p>
          </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
    <a href="#" id="btn-remove" class="btn btn-warning">Yes</a>
      </div>
    </div>
    </form>
  </div>
</div>

<script>
	var site_url 				= "<?php echo site_url($controleur);?>";		
	var valid_controle			= "<?php echo $group->valid; ?>";

	$(document).ready(function(){
		<?php 
            $valid = $group->valid;
            $type  = $group->type;
            if($valid == 0): 
        ?>            
            $('#devalider_controle_permanent').addClass('disabled');            
            $('#devalider_controle_ponctuels').addClass('disabled');
        <?php else: ?>
        	$('#valider_controle_permanent').addClass('disabled');            
            $('#valider_controle_ponctuels').addClass('disabled');
        <?php endif; ?> 

        <?php if($type == "permanent"): ?>
        	$('#valider_controle_ponctuels').addClass('disabled');
        	$('#devalider_controle_ponctuels').addClass('disabled');
        <?php else: ?>
        	$('#valider_controle_permanent').addClass('disabled');            
        	$('#devalider_controle_permanent').addClass('disabled');            
        <?php endif; ?>   
		
		//$("#date_input_controler_ponctuels").kendoDatePicker({format: "dd/MM/yyyy"});

		//popup modal confirmation remove data
		$('#controle_recurrents_supprimer').click(function(e) {
            e.preventDefault();
            $('#modal-form-remove').modal('show');
        });

		
		$("#client_view").change(function(){
			$.get(site_url + "/get_permanent/"+$("#client_view").val()+"/valides", function(data){
				$("#controle_permanent_view").html(data);
			});
			$.get(site_url + "/get_permanent/"+$("#client_view").val()+"/nonvalides", function(data){
				$("#controle_permanent_nonvalides_view").html(data);
			});
			$.get(site_url + "/get_ponctuels/"+$("#client_view").val()+"/valides", function(data){
				$("#controle_ponctuels_view").html(data);
			});
			$.get(site_url + "/get_ponctuels/"+$("#client_view").val()+"/nonvalides", function(data){
				$("#controle_ponctuels_nonvalides_view").html(data);
			});
		});

		$("#controle_permanent_view").change(function(){
			window.location = ($("#controle_permanent_view option:selected").val());
		})
		$("#controle_permanent_nonvalides_view").change(function(){
			window.location = ($("#controle_permanent_nonvalides_view option:selected").val());
		})
		$("#controle_ponctuels_view").change(function(){
			window.location = ($("#controle_ponctuels_view option:selected").val());
		})
		$("#controle_ponctuels_nonvalides_view").change(function(){
			window.location = ($("#controle_ponctuels_nonvalides_view option:selected").val());
		})

		//change list view
		$('#sel_view').change(function(){
			window.location = ($('#sel_view option:selected').val());
		})			
		
		//mass action
		$("#mass_action").click(function(e) {
			if(valid_controle == "0") {
				var action 		= $("#sel_action_all" ).val();
				var resultat	= $("#sel_action_all option:selected" ).text().substr(9);
				theid = {};
				$("input:checkbox:checked").each(function(i){
					theid[i] = $(this).val();
					if(action == 'remove' || action == 'unremove')
						$(this).parent().parent().fadeOut();
					else{												

					}
				});
				$.ajax({
					type: "POST",
					url: site_url + "/mass_" + action,
					data: {ids: JSON.stringify(theid)},
					success: function(data) {
						$('#datatable').DataTable().ajax.reload();
					},
					error: function(err) {
						notificationWidget.show("il y avait une erreur dans le système", "error");
					}
				});
			} else {
				notificationWidget.show("Vous devez dé-valider le contrôle pour le modifier", "error");
			}
		});

		/** action remove data */
        $('#datatable tbody').on('click', 'tr', function(e) {
            var table = $('#datatable').DataTable();
            var data = table.row(this).data();

            $('#btn-remove').prop("href", "<?php echo site_url('controle_recurrents/remove');?>/" + data.controle_recurrents_id);

            //customize btn action une address
            var urlDetail = $('#controle_recurrents_detail a').attr('data-cible');
            $('#controle_recurrents_detail a').prop("href", urlDetail + "/" + data.controle_recurrents_id);
            var urlUpdate = $('#controle_recurrents_modification a').attr('data-cible');
            $('#controle_recurrents_modification a').prop("href", urlUpdate + "/" + data.controle_recurrents_id);
            var urlRemove = $('#controle_recurrents_supprimer a').attr('data-cible');
            $('#controle_recurrents_supprimer a').prop("href", urlRemove + "/" + data.controle_recurrents_id);

            $('.btn-single-action li').removeClass('disabled');
        });

		$('.k-grid').css('width', '1300px');		
	});	
	
	//this function will overwrite afterLoaded in kendogrid_js
	//function will be called after data is successfully rendered
	function afterLoaded(){
		//give text color if value Pas de Commande
		$('td[role="gridcell"] a:contains("Observations")').text('voir les observations');

		//kendo Window for textarea type data
		if($("div[class^=textarea-popup]").length){
			$("div[class^=textarea-popup]").kendoWindow({
				visible: false,
				modal: true,
				width: "75%",
				height: "75%"
			});
		}

		$('.copy-btn').click(function(){
			textarea = $(this).parent().find('textarea');
			textarea.select();
			document.execCommand('copy');
		})
	}	
</script>
<script>	
$(document).ready(function(){
	setTimeout(function() {	
		//extending print button
		var table = $('#datatable').DataTable();
		table.button(4).remove();	//we will customize print button so remove it first then readding again
		table.button().add( 4, {	//re adding the print button
			extend: 'print',
			text: 'Print',
			autoPrint: true,
			exportOptions: {
				columns: ':visible',
			},
			customize: function (win) {	
				if(groupValid) valid = 'VALIDE';
				else valid = 'NON VALIDEE';
				
				$(win.document.body).find("h1").after("<b><h2>"+groupName+" "+valid+"</b></h2>");
				container =	$(win.document.body).find("table");
				window.location = "";
			}
		} );
		
		//extending excel button
		table.button(1).remove();	//we will customize print button so remove it first then readding again
		table.button().add(1, {	//re adding the excel button
			extend: 'excelHtml5',
			text: 'Excel',		
			exportOptions: {
                format: {
                    header: function ( data, columnIdx ) {
                        if(data.search('check-all') > 0) {
                            var text = "";
                        } else {
                            var text = data.substring(data.indexOf("<span>")+6,data.indexOf("</span>"));
                        }

                        return text;
                    }
                }
            },
			customize: function(xlsx) {
				if(groupValid) valid = 'VALIDE';
				else valid = 'NON VALIDEE';
				
				var sheet = xlsx.xl.worksheets['sheet1.xml'];
				rownumber = 0;                               
				$('sheetData row', sheet).each( function () {
					//insert two rows for placing text in excel export
					rownumber = rownumber + 1;
					$(this).attr("r", rownumber+2);
					$(this).find("c").each(function(){
						thecolumn = $(this).attr("r");
						column_index = (parseInt(thecolumn.replace(/[a-zA-Z]/g, ''))+2);
						column_name = thecolumn.replace(/[0-9]/g, '');
						$(this).attr("r", column_name+column_index);
					})
				});
				//prepend title into excel
				$('sheetData', sheet).prepend(
					'<row r="1">'+
						'<c t="inlineStr" r="A1"><is><t/></is></c>'+
						'<c t="inlineStr" r="B1" s="22"><is><t>Controle Recurrent</t></is></c>'+
					'</row>'+
					'<row r="2">'+
						'<c t="inlineStr" r="A2"><is><t/></is></c>'+
						'<c t="inlineStr" r="B2" s="23"><is><t>'+groupName+' '+valid+'</t></is></c>'+
					'</row>'
				);
			},
		});		

		//extending pdf button   
        table.button(3).remove();   //we will customize pdf button so remove it first then readding again
        table.button().add( 3, {    //re adding the pdf button
            extend: 'pdfHtml5',
            text: 'Save PDF',
            exportOptions: {
                modifier: {         
                    columns: ':visible',
                },
               	format: {
                    header: function ( data, columnIdx ) {
                        if(data.search('check-all') > 0) {
                            var text = "";
                        } else {
                            var text = data.substring(data.indexOf("<span>")+6,data.indexOf("</span>"));
                        }

                        return text;
                    }
                }
                 
            },             
            pageSize: 'LEGAL',
            customize: function(doc) {           
                if(groupValid == "1") valid = 'VALIDE';
                else valid = 'NON VALIDEE';

                var content = doc.content;
                var groupTitle = groupName + " " + valid;
                var secondHeader = {text: groupTitle, style: 'title', marginBottom: 10};

                content.splice(1,0, secondHeader);
                content.join();
                doc.content = content;
                doc.styles.title.alignment = "left";
            }   
        });	

        
	}, 1000);
})	
</script>
<?php $this->load->view('templates/remove_confirmation_js.php'); ?>