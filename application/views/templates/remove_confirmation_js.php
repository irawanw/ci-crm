<script>
//mass action script
$(document).ready(function(){
	//mass action
	$("#btn_action_all").click(function(e) {
		var action = $("#sel_action_all" ).val();
		if(action == 'remove')
			$('#modal-mass-remove').modal('show');
		//else
		//	$('#modal-mass-re-integrer').modal('show');
		else
			massAction();
	})
	
	//check/uncheck all boxes
	$("#datatable thead").on('click', '#check-all', function(e) {
		$("tbody input:checkbox").not(this).prop('checked', this.checked);
	});
	
	//custom header checkbox
	setTimeout(function() {
		customHeaderCheckbox();
	}, 500);	
})

function massAction(){
	var action = $("#sel_action_all" ).val();
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
	$('#modal-mass-remove').modal('hide');
	$('#modal-mass-re-integrer').modal('hide');
}

/**
 * Generate input type checkbox in header column checkbox ids
 * @return {[type]} [description]
 */
function customHeaderCheckbox() {
	var table = $('#datatable').DataTable();
	var headerCheckbox = table.column(0).header();
	$(headerCheckbox).html("<input type='checkbox' id='check-all' />");

}
</script>