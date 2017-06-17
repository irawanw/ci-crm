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
});
	

function savedComment()
{
	var id = $('#comment_id').val();
	var comment = $('#comment_desc').val();
	$.ajax({
		url: "<?php echo site_url('demande_des_devis/savedCommentaires');?>/"+id+"/ajax",
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