<script type="text/javascript">
    
    $(document).ready(function() {
		
            $('#filter_commercial').multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                numberDisplayed: 1,
				maxHeight: 300,
                enableCaseInsensitiveFiltering: true,
            });

            $('#filter_commercial').multiselect('rebuild');    
    
        $('#filter_commercial').change(function () {
	        var ids = $(this).val();

	        filter_val_cache = $(this).val(); // Cache value before filter change.
	        clearTimeout(typingTimer);
	        doneTyping();
	    });

	    $('#filter_rangedate').change(function() {	    	
	        doneTyping();
	    });

	    $('#filter_type_ctc_date_creation').change(function() {	
	    	$("#filter_rangedate").val($("#filter_rangedate option:first").val());
	    	var filterDate = $(this).periodpicker('valueStringStrong');
	    	$(this).val("btw");
	    	$('#filter_input_ctc_date_creation').val(filterDate);

	        doneTyping();
	    });

	    $('#filter_type_ctc_date_creation').periodpicker({
	        draggable: false,
	        resizeButton: false,
	        cells: [1, 3],	        
	        yearsLine: true,
	        title: false,
	        closeButton: true,
	        clearButton: true,
	        fullsizeButton: false,
	        format:'DD/MM/YYYY',
	        formatDate:'DD/MM/YYYY',
	        formatDecoreDate: 'MM/YYYY',
	        formatDecoreDateWithYear: 'DD/MM/YYYY'
	    });

    });


    
</script>