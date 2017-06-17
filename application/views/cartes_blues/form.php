<!-- Modal Form Ajouter -->
<div id="modal-form-ajouter" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Form</h4>
      </div>
      <form method="POST" action="<?php echo site_url('champs/nouveau');?>" id="form-add-champs">
			<div class="modal-body">
            <div id="form-alert" class="alert alert-danger" style="display: none;">
              
            </div>
            <input type="hidden" name="champ_id" />
      			<input type="hidden" name="champ_name" />
            <div class="form-group">
              <label>Entrez le champ</label>
              <input type="text" name="champ_value" class="form-control" />
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
		    <button type="submit" class="btn btn-primary">Save</button>
      </div>
			</form>
    </div>
     <!-- Eof Modal content-->
  </div>
</div>
<!-- Eof Modal Form Ajouter -->


