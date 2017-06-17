<script type="text/javascript">
$(document).ready(function()
{
	getFiltre();
    $('.list-group.checked-list-box .list-group-item').each(function ()
	{
		var $widget = $(this),
		$checkbox = $('<input type="checkbox" class="hidden" checked/>'),
		color = ($widget.data('color') ? $widget.data('color') : "primary"),
		style = ($widget.data('style') == "button" ? "btn-" : "list-group-item-"),
		settings = {
			on: {icon: 'glyphicon glyphicon-check'},
			off: {icon: 'glyphicon glyphicon-unchecked'}
		};
		$widget.css('cursor', 'pointer')
		$widget.append($checkbox);
		
		$widget.on('click', function () 
		{
			$checkbox.prop('checked', !$checkbox.is(':checked'));
			$checkbox.triggerHandler('change');
			updateDisplay();
        });
        $checkbox.on('change', function () {updateDisplay();});

        function updateDisplay() 
		{
            var isChecked = $checkbox.is(':checked');
            $widget.data('state', (isChecked) ? "on" : "off");
            $widget.find('.state-icon')
                .removeClass()
                .addClass('state-icon ' + settings[$widget.data('state')].icon);
            if (isChecked) {
                $widget.addClass(style + color + ' active');
            } else {
                $widget.removeClass(style + color + ' active');
            }
        }
        function init() 
		{
            if ($widget.data('checked') == true) {
                $checkbox.prop('checked', !$checkbox.is(':checked'));
            }
            updateDisplay();
            if ($widget.find('.state-icon').length == 0) {
                $widget.prepend('<span class="state-icon ' + settings[$widget.data('state')].icon + '"></span>');
            }
        }
        init();
    });
	$('#check-list-box').on('click',"li",function(event) 
	{
		event.preventDefault(); 
		var checkedItems = {}, counter = 0;
		var arr = [];
		$("#check-list-box li.active").each(function(idx, li) {
			arr[counter] = "'"+$(li).data('id')+"'";
			counter++;
		});
		//console.log(arr.join());
		//$('#commercial').val(JSON.stringify(checkedItems));
		$('#commercial').val(arr.join());
		getFiltre();
	});

	$("input:radio").on("click", function() 
	{
		var id = $(this).val();
		if(id == 1)
		{
			$("#periods_manual").attr("disabled", true);
			$("#periods").attr("disabled", false);
		}
		else
		{
			$("#periods").attr("disabled", true);
			$("#periods_manual").attr("disabled", false);
		}
	});
	
	$("#periods_manual").daterangepicker({
		autoUpdateInput: false,
		locale: {
			cancelLabel: 'Clear'
		}
	});

	$("#periods_manual").on('apply.daterangepicker', function(ev, picker) {
		$(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
		$('#periods').val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
		getFiltre()
	});

	$("#periods_manual").on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');
	});
});

$(document).on('change',"#periods_select",function(){
	$('#periods').val($(this).val());
	getFiltre()
});

function getFiltre()
{
	$('.mloader').modal('show');
	var commercial = $('#commercial').val();
	if(!commercial) commercial = 0;
	var periods = $('#periods').val();
	if(!periods) periods = "all";
	console.log(commercial);
	console.log(periods);
	$.ajax({
		url:'demande_devis_commerciaux/getFilter',
		type:'post',
		data:'commercial='+commercial+'&periods='+periods,
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
	getFiltre(val.val());
	$('#generale').val('0');
	$('#Oadwords').val('0');
	$('#Oflyers').val('0');
	$('#Oprospection').val('0');
	$('#Oother').val('0');
});
</script>