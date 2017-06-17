<?php
if(isset($data))	
{	
foreach($data as $module => $value){
?>
<div class="table-<?php echo $module; ?>">
<h4 style="cursor: pointer" onclick="javascript:$(this).next().toggle()"><span>+</span> <?php echo $module; ?></h4>
<table width="500px" style="display: none">
	<tr>
		<th style="text-align: center; width: 70px">All</th>
		<th style="text-align: center; width: 70px">Read</th>
		<th style="text-align: center; width: 70px">Write</th>		
		<th>Field</th>
	</tr>
	<?php
		//echo "<pre>";
		//print_r($fields);
		//echo "</pre>";
		
		foreach($value['fields'] as $row){
			
			$read_disabled = '';
			$read_checked = '';
			$write_disabled = '';
			$write_checked = '';
			$all_disabled = '';
			$all_checked = '';
			
			$value['read'] = isset($value['read']) ? $value['read']: array();
			$value['write'] = isset($value['write']) ? $value['write']: array();
			
			if(!in_array($row, $value['read'])){
				$read_disabled = 'disabled';
				$all_disabled = 'disabled';	
			}
			if(!in_array($row, $value['write'])){
				$write_disabled = 'disabled';
				$all_disabled = 'disabled';	
			}
			
			if(in_array($row, $value['user_read'])){
				$read_checked = 'checked';
			}
			if(in_array($row, $value['user_write'])){
				$write_checked = 'checked';
			}
			if(in_array($row, $value['user_read']) &&
				in_array($row, $value['user_write'])){
				$all_checked = 'checked';
			}
			
			
	?>
	<tr>
		<td class="checkbox-setting" align="center">
			<input value="<?php echo $row; ?>" 
					id="<?php echo $module.'_all_'.$row; ?>" 	
						<?php echo $all_disabled; ?> 
						<?php echo $all_checked; ?> 
					type="checkbox">
		</td>
		
		<td class="checkbox-setting"  align="center">
			<input value="<?php echo $row; ?>" 
					id="<?php echo $module.'_read_'.$row; ?>" 	
						<?php echo $read_disabled; ?> 
						<?php echo $read_checked; ?> 
					type="checkbox">
		</td>
		
		<td class="checkbox-setting" align="center">
			<input value="<?php echo $row; ?>" 
					id="<?php echo $module.'_write_'.$row; ?>" 	
						<?php echo $write_disabled; ?> 
						<?php echo $write_checked; ?> 
					type="checkbox">
		</td>
		<td class="checkbox-setting"><?php echo $row; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="3">
			<br>
			<input type="button" id="update" 
				onclick="update_permissions('<?php echo $module; ?>')" value="Save Permissions">
		</td>
	</tr>
</table>
</div>
<?php } ?>

<style>
	.checkbox-setting{
		height: 28px;
		border-bottom: 1px solid #ccc;
	}
</style>
<?php } ?>
<script>
	function get_fields(module, type){
		fields = new Array();
		$('input[id^=' + module + '_' + type + ']').each(function(){			
			if($(this).is(':checked')){ 
				fields.push($(this).val());
			}
		})	
		return fields.join();
	}
	
	function update_permissions(module){
		$('#table-settings').css('opacity', '0.2');
		$.post('<?php echo site_url(); ?>/users_permissions/update_permissions/<?php echo $usp_utilisateurs; ?>', { 
			"module" : module,
			"fields_read" : get_fields(module, 'read'),
			"fields_write" : get_fields(module, 'write'),
		}, function(data){
			$('#table-settings').css('opacity', '1');
		})
	}
	
	function new_permissions(module){
		$('#table-settings').html('Adding new permission. Please wait...');
		$.post('<?php echo site_url(); ?>/users_permissions/update_permissions/<?php echo $usp_utilisateurs; ?>', { 
			"module" : module,
			"fields_read" : '',
			"fields_write" : '',
		}, function(data){			
			$.ajax({
				type: 'GET',
				url: '<?php echo site_url(); ?>/users_permissions/get_permissions/<?php echo $usp_utilisateurs; ?>',
				success: function(result) {
					$('#table-settings').html(result);				
				},
			});
		})
	}	
	
	$(function(){
	//check all toggle
		$('input[id*=_all_]').click(function(){
			$(this).parent().next().find('input').prop('checked', $(this).is(':checked'));
			$(this).parent().next().next().find('input').prop('checked', $(this).is(':checked'));
		})	
	})
</script>
