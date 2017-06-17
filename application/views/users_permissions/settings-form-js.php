<script>
	$(document).ready(function(){
		
		//add new permission to certain module
		$('#permission-add').click(function()
		{
			if($('#usp_table').val() == 0){
				alert('Please select menu first')
			}
			else {
				new_permissions($('#usp_table').val());
			}
		})
		
		//select utilisateurs
		$('#usp_utilisateurs').change(function()
		{			
			$('#table-settings').html('');
			$.ajax({
				type: 'GET',
				url: '<?php echo site_url(); ?>/users_permissions/get_permissions/'+$(this).val(),
				success: function(result) {
					$('#table-settings').html(result);				
				},
			});
		})		
	})
</script>