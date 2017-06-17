<div class="row custom-toolbar">
<form class="form-inline">
<div class="form-group">
	<label for="action">Actions de masse</label>
	<select class="form-control input-sm" id="sel_action_all">
		<option value="remove">Supprimer</option>
	</select>
	<button type="button" class="btn btn-default btn-sm" id="btn_action_all">Ok</button>
</div>	
</form>
</div>

<!-- Modal Form Confirmation Delete -->
<div id="modal-confirm-run" class="modal fade" role="dialog">
	<div class="modal-dialog">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Confirmation Run Script Migration</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure run this script?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
				<a href="#"  id="btn-run" class="btn btn-warning">Yes</a>
			</div>
		</div>
	</div>
</div>

<?php 
//load confirmation mass remove box
$this->load->view('templates/remove_confirmation.php'); 
?>