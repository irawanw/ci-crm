<script>
    var SITE_URL = "<?php echo site_url($controleur);?>";
	var groupName = "<?php echo $this->uri->segment(3); ?>";
	var groupValid = "<?php echo isset($group_valid) ? $group_valid : null;?>";

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
    
    $(document).ready(function() {

         $('#controle_distribution_heure_de_debut,#controle_distribution_heure_de_fin').datetimepicker({
            format:'H:i:s',
            formatDate:'H:i:s',     
            datepicker:false
        }); 

        $('#controle_distribution_date').datetimepicker({
            format:'d/m/Y',
            formatDate:'d/m/Y',
            defaultDate:'+01/01/1970',
            timepicker:false
        }); 

        //mass action group
        $("#btn_action_all_group").click(function(e) {
            var action = $("#sel_action_all_group" ).val();
            if(action == 'remove_group')
                $('#modal-form-remove-group').modal('show');
            else
                massActionGroup();
        });

        <?php 
            $group_valid = isset($group_valid) ? $group_valid : 'undefined';
            if($group_valid){ ?>
            $('#valider_controle_distribution').addClass('disabled');
        <?php } 
            if($group_valid == 0){ ?>
            $('#devalider_controle_distribution').addClass('disabled');
        <?php } ?>

        $('#liste_controle_distribution a').click(function(e) {
            e.preventDefault();
            window.location = "<?php echo site_url('feuille_controle');?>";
        });
		//view all button to list all result
        $('#feuille_controle_voir_liste a').click(function(e) {
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
		
        $('#sel_view').change(function(e) {
            var view = $(this).val();
            window.location = view;
        });
   
        /** action one data */
        $('#datatable tbody').on('click', 'tr', function(e) {
            var table = $('#datatable').DataTable();
            var data = table.row(this).data();

            $('#btn-remove').prop("href", "<?php echo site_url('feuille_controle/remove');?>/" + data.feuille_controle_id);

            //customize btn action une address
            var urlDetail = $('#feuille_controle_detail a').attr('data-cible');
            $('#feuille_controle_detail a').prop("href", urlDetail + "/" + data.feuille_controle_id);
            var urlUpdate = $('#feuille_controle_modification a').attr('data-cible');
            $('#feuille_controle_modification a').prop("href", urlUpdate + "/" + data.feuille_controle_id);
            var urlRemove = $('#feuille_controle_supprimer a').attr('data-cible');

            $('#feuille_controle_supprimer a').prop("href", urlRemove + "/" + data.feuille_controle_id);

            $('.btn-single-action li').removeClass('disabled');
        });

        $('#feuille_controle_supprimer').click(function(e) {
            e.preventDefault();
            $('#modal-form-remove').modal('show');
        });        

        //popup modal form create a new controle distribution
        $("#create_controle_distribution").click(function(e){
            e.preventDefault();
            $("#modal-form-controle-distribution").modal('show');
            get_controleur_option();
            get_client_option();
            get_resultat_option();
        });

        //popup modal form valider controle distribution
        $('#valider_controle_distribution').click(function(e) {
            e.preventDefault();        
            $('#modal-form-valider-controle-distribution').modal('show');
        });

        //popup modal form devalider controle distribution
        $('#devalider_controle_distribution').click(function(e) {
            e.preventDefault();           
            $('#modal-form-devalider-controle-distribution').modal('show');
        });


        //pull devis data when client select is changed
        $("#controle_distribution_client").change(function(){
            $('#controle_distribution_devis').attr('readonly', true);
            $.get("<?php echo site_url('feuille_controle/devis_option');?>/"+$("#controle_distribution_client").val(), function(data){
                $("#controle_distribution_devis").html(data);
                $('#controle_distribution_devis').attr('readonly', false);
            });         
        });

        //pull factures data when devis select is changed
        $("#controle_distribution_devis").change(function(){
            $('#controle_distribution_facture').attr('readonly', true);
            $.get("<?php echo site_url('feuille_controle/factures_option');?>/"+$("#controle_distribution_devis").val(), function(data){
                $("#controle_distribution_facture").html(data);
                $('#controle_distribution_facture').attr('readonly', false);
            });         
        });

        //set controle distribution name when input on modal form changed
        $('#modal-form-controle-distribution input, #modal-form-controle-distribution select').change(function(e) {
            setControleDistributionName();
        });
        $('#controle_distribution_commentaire').keyup(function(e) {
            setControleDistributionName();
        });

        //get list controle distribution & controle ponctuels related with client selected
        $("#client_view").change(function(){
            $.get(SITE_URL + "/get_group/"+$("#client_view").val()+"/valides", function(data){
                $("#controle_distribution_view").html(data);
            });
            $.get(SITE_URL + "/get_group/"+$("#client_view").val()+"/nonvalides", function(data){
                $("#controle_distribution_nonvalides_view").html(data);
            });
        });

        //action redirect to controle distribution or controle ponctuels list data
        $("#controle_distribution_view").change(function(){
            var url  = $(this).val() ? $(this).val() : SITE_URL;
            window.location = (url);
        });
        $("#controle_distribution_nonvalides_view").change(function(){
            var url  = $(this).val() ? $(this).val() : SITE_URL;
            window.location = (url);
        });

        //disabled button valider and devalider if controle distribution not selected
        disabledButtonValiderDevalider();
    });

    function get_client_option() {
        $.get("<?php echo site_url('feuille_controle/client_option');?>", function(data) {
            $('#controle_distribution_client').html(data);
        });
    }

    function get_controleur_option() {
        $.get("<?php echo site_url('feuille_controle/controleur_option');?>", function(data) {
            $('#controle_distribution_controleur').html(data);
        });
    }

    function get_resultat_option() {
        $.get("<?php echo site_url('feuille_controle/resultat_option');?>", function(data) {
            $('#controle_distribution_resultat').html(data);
        });
    }

    function setControleDistributionName() {
        var dateString = $('#controle_distribution_date').val();
        var dateStringArr = dateString.split('/');
        date = dateStringArr[0] + "-" + dateStringArr[1] + "-" + dateStringArr[2];

        var heureDeDebut = $('#controle_distribution_heure_de_debut').val() != "" ? $('#controle_distribution_heure_de_debut').val() : "";
        heureDeDebut = heureDeDebut.replace(" ", "-");
        var heureDeFin = $('#controle_distribution_heure_de_fin').val() != "" ? $('#controle_distribution_heure_de_fin').val() : "";
        heureDeFin = heureDeFin.replace(" ", "-");
        var controleur = $('#controle_distribution_controleur option:selected').val() != "" ? $('#controle_distribution_controleur option:selected').text() : "";
        controleur = controleur.replace(" ", "-");        
        var client = $('#controle_distribution_client option:selected').val() != 
        "" ? $('#controle_distribution_client option:selected').text() : "";
        client = client.replace(/ /g , "-");
        var devis = $('#controle_distribution_devis option:selected').val() != "" ? $('#controle_distribution_devis option:selected').text() : "";
        devis = devis.replace(/ /g , "-");
        devis = client == "" ? "" : devis;
        var facture = $('#controle_distribution_facture option:selected').val() != "" ? $('#controle_distribution_facture option:selected').text() : "";
        facture = facture.replace(/ /g , "-");
        facture = devis == "" ? "" : facture;		

		var name = "";

        if(date != "") {
            name += date;
        }

        // if(heureDeDebut != "") {
        //     name += "_" + heureDeDebut;   
        // }

        // if(heureDeFin != "") {
        //     name += "_" + heureDeFin;   
        // }

        if(controleur != "") {
            name += "_" + controleur;   
        }

        if(client != "") {
            name += "_" + client;   
        }  

        if(devis != "") {
            name += "_" + devis;   
        }   

        if(facture != "") {
            name += "_" + facture;   
        }   


        $('#controle_distribution_name').val(name);
    }

    function disabledButtonValiderDevalider() {
        
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
                }
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
						'<c t="inlineStr" r="B1" s="22"><is><t>Liste des Amalgame</t></is></c>'+
					'</row>'+
					'<row r="2">'+
						'<c t="inlineStr" r="A2"><is><t/></is></c>'+
						'<c t="inlineStr" r="B2" s="23"><is><t>'+groupName+' '+valid+'</t></is></c>'+
					'</row>'
				);
			},
		} );	

        //extending pdf button    
        table.button(3).remove();   //we will customize pdf button so remove it first then readding again
        table.button().add( 3, {    //re adding the pdf button
            extend: 'pdfHtml5',
            text: 'Save PDF',
            exportOptions: {
                modifier: {         
                    columns: ':visible',
                },
            },   
            // orientation: 'landscape',
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
<?php 
$this->load->view('templates/remove_confirmation_js.php');
?>