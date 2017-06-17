<div class="modal fade" id="comment_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="header-title">Ajouter un commentaire</h4>
			</div>
			<div class="modal-body"><input type="hidden" id="comment_id" name="comment_id">
			<textarea class="form-control" rows="3" id="comment_desc" name="comment_desc"></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-primary" onclick="savedComment()">Enregister</button>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	$(document).on('click','.update-contact-comment', function(e) {
		e.preventDefault();

		var id = $(this).attr('data-id');
		var desc = $(this).attr('data-desc');

		$('#comment_id').val(id);
		$('#comment_desc').val(desc);
		$('#comment_modal').modal('show');		
	});

	setTimeout(function() {
		loadTotalHelper();
	},300);	

	
	var old_done_typing = doneTyping;
    doneTyping = function() {
        old_done_typing.apply(this, arguments);
        loadTotalHelper();
    }
});


	

function loadTotalHelper() {		
	console.log("loadTotalHelper")
	var data = DT.ajax.params();
	var url = "<?php echo site_url('demande_devis_general/get_total/0');?>"

	$.ajax({
        url : url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            $('#totalContact').html(response.total);
            $('#totalSigned').html(response.total_signe);
            $('#percentageContact').html(response.percentage);
            $('#totalCa').html(response.total_ca);
        }
    });
}

function savedComment()
{
	var id = $('#comment_id').val();
	var comment = $('#comment_desc').val();
	$.ajax({
		url: "<?php echo site_url('demande_devis_general/savedCommentaires');?>/"+id+"/ajax",
		type:'POST',
		data: {comment: comment},
		dataType: 'json',
		success: function(response)
		{
			console.log(response);
			if(response.success) {
				$('#comment_modal').modal('hide');	
				var data = response.data;
				if (data.event.id) {
					var helper = actionMenuBar.datatable;
                    helper.reload(data.event.id);
                }

			}

			notificationWidget.show(response.message, response.notif);
		}
	});
}
</script>