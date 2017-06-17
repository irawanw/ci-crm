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
		})
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

        $('#datatable tbody').on('click', 'tr', function(e) {
            var table = $('#datatable').DataTable();
            var data = table.row(this).data();

            $('#btn-remove').prop("href", "<?php echo site_url('amalgame/remove');?>/" + data.amalgame_id);

            //customize btn action une address
            var urlDetail = $('#amalgame_detail a').attr('data-cible');
            $('#controle_recurrents_detail a').prop("href", urlDetail + "/" + data.amalgame_id);
            var urlUpdate = $('#amalgame_modification a').attr('data-cible');
            $('#amalgame_modification a').prop("href", urlUpdate + "/" + data.amalgame_id);
            var urlRemove = $('#amalgame_supprimer a').attr('data-cible');
            $('#amalgame_supprimer a').prop("href", urlRemove + "/" + data.amalgame_id);

            $('.btn-single-action li').removeClass('disabled');
        });		   
			

        $('#amalgame_supprimer').click(function(e) {
            e.preventDefault();
           	$('#modal-form-remove').modal('show');	           
        });
        /** eof action remove data */
		
	
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
	
</script>

<?php 
$this->load->view('templates/remove_confirmation_js.php');
$this->load->view('templates/modal_upload_files-js.php', array('url_upload' => $url_upload));
?>