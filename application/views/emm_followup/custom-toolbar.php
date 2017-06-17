<div class="row">
<form class="form-inline">
<div class="form-group">
	<label for="action">Actions de masse</label>
	<select class="form-control input-sm" id="sel_action_all">
		<option value="remove">Supprimer</option>
		<option value="unremove">re-integrer</option>
	</select>
	<button type="button" class="btn btn-default btn-sm" id="btn_action_all">Ok</button>
</div>
<div class="form-group">
    <label for="action">Vue</label>
    <select class="form-control input-sm" id="sel_view">
		<option value="">[Select]</option>
		<option <?php if(uri_string()==$controleur) echo "selected"; ?>
			value="<?php echo site_url($controleur); ?>">En cours</option>
        <option <?php if(uri_string()==$controleur.'/archiver') echo "selected"; ?>
			value="<?php echo site_url($controleur.'/archiver'); ?>">Archivées</option>
        <option <?php if(uri_string()==$controleur.'/supprimees') echo "selected"; ?>
			value="<?php echo site_url($controleur.'/supprimees'); ?>">Supprimées</option>
		<option <?php if(uri_string()==$controleur.'/all') echo "selected"; ?>
			value="<?php echo site_url($controleur.'/all'); ?>">Tout</option>
    </select>
</div>
</form>
</div>

<!-- Modal Form Upload File -->
<div id="modal-form-upload" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Form Upload File</h4>
      </div>
			<form enctype="multipart/form-data" method="post" action="<?php echo site_url('emm_followup/upload_message');?>">
			<div class="modal-body">

					<input type="file" name="message" id="message" required="">
					<input type="hidden" name="id" id="upload_id" value="0">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input class="btn btn-primary" type="submit" value="Submit">
      </div>
			</form>
    </div>
  </div>
</div>
<!-- Eof Modal Form Upload File -->
