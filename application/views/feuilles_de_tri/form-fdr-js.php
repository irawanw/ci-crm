<script src="<?php echo base_url('assets/js/bootstrap-datepicker.min.js');?>"></script>

<script type="text/javascript">
	$(document).ready(function() {
		 $(".date").datepicker({
		 	format: 'yyyy-mm-dd',
		 });

		 $('.person-type').change(function(e) {
		 	var type = $(this).val();
		 	var id = $(this).attr('data-id');

		 	$('#person-' + id).attr('readonly', true);

		 	$.get("<?php echo site_url('feuilles_de_tri/get_persons');?>/" + type, function(response) {
		 		var data = response.data;
		 		$("#person-" + id).html("");
		 		$('#person-' + id).append("<option value='0' selected>(Choose)</option>");
		 		if(data) {
		 			for(var i=0; i < data.length; i ++) {
		 				var option = '<option value="'+ data[i].id +'">'+ data[i].name +'</option>';
		 				$("#person-" + id).append(option);
		 			}
		 		}
		 		$('#person-' + id).attr('readonly', false);
		 	},"json");
		 });
	});
</script>