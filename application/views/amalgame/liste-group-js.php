<?php 
//url structure definition
$view_mode		= $this->uri->segment(4);
$amalgame_name	= $this->uri->segment(3);
$controle_mode  = $this->uri->segment(2);
	
//set valider name as current date
$valider_name = date('d-F-Y');

//when we are on nonvalides list then 
//set valides name to its own name without NV
if(preg_match('/-NV/', current_url())){
	$valider_name = substr($amalgame_name, 0, -3);
}
?>

<script>
	//add custom method for dataTables
	$.fn.dataTable.Api.register( 'column().data().sum()', function () {
	    return this.reduce( function (a, b) {
	        var x = parseFloat( a ) || 0;
	        var y = parseFloat( b ) || 0;
	        return x + y;
	    },0);
	});
	var groupValid = "<?php echo  isset($data) ? $data['group_valid'] : null;?>";

	function setDateName(dateString) {

		var dateStringArr = dateString.split('/');
		dateString = dateStringArr[2] + "-" + dateStringArr[1] + "-" + dateStringArr[0];

		var monthNames = ["January", "February", "March", "April", "May", "June",
		  "July", "August", "September", "October", "November", "December"
		];
		var date  = new Date(dateString).getDate();
		var month = new Date(dateString).getMonth();
		month     = monthNames[month];
		var year  = new Date(dateString).getFullYear();

		var dateFormatted = date + "-" + month + "-" + year;

		return dateFormatted;
	}

	/**
	 * generate amalgame name from input form ajouter un amalgame
	 */
	function setAmalgameName() {
       	var date_livraison_reelle = $('#date_livraison_reelle').val() ? $('#date_livraison_reelle').val() : "";
		var name = "";
        if(date_livraison_reelle != "") {
            name += setDateName(date_livraison_reelle);
        }
        $('#amalgame_name').val(name);
    }

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
		$('#modal-form-remove').modal('hide');
	}

    $(document).ready(function() {

    	$('#date_de_livraison_del_amalgame, #date_envoi_bat_global, #date_livraison_reelle').datetimepicker({
    		format:'d/m/Y',
	    	formatDate:'d/m/Y',
	    	defaultDate:'+01/01/1970',
	    	timepicker:false
    	}); 

		<?php 
			$data['group_valid'] = isset($data['group_valid']) ? $data['group_valid'] : 'undefined';
			if($data['group_valid']){ ?>
			$('#amalgame_valider').addClass('disabled');
		<?php } 
			if($data['group_valid'] == 0){ ?>
			$('#amalgame_devalider').addClass('disabled');
		<?php } ?>
		
    	//view all button to list all result
        $('#amalgame_voir_liste a').click(function(e) {
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
		
		$('#amalgame_liste_all a').attr('href', '<?php echo site_url('amalgame/valider/all'); ?>');
		$('#amalgame_nonvalider a').attr('href', '<?php echo site_url('amalgame/valider/Non%20Valid%C3%A9es'); ?>');

		//set amalgame name when input on modal form changed
        $('#popup_amalgame_ajouter').change(function(e) {
            setAmalgameName();
        });

        //mass action group
		$("#btn_action_all_group").click(function(e) {
			var action = $("#sel_action_all_group" ).val();
			if(action == 'remove_group')
				$('#modal-form-remove').modal('show');
			else
				massActionGroup();
		});


    });
</script>
<script>
	$(document).ready(function(){			
		//set amalgame data into specific amalgame group
		$("#amalgame_valider").click(function(e){
			e.preventDefault();
			$('#popup_amalgame_valider').modal('show');
			return false;
		});

		$("#popup_amalgame_valider_do").click(function(e) {
			window.location = "<?php echo site_url('amalgame/set_valider').'/'.$amalgame_name; ?>";
		});	
		
		//unset amalgame data from group
		$("#amalgame_devalider").click(function(e){
			e.preventDefault();
			$('#popup_amalgame_devalider').modal('show');
			return false;			
			
		})
		$("#popup_amalgame_devalider_do").click(function(e) {
			window.location = "<?php echo site_url('amalgame/unset_valider').'/'.$amalgame_name;?>";
		});			
		
		//show modal form amalgame ajouter
		$("#amalgame_ajouter").click(function(e){
			e.preventDefault();
			$('#popup_amalgame_ajouter').modal('show');
			return false;
		});
		
		
		//Change view between valider set
		$("#valider_view").change(function(){
			window.location = ($("#valider_view option:selected").val());
		})
		$("#non_valider_view").change(function(){
			window.location = ($("#non_valider_view option:selected").val());
		})
		
		//change list view
		$('#sel_view').change(function(){
			window.location = ($('#sel_view option:selected').val());
		})		
		
        //action to open modal form upload file
        $('#datatable tbody').on('click', '.btn-upload-file', function(e) {
          e.preventDefault();
          var id = $(this).attr('data-id');
          $('#upload_id').val(id);
          $('#modal-form-upload').modal('show');
        });		
		
        //pull commande data when client select is changed
		$("#client").change(function(){
			$.get("<?php echo site_url('amalgame'); ?>/commande_option/"+$("#client").val(), function(data){
				$("#commande").html(data);
			});
		})
		
		$('#qty, #largeur, #longueur').focusout(function(){
			calculate_a5();
		})				
		
		$('#denomination_taille, #denomination_taille_ferme').change(function(){
			calculate_a5();
		})

        $('#amalgame_supprimer').click(function(e) {
            e.preventDefault();
           	$('#modal-form-remove').modal('show');	           
        });
        /** eof action remove data */
		
		/*
		$('#amalgame_liste').click(function(e) {
            e.preventDefault();
            $('#popup_liste_amalgame').modal('show');
        });
		$('#popup_liste_amalgame_do').click(function(e) {
			e.preventDefault();
			window.location = "<?php echo site_url('amalgame/filter_date_livraisons'); ?>/"+$('#date_livraison_reelle_filter').val();
		});
		*/
		
		$('#amalgame_liste').click(function(e) {
            e.preventDefault();
            window.location = "<?php echo site_url('amalgame/'); ?>";
        });

        <?php if($controle_mode == 'group'): ?>
        var old_init = dt_persist_state;
        dt_persist_state = function() {
        	var footer = document.getElementById('footer-datatable');
        	if(footer == null) {
	        	addTotalFooter($("#datatable"));
  			}
			old_init.apply(this, arguments);
        };
        setTimeout(function() {			        
        	var table = $('#datatable').DataTable();
        	table.columns.adjust().draw();
        }, 1000);
        <?php endif; ?>
    });					
	
	function calculate_a5(){
		size = $('#denomination_taille option:selected').val();	
		if(size == 'A1'){ 		$('#largeur').val(59.4);	$('#longueur').val(84.1) }
		if(size == 'A2'){ 		$('#largeur').val(42);		$('#longueur').val(59.4) }
		if(size == 'A3'){ 		$('#largeur').val(29.7);	$('#longueur').val(42) }
		if(size == 'A4'){ 		$('#largeur').val(21);		$('#longueur').val(29.7) }
		if(size == 'A5'){ 		$('#largeur').val(14.8);	$('#longueur').val(21) }
		if(size == 'DIN LONG'){ $('#largeur').val(14.8);	$('#longueur').val(21) }
		if(size == 'A6'){ 		$('#largeur').val(10.5);	$('#longueur').val(14.8) }
		if(size == 'A7'){ 		$('#largeur').val(7.4);		$('#longueur').val(10.5) }
		
		size = $('#denomination_taille_ferme option:selected').val();	
		if(size == 'A1'){ 		$('#largeur_ferme').val(59.4);	$('#longueur_ferme').val(84.1) }
		if(size == 'A2'){ 		$('#largeur_ferme').val(42);	$('#longueur_ferme').val(59.4) }
		if(size == 'A3'){ 		$('#largeur_ferme').val(29.7);	$('#longueur_ferme').val(42) }
		if(size == 'A4'){ 		$('#largeur_ferme').val(21);	$('#longueur_ferme').val(29.7) }
		if(size == 'A5'){ 		$('#largeur_ferme').val(14.8);	$('#longueur_ferme').val(21) }
		if(size == 'DIN LONG'){ $('#largeur_ferme').val(14.8);	$('#longueur_ferme').val(21) }
		if(size == 'A6'){ 		$('#largeur_ferme').val(10.5);	$('#longueur_ferme').val(14.8) }
		if(size == 'A7'){ 		$('#largeur_ferme').val(7.4);	$('#longueur_ferme').val(10.5) }
		
		var width 	= $('#qty').val();
		var length 	= $('#largeur').val();
		var qty		= $('#longueur').val();
		var eq_a5	= (width*length*qty/15540000).toFixed(2);
		
		if(width != 0 && length != 0 && qty != 0)
			$('#eq_t50_a5').val(eq_a5);
	}	
</script>
<script>
//add custom method for dataTables
$.fn.dataTable.Api.register( 'column().data().sum()', function () {
    return this.reduce( function (a, b) {
        var x = parseFloat( a ) || 0;
        var y = parseFloat( b ) || 0;
        return x + y;
    },0);
} );

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
			customize: function (win) {	
				if(groupValid) valid = 'VALIDEE';
				else valid = 'NON VALIDEE';
				$(win.document.body).find("h1").after("<b><h2><?php echo $amalgame_name; ?> "+valid+"</b></h2>");
				container =	$(win.document.body).find("table");
				addTotalFooter(container);
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
			customizeData: function ( data ) {
				
				//sum specific column
				table = $('#datatable').DataTable();
				lastcolumn = (data.body.length);
				var totalQuantite = table.column( 12, {page:'current'} ).data().sum().toFixed(2);
				var totalEquivalen = table.column( 15, {page:'current'} ).data().sum().toFixed(2);								
				
				//manually add total at the end of array
				console.log(lastcolumn);
				data.body[lastcolumn] = [];
				data.body[lastcolumn][0] = '';
				data.body[lastcolumn][1] = 'Total';
				data.body[lastcolumn][2] = '';
				data.body[lastcolumn][3] = '';
				data.body[lastcolumn][4] = '';
				data.body[lastcolumn][5] = '';
				data.body[lastcolumn][6] = '';
				data.body[lastcolumn][7] = '';
				data.body[lastcolumn][8] = '';
				data.body[lastcolumn][9] = '';
				data.body[lastcolumn][10] = '';
				data.body[lastcolumn][11] = '';
				data.body[lastcolumn][12] = totalQuantite;
				data.body[lastcolumn][13] = '';
				data.body[lastcolumn][14] = '';
				data.body[lastcolumn][15] = totalEquivalen;
				data.body[lastcolumn][16] = '';
				data.body[lastcolumn][17] = '';
				data.body[lastcolumn][18] = '';

				console.log(data);
			},			
			customize: function(xlsx) {
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
						'<c t="inlineStr" r="B1" s="22"><is><t>Liste des Amalgame</t></is></c>'+
					'</row>'+
					'<row r="2">'+
						'<c t="inlineStr" r="A2"><is><t/></is></c>'+
						'<c t="inlineStr" r="B2" s="23"><is><t><?php echo $amalgame_name; ?></t></is></c>'+
					'</row>'
				);
			},
		});

		//extending pdf button
		table.button(3).remove();	//we will customize pdf button so remove it first then readding again
		table.button().add( 3, {	//re adding the pdf button
			extend: 'pdfHtml5',
		   	text: 'PDF',
		   	exportOptions: {
		      	modifier: {		        
		         	columns: ':visible',
		      	},
		   	},   
		   	orientation: 'landscape',
		   	pageSize: 'LEGAL',
		   	customize: function(doc) {
		   	  //sum specific column
			  datatable = $('#datatable').DataTable();				
			  var totalQuantite = datatable.column( 12, {page:'current'} ).data().sum().toFixed(2);
		      var totalEquivalen = datatable.column( 15, {page:'current'} ).data().sum().toFixed(2);	
		      if(groupValid == "1") valid = 'VALIDEE';
				else valid = 'NON VALIDEE';
			  var secondHeader = "<?php echo $amalgame_name;?> " + valid; 
			  
		   	  var table = doc.content[1].table;
		   	  var body = table.body;
		   	  var footer = [];

		   	  for(var i=0; i < 19; i++) {		   	  	
		   	  	if(i == 0) {
		   	  		footer.push({
			   	  		text: "Total",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 12
			   	  	});
		   	  	}else if(i == 12) {
		   	  		footer.push({
			   	  		text: totalQuantite,
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 3
			   	  	});
		   	  	} else if(i == 15) {
		   	  		footer.push({
			   	  		text: totalEquivalen,
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  		colSpan: 4
			   	  	});
		   	  	} else {
		   	  		footer.push({
			   	  		text: "",
			   	  		style: "tableFooter",
			   	  		width: "*",
			   	  	});
		   	  	}		   	 
		   	  }


		   	  body.push(footer);
		   	  table.body = body;		   
		   	  		  
		   	  var contentTable = doc.content[1];		   	 
		   	  contentTable.table = table;				   	    	 		  	
		  	  var contentSecondHeader = {text: secondHeader, style: 'title'};
		  	  
		   	  doc.content[1] = contentSecondHeader;
		   	  doc.content[2] = contentTable;

		   	  console.log(doc)

		      doc.defaultStyle.fontSize = 6; //<-- set fontsize to 16 instead of 10 
		      doc.styles.tableHeader.fontSize = 6;
		      doc.styles.tableFooter.fontSize = 6;
		      doc.styles.title.alignment = "left";
		      doc.styles.title.marginBottom = 15;
		   	}  	
		} );

	}, 1000);
})

function addTotalFooter(container){
	var table = $('#datatable').DataTable();
	var totalRows = table.rows().indexes();

	if(totalRows.length > 0 ) {							
		var totalQuantite = table.column( 12, {page:'current'} ).data().sum().toFixed(2);
		var totalEquivalen = table.column( 15, {page:'current'} ).data().sum().toFixed(2);				
						
		container.append(
			'<tfoot id="footer-datatable">'+
				'<tr>'
					+'<th colspan=12>Total</th>'
					+'<th>'+totalQuantite+'</th>'
					+'<th colspan=2></th>'
					+'<th>'+totalEquivalen+'</th>'
					+'<th colspan=3></th>'
				+'</tr>'						
			+'</tfoot>'
		);
		container.after(
			'<style>'+
				'tfoot th{ padding: 0px !important}'+
			'</style>'
		)		
	}	
}
</script>
<?php 
$this->load->view('templates/remove_confirmation_js.php');
$this->load->view('templates/modal_upload_files-js.php', array('url_upload' => $url_upload));
?>