<style type="text/css">
.progress {
    display: block;
    text-align: center;
    width: 0;
    height: 13px;
    background: red;
    transition: width .3s;
}
.progress.hide {
    opacity: 0;
    transition: opacity 1.3s;
}
</style>

<div class="modal fade" id="creer_document_modal" tabindex="-1" role="dialog" data-id="">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="progress"></div>
				<div class="frm_document_generate">
					
				</div>
			</div>
		
		</div>
	</div>
</div>

<script type="text/javascript">

$(document).ready(function(){
	$('#contacts_document_generate').click(function(e) {
		e.preventDefault();
		var selectedIds = getSelectedIds();

		//console.log(selectedIds);

		$.ajax({
			url: "<?php echo site_url('contacts/document_generate/0/ajax');?>",
			type:'post',
			data: 'selectedIds=' + JSON.stringify(selectedIds),
			dataType:'json',
			success:function(response)
			{
				$('.frm_document_generate').html(response.data);
				$('#creer_document_modal').modal('show');

				$.each(selectedIds, function(index, value) {
				    $('#ctc_id').multiselect('select', [value]);
				});

				$('#ctc_id').multiselect('refresh');
				
				var btnCombine = '&nbsp;<button type="button" id="combine-doc-file" class="btn btn-primary">Generate Doc Combine</button>';
				var btnPrint = '&nbsp;<button type="button" id="print-doc-file" class="btn btn-default">Print Doc Files</button>';
				
				$('#form-submit-contacts-document_generate').after(btnCombine + btnPrint);
			}
		});
	});

	/**
	 * Generate Doc
	 * 
	 */
	$("#creer_document_modal").on("click","#form-submit-contacts-document_generate", function(e) {
		e.preventDefault();
	
		var form = $('#form-contacts-document_generate-1');
		tinyMCE.triggerSave();
		var url = $(form).attr("action");
        var data = $(form).serialize();
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: "json",
            success: function(response) {
            	if(response.success == true) {
            		var event = response.data.event;
            		$('#creer_document_modal').modal('hide');
            		notificationWidget.show(response.message, response.notif);
            		
            		var redirect = event.redirect;

            		if(Array.isArray(redirect)) {
            			$.each(redirect, function(index, value) {
            				window.open(value);
            			});
            		} else {
            			window.location.href = event.redirect;
            		}
            	} else {
            		notificationWidget.show(response.message, response.notif);
            	}
            }
        });
	});

	/**
	 * Generate Doc Combine
	 * 
	 */
	$("#creer_document_modal").on("click","#combine-doc-file", function(e) {
		e.preventDefault();
	
		var form = $('#form-contacts-document_generate-1');
		tinyMCE.triggerSave();
		var url = $(form).attr("action") + '/combine';
        var data = $(form).serialize();
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: "json",
            success: function(response) {
            	if(response.success == true) {
            		var event = response.data.event;
            		$('#creer_document_modal').modal('hide');
            		notificationWidget.show(response.message, response.notif);
            		
            		var redirect = event.redirect;

            		if(Array.isArray(redirect)) {
            			$.each(redirect, function(index, value) {
            				window.open(value);
            			});
            		} else {
            			window.location.href = event.redirect;
            		}
            	} else {
            		notificationWidget.show(response.message, response.notif);
            	}
            }
        });
	});

	/**
	 * Print Doc Files
	 * 
	 */
	$("#creer_document_modal").on("click","#print-doc-file", function(e) {
		e.preventDefault();

		var form = $('#form-contacts-document_generate-1');
		tinyMCE.triggerSave();
		var url = "<?php echo site_url('contacts/document_generate_html/ajax');?>";
        var data = $(form).serialize();
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: "json",
            success: function(response) {            	
            	if(response.success == true) {
            		var event = response.data.event;
            		            		
            		var redirect = event.redirect;
            		if(redirect) {
            			setTimeout(function(){
            				var urlPrint = "<?php echo site_url('contacts/print_document');?>/" + redirect;
            				window.open(urlPrint);
            			},300);            			
            		}

            		$('#creer_document_modal').modal('hide');
            	} else {
            		notificationWidget.show(response.message, response.notif);
            	}
            }
        });
	});

	$("#creer_document_modal").on("change", "#ctc_id", function(e) {
		var template_id = $('#tpl_nom').val();
		var contact_ids = $(this).val();

		$.ajax({
			url:'contacts/getTemplate',
			type:'POST',
			data: {template_id: template_id, contact_ids: contact_ids},
			success:function(response)
			{
				tinyMCE.get('content').setContent(response,{format : 'raw'});
			}
		});
	});	
});

	/**
	 * [getSelectedIds description]
	 * @return {[type]} [description]
	 */
	var getSelectedIds = function () {
		theid = {};
		$("input:checkbox:checked").each(function(i){
			var id = $(this).val();
			theid[i] = id;
		});

		if(Object.keys(theid).length === 0) {
			var selectedRowId = $('#datatable').find('tr.selected').attr('id');
			theid[0] = selectedRowId;
		}

		return theid;
	};

	$(document).on('change',"#tpl_nom",function()
	{
		var template_id = $(this).val();
		var contact_ids = [];
		
		$('#ctc_id option').each(function(i, selected){ 
		  //console.log("test " + i);
		  contact_ids[i] = $(this).val();
		});

		$.ajax({
			url:'contacts/getTemplate',
			type:'POST',
			data: {template_id: template_id, contact_ids: contact_ids},
			success:function(response)
			{
				tinyMCE.get('content').setContent(response,{format : 'raw'});
			}
		});
	});
	
	function initEditor()
	{
		tinymce.init({
			selector: '#content',
			height: 250,
			theme: 'modern',
			menubar:false,
			element_format : 'html',
			plugins: [
				'advlist lists charmap preview hr anchor pagebreak textcolor',
				'code fullscreen',
			],
			toolbar1: 'undo redo | styleselect | fontselect fontsizeselect | bold italic underline | alignleft aligncenter alignright alignjustify',
			toolbar2: 'bullist numlist outdent indent | forecolor backcolor | code',
			image_advtab: true,
		});
	}
	
	$("#creer_document_modal").on('hide.bs.modal',function()
	{
		tinymce.remove();
		$('#ctc_id').multiselect('destroy');
	});
	
$("#creer_document_modal").on('shown.bs.modal',function()
{
	initEditor();
});

$("#template-modal-detail").on('shown.bs.modal',function()
{
	$('#ctc_id').val($(this).attr('data-id'));
	$('#ctc_statistiques').val('1');
	var marche = $("input[name='ctc_marche']:checked").length;
	if(marche > 0)
	{
		$('#ctc_date_marche').prop('disabled',false);
	}
	else
	{
		$('#ctc_date_marche').val('00/00/0000');
		$('#ctc_date_marche').prop('disabled',true);
	}

	var alerte = $("input[name='ctc_alerte']:checked").length;
	if(alerte > 0)
	{
		$('#ctc_date_alerte').prop('disabled',false);
	}
	else
	{
		$('#ctc_date_alerte').val('00/00/0000');
		$('#ctc_date_alerte').prop('disabled',true);
	}
	
	$.get('<?php echo site_url('contacts/is_having_factures');?>/'+$(this).attr('data-id'), function(data){
		if(data == 1){
			$('#ctc_signe').val('1');
			$('#ctc_signe').prop('disabled',true);
		}			
	});
});


$("#template-modal-detail").on('change',"input[name='ctc_marche'],input[name='ctc_alerte']",function(){
	var dos = $("input[name='ctc_marche']:checked").length;
	if(dos > 0)
	{
		$('#ctc_date_marche').prop('disabled',false);
	}
	else
	{
		$('#ctc_date_marche').val('00/00/0000');
		$('#ctc_date_marche').prop('disabled',true);
	}

	var dos = $("input[name='ctc_alerte']:checked").length;
	if(dos > 0)
	{
		$('#ctc_date_alerte').prop('disabled',false);
	}
	else
	{
		$('#ctc_date_alerte').val('00/00/0000');
		$('#ctc_date_alerte').prop('disabled',true);
	}
});
</script>