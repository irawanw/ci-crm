<script type="text/javascript">
$(document).ready(function()
{
	getFiltre();
});

$(document).on('change','#commercial,#periods,#generale,#enseignes',function()
{
	$('#Oemailing').val('0');
	$('#Oadwords').val('0');
	$('#Oflyers').val('0');
	$('#Oprospection').val('0');
	$('#Oother').val('0');
	getFiltre();	
});

function getFiltre(name=0)
{
	$('.mloader').modal('show');
	var commercial = $('#commercial').val();
	if(!commercial) commercial = 0;  
	var periods = $('#periods').val();
	if(!periods) periods = 'all';
	var generale = $('#generale').val();
	if(!generale) generale = 0;
	var enseignes = $('#enseignes').val();
	if(!enseignes) enseignes = 0;
	
	var origine = name; //get from selected origine dropdown
	if(!origine) origine = 0;
	$.ajax({
		url:'statistiques_prospection/getFilter',
		type:'post',
		data:'commercial='+commercial+'&periods='+periods+'&generale='+generale+'&origine='+origine+'&enseignes='+enseignes,
		success:function(response)
		{
			$('#filtre').html(response);			
			$('.mloader').modal('hide');
		}
	});	
} 

$('#Oemailing').on('change',function()
{
	var val = $(this);
	/*
	var raw = $('#Oemailing option:selected').text().split(" ");
	var generale = raw[0];
	if(generale == 'Tous')
		$('#generale').val('0');
	else
	$('#generale').val(generale);
	*/
	getFiltre(val.val());
	$('#generale').val('0');
	$('#Oadwords').val('0');
	$('#Oflyers').val('0');
	$('#Oprospection').val('0');
	$('#Oother').val('0');
});
$('#Oadwords').on('change',function()
{
	var val = $(this);
	getFiltre(val.val());
	$('#generale').val('0');
	$('#Oemailing').val('0');
	$('#Oflyers').val('0');
	$('#Oprospection').val('0');
	$('#Oother').val('0');
});
$('#Oflyers').on('change',function()
{
	var val = $(this);
	getFiltre(val.val());
	$('#generale').val('0');
	$('#Oemailing').val('0');
	$('#Oadwords').val('0');
	$('#Oprospection').val('0');
	$('#Oother').val('0');
});
$('#Oprospection').on('change',function()
{
	var val = $(this);
	getFiltre(val.val());
	$('#generale').val('0');
	$('#Oemailing').val('0');
	$('#Oadwords').val('0');
	$('#Oflyers').val('0');
	$('#Oother').val('0');
});
$('#Oother').on('change',function()
{
	var val = $(this);
	getFiltre(val.val());
	$('#generale').val('0');
	$('#Oemailing').val('0');
	$('#Oadwords').val('0');
	$('#Oflyers').val('0');
	$('#Oprospection').val('0');
});
</script>