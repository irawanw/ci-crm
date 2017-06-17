<script type="text/javascript">
	var SITE_URL = "<?php echo site_url();?>";
	var groupName = "<?php echo $this->uri->segment(4); ?>";
	var groupValid = "<?php echo isset($group_valid) ? $group_valid : null;?>";

	function generateControlePermanentName() {
		
		var formControlePermanentID = "#modal-create-controle-permanent";
		
		var clientName = $(formControlePermanentID + ' #client').val() != "" ? $(formControlePermanentID + ' #client option:selected').text() : "";
		var commandeName = $(formControlePermanentID + ' #commande').val() != "" ? $(formControlePermanentID + ' #commande option:selected').text() : "";
		var date = $(formControlePermanentID + ' #name').attr('data-date');

		var groupName = clientName.replace(/\s+/g, '-') + '_' + commandeName.replace(/\s+/g, '-') + '_' + date;
		$(formControlePermanentID + ' #name').val(groupName);		
	}

	function generateControlePonctuelName(){
		var formControlePonctuelID = "#modal-create-controle-ponctuels";
		var permanentName = $('#controle_permanent option:selected').text();
		$(formControlePonctuelID + ' #name').val(permanentName);		
	};

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

	$(document).ready(function(e) {

		//mass action group
        $("#btn_action_all_group").click(function(e) {
            var action = $("#sel_action_all_group" ).val();
            if(action == 'remove_group')
                $('#modal-form-remove-group').modal('show');
            else
                massActionGroup();
        });

		<?php 
            $group_mode = $this->uri->segment(2) == "group" ? true : false;
            if($group_mode == false){ ?>
            $('#valider_controle_permanent').addClass('disabled');
            $('#devalider_controle_permanent').addClass('disabled');
            $('#valider_controle_ponctuels').addClass('disabled');
            $('#devalider_controle_ponctuels').addClass('disabled');
        <?php } ?> 
        
        $('#date_controle_ponctuel').datetimepicker({
    		format:'d/m/Y',
	    	formatDate:'d/m/Y',
	    	defaultDate:'+01/01/1970',
	    	timepicker:false
    	}); 

		//open modal form create a new controle permanent
		$('#create_controle_permanent a').click(function(e) {
			e.preventDefault();			
			$('#modal-create-controle-permanent').modal('show');
		});

		//open modal form create a new controle ponctuel
		$('#create_controle_ponctuels a').click(function(e) {
			e.preventDefault();			
			$('#modal-create-controle-ponctuels').modal('show');
		});


		//pull commande data when client select is changed
		$("#modal-create-controle-permanent #client").change(function(){
			$("#modal-create-controle-permanent #commande").attr('readonly', true);
			$('#modal-create-controle-permanent #name').val('');
			$.get(SITE_URL + "/controle_recurrents/commande_option/"+$(this).val(), function(data){
				$("#modal-create-controle-permanent #commande").html(data);
				$("#modal-create-controle-permanent #commande").attr('readonly', false);
			});
		});		

		//generate new controle permanent name
		$('#modal-create-controle-permanent select').change(function(e) {
			generateControlePermanentName();
		});

		//pull commande data when client select is changed
		$("#modal-create-controle-ponctuels #client").change(function(){
			$("#modal-create-controle-ponctuels #commande").attr('readonly', true);
			$('#modal-create-controle-ponctuels #name').val('');
			$.get(SITE_URL + "/controle_recurrents/get_permanent/"+$(this).val(), function(data){
				$("#controle_permanent").html(data);
				$("#controle_permanent").attr('readonly', false);
			});
		});

		//generate new controle ponctuels name
		$('#modal-create-controle-ponctuels #controle_permanent').change(function(e) {
			generateControlePonctuelName();
		});

		//open modal form valider and devalider control
		$('#valider_controle_permanent a').click(function(e) {
			e.preventDefault();
			$('#modal-form-valider-controle').modal('show');
		});

		$('#devalider_controle_permanent a').click(function(e) {
			e.preventDefault();
			$('#modal-form-devalider-controle').modal('show');
		});

		$('#valider_controle_ponctuels a').click(function(e) {
			e.preventDefault();
			$('#modal-form-valider-controle').modal('show');
		});

		$('#devalider_controle_ponctuels a').click(function(e) {
			e.preventDefault();
			$('#modal-form-devalider-controle').modal('show');
		});
		//eof open modal form valider and devalider control
		
		//liste de controle
		$('#liste_controle').click(function(e) {
			e.preventDefault();
			window.location = SITE_URL + "/controle_recurrents";
		});
		$('#liste_controle_permanent').click(function(e) {
			e.preventDefault();
			window.location = SITE_URL + "/controle_recurrents/permanent";
		});
		$('#liste_controle_ponctuels').click(function(e) {
			e.preventDefault();
			window.location = SITE_URL + "/controle_recurrents/ponctuel";
		});
		//eof liste de controle
		
		//change list view
		$('#sel_view').change(function(){
			window.location = ($('#sel_view option:selected').val());
		});

		//view all button to list all result
        $('#controle_recurrents_voir_liste a').click(function(e) {
            e.preventDefault();
            console.log("show all");
            var isList = $(this).attr('data-list');
            var table = $('#datatable');
            var setting = table.DataTable().init();
            var textHtml = $(this).html();

            if(isList) {
                setting.iDisplayLength = 10;
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
	});
</script>
<?php $this->load->view('templates/remove_confirmation_js.php'); ?>