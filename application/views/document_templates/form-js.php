<script type="text/javascript">
	$("#template-modal-detail").on('shown.bs.modal',function()
	{
		var form = $('#template-modal-detail form').attr('id');
		var container = document.getElementById(form);
		var info = '<div class="row guide info"><div class="form-group"><label class="col-sm-3 control-label" for="fields_info">Fields Info</label><div class="col-sm-9"><a id="btn-info" class="btn btn-sm btn-info" onclick="ShowHide()" style="margin-bottom:10px">Show</a><div class="fields well" style="display:none"><div>List of all available field are :</div><div class="row"><div class="col-md-6 col-sm-6 col-xs-6"><ul><li style="padding: 10px;">ctc_nom = contact name</li><li style="padding: 10px;">ctc_adresse = contact address</li><li style="padding: 10px;">ctc_cp = postal code</li><li style="padding: 10px;">ctc_ville = Ville</li><li style="padding: 10px;">message_date = Today date</li></ul></div><div class="col-md-6 col-sm-6 col-xs-6"><ul style="list-style-type: none;"><li style="padding: 5px;"><a class="btn btn-sm btn-primary" onclick="copyPaste(\'{ctc_nom}\')">Copy-Paste</a></li><li style="padding: 5px;"><a class="btn btn-sm btn-primary" onclick="copyPaste(\'{ctc_adresse}\')">Copy-Paste</a></li><li style="padding: 5px;"><a class="btn btn-sm btn-primary" onclick="copyPaste(\'{ctc_cp}\')">Copy-Paste</a></li><li style="padding: 5px;"><a class="btn btn-sm btn-primary" onclick="copyPaste(\'{ctc_ville}\')">Copy-Paste</a></li><li style="padding: 5px;"><a class="btn btn-sm btn-primary" onclick="copyPaste(\'{message_date}\')">Copy-Paste</a></li></ul></div></div><div>To insert field into the document please use format like <b>{ctc_nom}</b>.</div></div></div></div></div>';
		$('.guide').after(info);
		var action = $('#template-modal-detail form').attr('action');
		var isModif = action.search('modification');
		initEditor();
		if(isModif <= 0)
		{
		}
		else
		{
			var template_id = $('#template-modal-detail').attr('data-id');
			$.ajax({
				url:'document_templates/getTemplate',
				type:'GET',
				dataType: 'json',
				data:'template_id='+template_id,
				success:function(response)
				{
					tinyMCE.get('tpl_content').setContent(response,{format : 'raw'});
				}
			});
		}
	});

	function ShowHide()
	{
		 var style = $('.fields').css('display');
		 if(style =='none')
		 {
			$('#btn-info').text('hide');
			$('.fields').fadeIn(function(){});
		 }
		 else
		 {
			$('#btn-info').text('show');
			$('.fields').fadeOut(function(){});
		 }
	}
	
	$("#template-modal-detail").on('hide.bs.modal',function()
	{
		tinymce.remove();
	});

	function initEditor()
	{
		tinymce.init({
			selector: '#tpl_content',
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

	function copyPaste(message)
	{
		 //console.log(message);
         var editor = tinymce.get('tpl_content');
		 var content = editor.getContent();
         editor.setContent(content+message);
	}
	
</script>