<div class="row custom-toolbar">
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

<!-- Modal Form View Long Text -->
<div id="modal-form-view-text" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Form View Long Text</h4>
      </div>
			<form method="post" action="<?php echo site_url('max_bulk/update_value');?>">
			<div class="modal-body">
					<div class="form-group">
						<a class="btn btn-primary btn-copy-text">Copy</a>
					</div>
				  <div class="form-group">
						<textarea class="form-control" name="message" id="message" rows="8" cols="40"></textarea>
					</div>
					<input type="hidden" name="id" id="id" value="0">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input class="btn btn-primary" type="submit" value="OK">
      </div>
			</form>
    </div>
  </div>
</div>
<!-- EOf Modal Form View Long Text -->

<?php 
//load confirmation mass remove box
$this->load->view('templates/remove_confirmation.php'); 
?>