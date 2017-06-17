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

<?php 
//load confirmation mass remove box
$this->load->view('templates/remove_confirmation.php'); 
$this->load->view('templates/modal_upload_files.php'); 
?>