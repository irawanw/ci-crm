<?php
$actions = array(
	'supprimer' => array(
		'value' => "remove",
		'text'  => "Supprimer",
	),
	'archiver' => array(
		'value' => "archiver",
		'text'  => "Archiver"
	),
	'reintegrer' => array(
		'value' => "unremove",
		'text'  => "Re-integrer"
	),
)
?>

<form class="form-inline">
	<div class="form-group">
		<label for="action">Actions de masse</label>
		<select class="form-control input-sm" id="sel_action_all">
			<?php if(isset($custom_mass_action_toolbar)):?>
				<?php foreach($custom_mass_action_toolbar as $val):?>
					<option value="<?php echo $actions[$val]['value'];?>"><?php echo $actions[$val]['text'];?></option>
				<?php endforeach; ?>
			<?php else:?>
				<?php foreach($actions as $i => $action):?>
				<option value="<?php echo $action['value'];?>"><?php echo $action['text'];?></option>
				<?php endforeach;?>
			<?php endif;?>
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

<!-- Modal Form Confirmation Delete -->
<div id="modal-mass-remove" class="modal fade" role="dialog">
	<div class="modal-dialog">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Supprimer la confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Etes-vous certain de vouloir supprimer le champ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
				<a href="#" onclick="massAction()" id="btn-mass-remove" class="btn btn-warning">Oui</a>
			</div>
		</div>
	</div>
</div>
<!-- Modal Form Confirmation Re-integrer -->
<div id="modal-mass-re-integrer" class="modal fade" role="dialog">
	<div class="modal-dialog">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Re-integrer la confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Etes-vous certain de vouloir re-integrer le champ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
				<a href="#" onclick="massAction()" id="btn-mass-re-integrer" class="btn btn-warning">Oui</a>
			</div>
		</div>
	</div>
</div>
