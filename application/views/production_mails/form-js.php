<?php
if(isset($valeurs)):
if($valeurs->blacklist)
{
	$blacklists = explode(" ", $valeurs->blacklist);
	$multiple_select_html = '<option value="">(choisissez)</option>';

	foreach($liste_blacklist as $row)
	{
		if(in_array($row->value, $blacklists))
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


<script>
	$(document).ready(function(){
		readOnlyAbuseInput();

		$('#blacklist').attr('multiple', 'multiple');
		$('#blacklist').attr('name', 'blacklist[]');

		$('#mail').focusout(function(){
			domain = $(this).val().replace(/.*@/, "");
			$('#domain').val(domain);
		})

		$("#provider").change(function(){
			var providerId = $(this).val();

			if(providerId !== '') {
	            $.getJSON("<?php echo site_url('production_mails'); ?>/get_provider_detail/"+ providerId, function(data){

	            		$('#abuse_email').val(data.abuse_email);	            		            	
	            		$('#abuse_telephone').val(data.abuse_telephone);	        
	            		$('#abuse_url').val(data.abuse_url);
	            });
        	} else {
        		$('#abuse_email').val("");	            		            	
	            $('#abuse_telephone').val("");	        
	            $('#abuse_url').val("");
        	}
        })
	});

	function readOnlyAbuseInput() {
		$('#abuse_email').attr('readonly', true);
		$('#abuse_telephone').attr('readonly', true);
		$('#abuse_url').attr('readonly', true);
	}

	function generateDropdown(valuers, callback) {
		if(valuers) {
			var options = '<option value="" selected>(choisissez)</option>';
			for(var i = 0; i < valuers.length; i++) {
				options += '<option value="'+valuers[i]+'">' + valuers[i] + '</option>';
			}
		} else {
			var options = false;
		}

		callback(options);
	}
</script>

<?php if(isset($valeurs)): ?>
<?php if($valeurs->blacklist): ?>
<script>
		$(document).ready(function(e) {
				$('#blacklist').html('<?php echo $multiple_select_html;?>');
		});
</script>
<?php endif; ?>
<?php endif; ?>
