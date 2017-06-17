<?php
	//url structure definition
	$gest_mode			= $this->uri->segment(2);
	$gest_employe_id	= $this->uri->segment(3);
	$gest_year			= $this->uri->segment(4);
	$gest_month			= $this->uri->segment(5);
	
	$annee	= 	array( 
					"2016","2017","2018","2019","2020",
				);
	$mois	= 	array( 
					1 	=> "01",
					2	=> "02",
					3 	=> "03",
					4 	=> "04",
					5 	=> "05",
					6 	=> "06",
					7	=> "07",
					8 	=> "08",
					9 	=> "09",
					10 	=> "10",
					11 	=> "11",
					12 	=> "12",
				);							
?>
<div class="valider modal fade" style="display: none">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Confirmation Valider Gestion Heures</h4>
            </div>
            <div class="modal-body">
				<div style="width: 90%">
					<form enctype="multipart/form-data" method="post" action="/">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<div class="form-group">
							<label for="action">Salarie</label>
							<input type="hidden" name="filter" value="1">
							<select name="employes" class="form-control input-sm" id="employes" required>
								<option value="">(choisissez)</option>
							<?php
								foreach ($data['employee_list'] as $row) {
									$selected = '';
									if ($this->input->get('employes') == $row->emp_id) {
										$selected = ' selected ';
									}
									echo '<option ' . $selected . ' value="' . $row->emp_id . '">' . $row->emp_nom . '</option>';
								}
							?>
							</select>
						</div>
						<div class="form-group">
							<label for="action">Annee</label>
							<select name="annee" class="form-control input-sm" id="annee" required>
								<option value="">(choisissez)</option>
								<?php 
								foreach ($annee as $row) {
									$selected = '';
									if ($this->input->get('annee') == $row) {
										$selected = ' selected ';
									}
									echo '<option ' . $selected . ' value="' . $row . '">' . $row . '</option>';
								}
								?>
							</select>
						</div>
						<div class="form-group">
							<label for="action">Mois</label>
							<select name="mois" class="form-control input-sm" id="mois" required>
								<option value="">(choisissez)</option>
								<?php
								foreach ($mois as $key => $value) {
									$selected = '';
									if ($this->input->get('mois') == $key) {
										$selected = ' selected ';
									}
									echo '<option ' . $selected . ' value="' . $key . '">' . $value . '</option>';
								}
								?>
							</select>
						</div>
						<div class="form-group">
							<input class="form-control" type="hidden" name="urbain_div"/>
						</div>
						<div class="form-group">
							<input class="form-control" type="hidden" name="rural_div"/>
						</div>
						<input type="submit" class="btn btn-default btn-default nouveau_gestion" value="OK">						
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="popup_gestion_heures_valides" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Confirmation Valider Gestion Heures</h4>
            </div>
            <div class="modal-body">
                Êtes-vous sûr
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal" id="popup_gestion_heures_valides_do">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="popup_gestion_heures_devalider" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Confirmation Devalider Gestion Heures</h4>
            </div>
            <div class="modal-body">
                Êtes-vous sûr
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal" id="popup_gestion_heures_devalider_do">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="gestion_heures_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Feuilles d'heures</h4>
            </div>
            <div class="modal-body">
				
            </div>
        </div>
    </div>
</div>

<script>
//add custom method for dataTables
$.fn.dataTable.Api.register( 'column().data().sum()', function () {
    return this.reduce( function (a, b) {
        var x = parseFloat( a ) || 0;
        var y = parseFloat( b ) || 0;
        return x + y;
    },0);
});
var groupValid = "<?php echo  isset($data['group_valid']) ? $data['group_valid'] : null;?>";

	/**
     * Mass action group
     * @return {[type]} [description]
     */
    function massActionGroup(){
        var action = $("#sel_action_all_group" ).val();
        theid = {};
        $("input:checkbox:checked").each(function(i){
            theid[i] = $(this).val();
            $(this).parent().parent().fadeOut();
        });
        $.ajax({
            type: "POST",
            url: "<?php echo site_url($this->uri->segment(1)); ?>/mass_"+action,
            data: {ids: JSON.stringify(theid)},
        });
        $('#modal-form-remove-group').modal('hide');
    }

	$(document).ready(function(){
				
		//mass action group
        $("#btn_action_all_group").click(function(e) {
            var action = $("#sel_action_all_group" ).val();
            if(action == 'remove_group')
                $('#modal-form-remove-group').modal('show');
            else
                massActionGroup();
        });

		if(groupValid == "1")
			$('#gestion_heures_valides').addClass('disabled');	
		else
			$('#gestion_heures_devalider').addClass('disabled');					
		
		//view all button to list all result
        $('#voir_liste a, #gestion_heures_voir_liste a').click(function(e) {
            e.preventDefault();
            console.log("show all");
            var isList = $(this).attr('data-list');
            var table = $('#datatable');
            var setting = table.DataTable().init();
            var textHtml = $(this).html();

            if(isList) {
                setting.iDisplayLength = 100;
                setting.sScrollY = 575;
                setting.scroller = {loadingIndicator: true};
                setting.bPaginate = true;
                table.DataTable().destroy();
                table.DataTable(setting);
                textHtml = textHtml.replace("Défaut la liste", "Voir la liste");
                $(this).html(textHtml);
                $(this).removeAttr("data-list");
            } else {
                delete setting.scrollY;
                delete setting.scroller;
                setting.sScrollY = false;
                setting.iDisplayLength = -1;
                setting.bPaginate = false;
                table.DataTable().destroy();
                table.DataTable(setting);
                textHtml = textHtml.replace("Voir la liste", "Défaut la liste");
                $(this).html(textHtml);
                $(this).attr("data-list", true);
            }
        });
        
		$('#grid').css('width', '1310px');

		/*
		$(".valider").kendoWindow({
			visible: false,
			modal: true,
			width: "500px"
		});								
		$(".valider").data("kendoWindow").title("Valider Gestion Heures");
		*/
		
		//set amalgame data into specific amalgame group
		$("#gestion_heures_valider, #gestion_heures_nouveau").click(function(e){
			e.preventDefault();
			var urbainDiv = $('#input_urbain_div').val() != '' ? $('#input_urbain_div').val() : 150;
			var ruralDiv = $('#input_rural_div').val() != '' ? $('#input_rural_div').val() : 120;
			$('input[name=urbain_div]').val(urbainDiv);
			$('input[name=rural_div]').val(ruralDiv);
			$(".valider").modal('show');
		})		
		
		$('.nouveau_gestion').click(function(e){
			e.preventDefault();
			var employes = $('.valider #employes').val();
			var annee = $('.valider #annee').val();
			var mois = $('.valider #mois').val();

			if(employes !== '' && annee !== '' && mois !== '') {
				window.location = '<?php echo site_url('gestion_heures/salarie'); ?>/'+ employes
									+'/'+ annee
									+'/'+ mois
			} else {
				//$(".valider").data("kendoWindow").close();
			}
		})
		
		$("#gestion_heures_devalider").click(function(e){
			e.preventDefault();
			$('#popup_gestion_heures_devalider').modal('show');
			return false;
		})
		$("#gestion_heures_valides").click(function(e){
			e.preventDefault();
			$('#popup_gestion_heures_valides').modal('show');
			return false;
		})		
		$("#popup_gestion_heures_devalider_do").click(function(e) {
			window.location = "<?php echo site_url('gestion_heures/unset_valider/'.$gest_employe_id.'/'.$gest_year.'/'.$gest_month); ?>";
		});		
		$("#popup_gestion_heures_valider_do").click(function(e) {
			window.location = "<?php echo site_url('gestion_heures/set_valider/'.$gest_employe_id.'/'.$gest_year.'/'.$gest_month); ?>";
		});				
		$("#popup_gestion_heures_valides_do").click(function(e) {
			window.location = "<?php echo site_url('gestion_heures/valides/'.$gest_employe_id.'/'.$gest_year.'/'.$gest_month); ?>";
		});		

		$('#view_gestion_heures').click(function(e) {
			e.preventDefault();
			$('#gestion_heures_list').modal('show');
			$.post('<?php echo site_url('gestion_heures/get_group_list/'); ?>', $('#get_group_form').serialize())
				.done(function( data ) {
					$('#gestion_heures_list .modal-body').html(data);
				});
		});

		//change action urban divisions
		$('#input_urbain_div').change(function(e) {
			var urbainDiv = parseFloat($(this).val());

			if(urbainDiv != null && !isNaN(urbainDiv)) {
				$.post("<?php echo site_url('gestion_heures/set_urbain_group/'.$gest_employe_id.'/'.$gest_year.'/'.$gest_month);?>", {urbain_div: urbainDiv}, function(response){
			        if(response.status == true) {
				  //       for (var i = 1; i < $('#datatable tr').length; i++) {
						// 	var urbain = parseFloat($('#datatable tbody tr:nth-child('+i+') td:nth-child(3)').text());

						// 	var resultat = (urbain / urbainDiv).toFixed(2);
						// 	$('#datatable tbody tr:nth-child('+i+') td:nth-child(4)').text(resultat)
						// }	
						var table = $('#datatable').DataTable();
						var data = table.rows().data();
						for(var i = 0; i < data.length; i ++) {
							var urbain = parseFloat(data[i].urbain);
							var resultat = (urbain / urbainDiv).toFixed(2);

							data[i].heures_de_distribution_urbain = resultat;
						}				

						table.rows().draw();
			        }
			    });
			}
		});

		$('#input_rural_div').change(function(e) {
			var ruralDiv = parseFloat($(this).val());

			if(ruralDiv != null && !isNaN(ruralDiv)) {
				$.post("<?php echo site_url('gestion_heures/set_rural_group/'.$gest_employe_id.'/'.$gest_year.'/'.$gest_month);?>", {rural_div: ruralDiv}, function(response){
					if(response.status == true) {
						var table = $('#datatable').DataTable();
						var data = table.rows().data();
						for(var i = 0; i < data.length; i ++) {
							var rural = parseFloat(data[i].rural);
							var resultat = (rural / ruralDiv).toFixed(2);

							data[i].heures_de_distribution_rural = resultat;
						}				

						table.rows().draw();							
			        }
			    });
			}
		});
		
		//$('#gestion_heures_nouveau a').attr('href', '<?php echo site_url('gestion_heures/nouveau/'.$gest_employe_id.'/'.$gest_year.'/'.$gest_month); ?>')
		
		$('#recherge_gestion_heures').click(function(e){
			e.preventDefault();
			$('#gestion_des_lignes').fadeToggle();
		})

		//change list view
		$('#sel_view').change(function(){
			window.location = ($('#sel_view option:selected').val());
		})		

        /** action one data */
        $('#datatable tbody').on('click', 'tr', function(e) {
            var table = $('#datatable').DataTable();
            var data = table.row(this).data();

            $('#btn-remove').prop("href", "<?php echo site_url('gestion_heures/remove');?>/" + data.gestion_heures_id);

            //customize btn action une address
            var urlDetail = $('#gestion_heures_detail a').attr('data-cible');
            $('#gestion_heures_detail a').prop("href", urlDetail + "/" + data.gestion_heures_id);
            var urlUpdate = $('#gestion_heures_modification a').attr('data-cible');
            $('#gestion_heures_modification a').prop("href", urlUpdate + "/" + data.gestion_heures_id);
            var urlRemove = $('#gestion_heures_supprimer a').attr('data-cible');
            $('#gestion_heures_supprimer a').prop("href", urlRemove + "/" + data.gestion_heures_id);

            $('.btn-single-action li').removeClass('disabled');
        });

        $('#gestion_heures_supprimer').click(function(e) {
            e.preventDefault();
            $('#modal-form-remove').modal('show');
        });
        
        //custom header checkbox
        setTimeout(function() {
            customHeaderCheckbox();
            var table = $('#datatable').DataTable();
        }, 500);


		<?php if($gest_mode == 'salarie'): ?>
        var old_init = dt_persist_state;
        dt_persist_state = function() {			
        	var footer = document.getElementById('footer-datatable-gestion');
        	if(footer == null) {
				addTotalFooter($('#datatable'));
  			}
			old_init.apply(this, arguments);
        };
		

        setTimeout(function() {			        
        	var table = $('#datatable').DataTable();
        	table.columns.adjust().draw();
        }, 1000);

        $('body').on('keyup', '#nombre-heures', function(e) {
        	var input = $(this).val();
        	var totalHeuresTravaillees = parseFloat($('#total-heures-travaillees').text());        	

        	if(e.keyCode == 13) {
	        	if(!isNaN(input) && input != "") {
	        		input = parseFloat(input);
	        		var result = (input - totalHeuresTravaillees);

	        		if(result > 0) {
	        			$('#nombre-absence').text(result.toFixed(2));
	        			$('#nombre-supplementaires').text("");
	        		} else {
	        			$('#nombre-absence').text("");
	        			$('#nombre-supplementaires').text(result.toFixed(2));
	        		}

	        		//save nombre de heures into gestion_group 
	        		$.post("<?php echo site_url('gestion_heures/set_nombre_de_heures/'.$gest_employe_id.'/'.$gest_year.'/'.$gest_month);?>", {nombre_de_heures: input}, function(e) {

	        		}, "json");

	        		//calculate total footer used export PDF
        			sessionStorage.setItem("nombreDeHeures", input);
        			calculateNombre();

	        	} else {
	        		$('#nombre-absence').text("");
	        		$('#nombre-supplementaires').text("");
	        	}
        	}
        });
		
		<?php endif; ?>

        $('#valider_view, #non_valider_view').change(function(e) {
        	e.preventDefault();
			var url = $(this).val();

			if(url != "") {
				window.location = url;
			}      
        });

        $('#gestion_heures_tableau_ik_urssaf a').click(function(e) {
        	e.preventDefault();

        	$.get("<?php echo site_url('gestion_heures/get_tableau_ik_urssaf');?>", function(result) {
        		if(result) {
        			var rows = "";
        			for(var i =0; i < result.length; i++) {
        				var row = "<tr>";
        				for(var j =0; j < result[i].length; j++) {
        					if(j == 0){
        						row += "<td class='info'>" +result[i][j]+ "</td>";
        					} else {
        						row += "<td>" +result[i][j]+ "</td>";
        					}
        				}

        				row += "</tr>";
						
        				rows += row;
        			}

        			$('#tableau-ik-urssaf tbody').html(rows);
        			$('#modal-tableau-ik-urssaf').modal('show');
        		}        		
        	}, "json");
        	
        });
    });	


if (typeof(Storage) !== "undefined") {
	sessionStorage.nombreDeHeures = "<?php echo (isset($data['nombre_de_heures'])?$data['nombre_de_heures']:'') ; ?>";
	sessionStorage.nombreAbsence = 0;
	sessionStorage.nombreSupplementaires = 0;	
	sessionStorage.totalHeuresTravaillees = 0;					
}


function calculateNombre() {
	var totalHeuresTravaillees = sessionStorage.getItem("totalHeuresTravaillees");
	var nombreDeHeures = sessionStorage.getItem("nombreDeHeures");
	var nombreAbsence = sessionStorage.getItem("nombreAbsence");
	var nombreSupplementaires = sessionStorage.getItem("nombreSupplementaires");

	if(nombreDeHeures != 0) {
		var result = (parseFloat(nombreDeHeures) - totalHeuresTravaillees).toFixed(2);
		if(result > 0) {
			sessionStorage.setItem("nombreAbsence", result);
			sessionStorage.setItem("nombreSupplementaires", 0);
		} else {
			sessionStorage.setItem("nombreAbsence", 0);
			sessionStorage.setItem("nombreSupplementaires", result);
		}
	}
}


$(document).ready(function(){	
	//columnt total calculation
	var table = $('#datatable').DataTable();
	var totalRows = table.rows().indexes();
	if(totalRows.length > 0 ) {	        		
		var col3 = table.column( 3, {page:'current'} ).data().sum().toFixed(2);
		var col4 = table.column( 4, {page:'current'} ).data().sum().toFixed(2);
		var col5 = table.column( 5, {page:'current'} ).data().sum().toFixed(2);
		var col6 = table.column( 6, {page:'current'} ).data().sum().toFixed(2);
		var col7 = table.column( 7, {page:'current'} ).data().sum().toFixed(2);
		var col8 = table.column( 8,  {page:'current'} ).data().sum().toFixed(2);
		var col9 = table.column( 9, {page:'current'} ).data().sum().toFixed(2);
		var totalBoitesDistribuees = (parseFloat(col3) + parseFloat(col5)).toFixed(2);
		var totalHeuresTravaillees = (parseFloat(col4) + parseFloat(col6) + parseFloat(col7)).toFixed(2);
		var nombre_de_heures = '<?php echo (isset($data['nombre_de_heures'])?$data['nombre_de_heures']:'') ; ?>';
		var inputNombreHeures = '<input type="text" id="nombre-heures" style="color:black" value="'+nombre_de_heures+'" />';

		var nombre_absence = 0;
		var nombre_supplementaires = 0;
		if(nombre_de_heures != 0) {
			var result = (parseFloat(nombre_de_heures) - totalHeuresTravaillees).toFixed(2);
			if(result > 0) {
				nombre_absence = result;
			} else {
				nombre_supplementaires = result;
			}
		}		
	}
	
	window.test = col3;
	
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
				if(groupValid) valid = 'VALIDEE';
				else valid = 'NON VALIDEE';
				$(win.document.body).find("h1").after("<b><h2><?php echo $data['group_name']; ?> "+valid+"</b></h2>");
				container =	$(win.document.body).find("table");
				addTotalFooter(container);
				window.location = "";
			}
		} );
		
		// //extending excel button
		// table.button(1).remove();	//we will customize print button so remove it first then readding again
		// table.button().add(1, {	//re adding the excel button
		// 	extend: 'excelHtml5',
		// 	text: 'Excel',
		// 	exportOptions: {
  //               format: {
  //                   header: function ( data, columnIdx ) {
  //                       if(data.search('check-all') > 0) {
  //                           var text = "";
  //                       } else {
  //                           var text = data.substring(data.indexOf("<span>")+6,data.indexOf("</span>"));
  //                       }

  //                       return text;
  //                   }
  //               }
  //           },
		// 	customizeData: function ( data ) {
		// 		var table = $('#datatable').DataTable();
		// 		var totalRows = table.rows().indexes();
		// 		if(totalRows.length > 0 ) {	        		
		// 			var col3 = table.column( 3, {page:'current'} ).data().sum().toFixed(2);
		// 			var col4 = table.column( 4, {page:'current'} ).data().sum().toFixed(2);
		// 			var col5 = table.column( 5, {page:'current'} ).data().sum().toFixed(2);
		// 			var col6 = table.column( 6, {page:'current'} ).data().sum().toFixed(2);
		// 			var col7 = table.column( 7, {page:'current'} ).data().sum().toFixed(2);
		// 			var col8 = table.column( 8,  {page:'current'} ).data().sum().toFixed(2);
		// 			var col9 = table.column( 9, {page:'current'} ).data().sum().toFixed(2);
		// 			var totalBoitesDistribuees = (parseFloat(col3) + parseFloat(col5)).toFixed(2);
		// 			var totalHeuresTravaillees = (parseFloat(col4) + parseFloat(col6) + parseFloat(col7)).toFixed(2);
		// 			var nombre_de_heures = '<?php echo (isset($data['nombre_de_heures'])?$data['nombre_de_heures']:'') ; ?>';
		// 			var inputNombreHeures = '<input type="text" id="nombre-heures" style="color:black" value="'+nombre_de_heures+'" />';

		// 			if(nombre_de_heures != 0) {
		// 				console.log(nombre_de_heures)
		// 				var result = (parseFloat(nombre_de_heures) - totalHeuresTravaillees).toFixed(2);
		// 				if(result > 0) {
		// 					var nombre_absence = result;
		// 					var nombre_supplementaires = 0;
		// 				} else {
		// 					var nombre_absence = 0;
		// 					var nombre_supplementaires = result;
		// 				}
		// 			} else {
		// 				var nombre_absence = 0;
		// 				var nombre_supplementaires = 0;
		// 			}		
		// 		}
	

		// 		lastcolumn = (data.body.length);
				
		// 		//manually add total at the end of array
		// 		console.log(lastcolumn);
		// 		data.body[lastcolumn] = [
		// 									 ''
		// 									 ,'Total'
		// 									 ,''
		// 									 ,col3
		// 									 ,col4
		// 									 ,col5
		// 									 ,col6
		// 									 ,col7
		// 									 ,''
		// 									 ,col9
		// 								];
		// 		data.body[(lastcolumn+1)] = [
		// 									 ''
		// 									 ,'Total Boites Distribuees'
		// 									 ,''
		// 									 ,totalBoitesDistribuees
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 								];										
		// 		data.body[(lastcolumn+2)] = [
		// 									 ''
		// 									 ,'Total Heures Travaillees'
		// 									 ,''
		// 									 ,totalHeuresTravaillees
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 								];	
		// 		data.body[(lastcolumn+3)] = [
		// 									 ''
		// 									 ,'Nombre D\'heures Devant Etre Travaillees Dans Le Mois'
		// 									 ,''
		// 									 ,nombre_de_heures
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 								];	
		// 		data.body[(lastcolumn+4)] = [
		// 									 ''
		// 									 ,'Nombre D\'heures Absence'
		// 									 ,''
		// 									 ,nombre_absence
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 								];	
		// 		data.body[(lastcolumn+5)] = [
		// 									 ''
		// 									 ,'Nombre D\'heures Supplementaires'
		// 									 ,''
		// 									 ,nombre_supplementaires
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 									 ,''
		// 								];											
		// 	},			
		// 	customize: function(xlsx) {
		// 		var sheet = xlsx.xl.worksheets['sheet1.xml'];
		// 		rownumber = 0;                               
		// 		$('sheetData row', sheet).each( function () {
		// 			//insert two rows for placing text in excel export
		// 			rownumber = rownumber + 1;
		// 			$(this).attr("r", rownumber+2);
		// 			$(this).find("c").each(function(){
		// 				thecolumn = $(this).attr("r");
		// 				column_index = (parseInt(thecolumn.replace(/[a-zA-Z]/g, ''))+2);
		// 				column_name = thecolumn.replace(/[0-9]/g, '');
		// 				$(this).attr("r", column_name+column_index);
		// 			})
		// 		});
		// 		//prepend title into excel
		// 		$('sheetData', sheet).prepend(
		// 			'<row r="1">'+
		// 				'<c t="inlineStr" r="A1"><is><t/></is></c>'+
		// 				'<c t="inlineStr" r="B1" s="22"><is><t>Comptes-rendus Salariés</t></is></c>'+
		// 			'</row>'+
		// 			'<row r="2">'+
		// 				'<c t="inlineStr" r="A2"><is><t/></is></c>'+
		// 				'<c t="inlineStr" r="B2" s="23"><is><t><?php echo $data['group_name']; ?></t></is></c>'+
		// 			'</row>'
		// 		);
		// 	},
		// });		

		//extending pdf button	
		table.button(3).remove();	//we will customize pdf button so remove it first then readding again
		table.button().add( 3, {	//re adding the pdf button
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
		   	orientation: 'landscape',
		   	pageSize: 'LEGAL',
		   	customize: function(doc) {
		   	  var datatable = $('#datatable').DataTable();
			  var total = [];
		   	  //sum specific column			
		   	  total[0]  = "";
		   	  total[1]  = "";
		   	  total[2]  = "";
		   	  total[3]  = datatable.column( 3, {page:'current'} ).data().sum().toFixed(2);
		      total[4]  = datatable.column( 4, {page:'current'} ).data().sum().toFixed(2);
		      total[5]  = datatable.column( 5, {page:'current'} ).data().sum().toFixed(2);
		      total[6]  = datatable.column( 6, {page:'current'} ).data().sum().toFixed(2);
		      total[7]  = datatable.column( 7, {page:'current'} ).data().sum().toFixed(2);
		      total[8]  = "";
		      total[9]  = datatable.column( 9, {page:'current'} ).data().sum().toFixed(2);
		      var totalBoitesDistribuees = (parseFloat(total[3]) + parseFloat(total[5])).toFixed(2);
			  var totalHeuresTravaillees = (parseFloat(total[4]) + parseFloat(total[6]) + parseFloat(total[7])).toFixed(2);
		   	    
		      
		      var table = doc.content[1].table;	
		      var body = table.body;	   	  
		      var footer1 = [];
		      var footer2 = [];
		      var footer3 = [];
		      var footer4 = [];
		      var footer5 = [];
		      var footer6 = [];

		      //append data to footer 1st
		   	  for(var i=0; i < 10; i++) {		   	  	
		   	  	if(i == 0) {
		   	  		footer1.push({
			   	  		text: "Total",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 3,
			   	  	});
		   	  	}else {
		   	  		footer1.push({
			   	  		text: total[i],
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  	});
		   	  	}		   	 
		   	  }

		   	  //append data to footer 2nd
		   	  for(var i=0; i < 10; i++) {		   	  	
		   	  	if(i == 0) {
		   	  		footer2.push({
			   	  		text: "Total Boites Distribuees",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 3,
			   	  	});
		   	  	} else if(i == 3) {
		   	  		footer2.push({
			   	  		text: totalBoitesDistribuees,
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 7,
			   	  	});
		   	  	} else {
		   	  		footer2.push({
			   	  		text: "",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  	});
		   	  	}		   	 
		   	  }

		   	   //append data to footer 3rd
		   	  for(var i=0; i < 10; i++) {		   	  	
		   	  	if(i == 0) {
		   	  		footer3.push({
			   	  		text: "Total Heures Travaillees",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 3,
			   	  	});
		   	  	} else if(i == 3) {
		   	  		footer3.push({
			   	  		text: totalHeuresTravaillees,
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 7,
			   	  	});
		   	  	} else {
		   	  		footer3.push({
			   	  		text: "",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  	});
		   	  	}		   	 
		   	  }

		   	   //append data to footer 4th
		   	  for(var i=0; i < 10; i++) {		   	  	
		   	  	if(i == 0) {
		   	  		footer4.push({
			   	  		text: "Nombre D'heures Devant Etre Travaillees Dans Le Mois",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 3,
			   	  	});
		   	  	} else if(i == 3) {
		   	  		footer4.push({
			   	  		text: sessionStorage.getItem("nombreDeHeures"),
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 7,
			   	  	});
		   	  	} else {
		   	  		footer4.push({
			   	  		text: "",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  	});
		   	  	}		   	 
		   	  }

		   	   //append data to footer 5th
		   	  for(var i=0; i < 10; i++) {		   	  	
		   	  	if(i == 0) {
		   	  		footer5.push({
			   	  		text: "Nombre D'heures Absence",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 3,
			   	  	});
		   	  	} else if(i == 3) {
		   	  		footer5.push({
			   	  		text: sessionStorage.getItem("nombreAbsence"),
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 7,
			   	  	});
		   	  	} else {
		   	  		footer5.push({
			   	  		text: "",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  	});
		   	  	}		   	 
		   	  }

		   	   //append data to footer 6th
		   	  for(var i=0; i < 10; i++) {		   	  	
		   	  	if(i == 0) {
		   	  		footer6.push({
			   	  		text: "Nombre D'heures Supplementaires",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 3,
			   	  	});
		   	  	} else if(i == 3) {
		   	  		footer6.push({
			   	  		text: sessionStorage.getItem("nombreSupplementaires"),
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 7,
			   	  	});
		   	  	} else {
		   	  		footer6.push({
			   	  		text: "",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  	});
		   	  	}		   	 
		   	  }

		   	  body.push(footer1, footer2, footer3, footer4, footer5, footer6);
		   	  table.body = body;

		   	  doc.content[1].table = table;
		   	  //doc.content[1].layout = null;		   	 
			  //doc.defaultStyle.fontSize = 6; //<-- set fontsize to 16 instead of 10 
		      //doc.styles.tableHeader.fontSize = 6;
		      //doc.styles.tableFooter.fontSize = 6;

		      if(groupValid == "1") valid = 'VALIDEE';
				else valid = 'NON VALIDEE';

		      var groupTitle = "<?php echo $data['group_name'];?> " + valid;
		      var secondHeader = {text: groupTitle, style: 'title', marginBottom: 10};

		      var content = doc.content;
		      content.splice(1,0, secondHeader);
              content.join();
              doc.content = content;
              doc.styles.title.alignment = "left";
		  	    
		   	}  	
		});

		var tableToCalculate = $('#datatable').DataTable();
		var totalKilometres = tableToCalculate.column( 8, {page:'current'} ).data().sum().toFixed(2);
		$.get('<?php echo site_url('gestion_heures/calculate_indem_kilo/'.$gest_employe_id);?>/'+ totalKilometres, function(data) {
			var indemKilo = data.indem_kilo;
			console.log(indemKilo.toString());
			$('input[name=indemnite_kilometrique]').val(indemKilo);
		},'json');

	}, 2000);
})	
function addTotalFooter(container){
	var table = $('#datatable').DataTable();
	var totalRows = table.rows().indexes();
	if(totalRows.length > 0 ) {	        		
		var col3 = table.column( 3, {page:'current'} ).data().sum().toFixed(2);
		var col4 = table.column( 4, {page:'current'} ).data().sum().toFixed(2);
		var col5 = table.column( 5, {page:'current'} ).data().sum().toFixed(2);
		var col6 = table.column( 6, {page:'current'} ).data().sum().toFixed(2);
		var col7 = table.column( 7, {page:'current'} ).data().sum().toFixed(2);
		var col9 = table.column( 9, {page:'current'} ).data().sum().toFixed(2);
		var totalBoitesDistribuees = (parseFloat(col3) + parseFloat(col5)).toFixed(2);
		var totalHeuresTravaillees = (parseFloat(col4) + parseFloat(col6) + parseFloat(col7)).toFixed(2);
		var nombre_de_heures = '<?php echo (isset($data['nombre_de_heures'])?$data['nombre_de_heures']:'') ; ?>';
		var inputNombreHeures = '<input type="text" id="nombre-heures" style="color:black" value="'+nombre_de_heures+'" />';

		var nombre_absence = 0;
		var nombre_supplementaires = 0;
		if(nombre_de_heures != 0) {
			var result = (parseFloat(nombre_de_heures) - totalHeuresTravaillees).toFixed(2);
			if(result > 0) {
				nombre_absence = result;
			} else {
				nombre_supplementaires = result;
			}
		}

		//calculate total footer used export PDF
		sessionStorage.setItem("totalHeuresTravaillees", totalHeuresTravaillees);
		calculateNombre();

		container.append(
			'<tfoot id="footer-datatable-gestion" style="font-size:10px"><tr><th colspan=3>Total</th>'
				+'<th>'+col3+'</th>'
				+'<th>'+col4+'</th>'
				+'<th>'+col5+'</th>'
				+'<th>'+col6+'</th>'
				+'<th>'+col7+'</th>'
				+'<th></th>'
				+'<th>'+col9+'</th>'
			+'</tr>'
			+'<tr><th colspan=3>Total Boites Distribuees</th><th colspan=7>'+ totalBoitesDistribuees +'</th></tr>'
			+'<tr><th colspan=3>Total Heures Travaillees</th><th colspan="7" id="total-heures-travaillees">'+ totalHeuresTravaillees +'</th></tr>'
			+'<tr><th colspan=3>Nombre D\'heures Devant Etre Travaillees Dans Le Mois</th><th>' +inputNombreHeures+ '</t><th colspan=6></th></tr>'
			+'<tr><th colspan=3>Nombre D\'heures Absence</th><th colspan="7" id="nombre-absence">'+nombre_absence+'</th></tr>'
			+'<tr><th colspan=3>Nombre D\'heures Supplementaires</th><th colspan="7" id="nombre-supplementaires">'+nombre_supplementaires+'</th></tr>'
			+'</tfoot>'
		);		  											  		
		container.after(
			'<style>'+
				'tfoot th{ padding: 0px !important}'+
			'</style>'
		)
	}	
}

var originExportXls = exportXls;
exportXls = function(url, data, callback) {
	data.nombreDeHeures = $('#nombre-heures').val();
	return originExportXls(url, data, callback);
};
</script>
<?php $this->load->view('templates/remove_confirmation_js.php'); ?>