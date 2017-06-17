<script type="text/javascript" src="<?php echo base_url();?>/assets/js/jquery.periodpicker.full.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
	getData();

	$('#date').periodpicker({
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

    $('#date').change(function() {	    	
    	getData();
    });

    $(".period_picker_clear").removeAttr("style");   
});

function getData()
{
	$('.mloader').modal('show');	
	var rangeDate = $('#date').periodpicker('valueStringStrong');

	if(typeof rangeDate === "object") {
		rangeDate = "";
	} 
		
	$.ajax({
		url:'demande_devis_quick_followup/get_data',
		type:'post',
		data: 'rangeDate=' + rangeDate,	
		success:function(response)
		{
			$('#filtre').html(response);			
			$('.mloader').modal('hide');			
		}
	});	
} 
</script>