<script>
    $(document).ready(function(){
    	$('#template-modal-detail').on('focus',"#souscription_date,#resiliation_date, #engagement_jusquau", function(){
    		$(this).datetimepicker({
	    		format:'d/m/Y',
		    	formatDate:'d/m/Y',
		    	defaultDate:'+01/01/1970',
		    	timepicker:false
	    	});
    	});

    	$('#template-modal-detail').on('change', 'input[name=pas_engage]', function(e) {
    		var checked = $(this).prop('checked');

    		if(checked) {
    			$('#engagement_jusquau').val("");
    			$('#engagement_jusquau').attr('readonly', true);
    		} else {    			
    			$('#engagement_jusquau').attr('readonly', false);
    		}
    	});       
    });
</script>