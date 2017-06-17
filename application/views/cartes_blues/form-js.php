<?php
if(isset($valeurs)):
if($valeurs->societe)
{
	$societe = explode(" ", $valeurs->societe);
	$multiple_select_html = '<option value="">(choisissez)</option>';

	foreach($liste_societe as $row)
	{
		if(in_array($row->value, $societe))
		{
			$multiple_select_html .= '<option value="'.$row->id.'" selected>'.$row->value.'</option>';
		}
		else
		{
			$multiple_select_html .= '<option value="'.$row->id.'">'.$row->value.'</option>';
		}
	}
}
endif;
?>



<?php if(isset($valeurs)): ?>
<?php if($valeurs->societe): ?>
<script>
		$(document).ready(function(e) {
				$('#societe').html('<?php echo $multiple_select_html;?>');
		});
</script>
<?php endif; ?>
<?php endif; ?>
