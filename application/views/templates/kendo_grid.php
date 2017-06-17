<div class="text-center">
<?php foreach ($cmd_globales as $c) {
    $cible = $c[1]."/$id";
    if ($cible == $this->uri->uri_string()) {
        $cmd = anchor("#",$c[0],'class="btn btn-'.$c[2].' btn-sm" role="button" disabled="disabled"').'&nbsp;';
    }
    else {
        $cmd = anchor($cible,$c[0],'class="btn btn-'.$c[2].' btn-sm" role="button"').'&nbsp;';
    }
    echo $cmd;
}?>
</div>

<?php 
//if the template called using ajax then skin toolbar
if(!isset($values['ajax']) || $values['ajax'] == 0){
?>
	<div>
	<?php if ($toolbar != '') {
		$this->load->view($toolbar.'_toolbar1');
	}
		if (!isset($cmd_masque_specifiques)) {?>
			<div class="btn-group btn-group-xs" role="group" aria-label="...">
				<button type="button" class="btn btn-default" id="rule_list">Organiser la vue</button>
				<button type="button" class="btn btn-default" id="save_list">Enregistrer la vue</button>
				<div class="btn-group btn-group-xs" role="group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Mes vues enregistrées
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" id="liste_vues">
		<?php foreach($vues as $v) {?>
						<li><a href="#<?php echo $v->vue_id?>"><?php echo $v->vue_nom?></a></li>
		<?php }?>
					</ul>
				</div>
			</div>
		<?php } ?>
	</div><br />
<?php } ?>

<div id="grid"></div>

<div class="modal fade" id="popup_reglage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Organisation des colonnes</h4>
            </div>
            <div class="modal-body">
                <div class="checkbox"><label><input type="checkbox" id="toutes_colonnes" value="">Tout cocher / décocher</label></div>
                <small>Décochez pour masquer, glissez-déposez pour changer l'ordre d'affichage</small>
                <ul id="liste_colonnes" class="list-unstyled">
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal" id="popup_reglage_sauver">Appliquer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="popup_suppression" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Confirmation de suppression</h4>
            </div>
            <div class="modal-body">
                Veuillez confirmer la suppression.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal" id="popup_suppr_do">Supprimer</button>
            </div>
        </div>
    </div>
</div>
