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
                        <li><a class="vue" href="#<?php echo $v->vue_id?>"><?php echo $v->vue_nom?></a></li>
                    <?php }?>
                </ul>
            </div>
        </div>
    <?php }?>
</div><br />
<div id="datatable_loading">Loading DataTable...</div>
<div id="datatable_div">
	<?php
		//load external toolbar view
		if(isset($external_toolbar)){
			$this->view($controleur.'/'.$external_toolbar, $external_toolbar_data);
		}
	?>
    <div class="filter_clear_all"  id="filter_clear_all">Effacer tous les filtres de colonnes &#x2716;</div>
    <input type="text" class="filter_global" id="filter_input_global" placeholder="Chercher dans toutes les colonnes"></input>
    <div class="filter_global_clear"  id="filter_global_clear" title="Effacer le filtre global">&#x2716;</div>
    <table id="datatable" class="cell-border stripe">
        <thead>
        <tr id="data_table_columns_header" class="labels">
<?php foreach($descripteur['champs'] as $c) {?>
            <td class="filter_label" data="<?php echo $c[0]?>" <?php if(count($c)>3 && $c[3]=="invisible"):?>style="width:0px;"<?php endif;?>>
                <div class="movable">
                    <span><?php echo $c[2]?></span>
                    <?php if ($c[0]!="CBSelect" && $c[2]!="RELANCES"  && $c[2]!="__DT_Row_ID"): ?>
                    <div class="sorter" id="sorter_<?php echo $c[0]?>" title="Sort">Sort</div> 
                    <?php endif; ?>
                </div>
                <?php if ($c[0]=="CBSelect"): ?>
                    <div>
                        <input type="checkbox" id="check-all" style="margin-left:10px; margin-right:10px;"/>
                    </div> 
                <?php endif; ?>
                <?php if ( in_array($c[0], array_keys($descripteur['filterable_columns']))) : ?>
                <?php     if ( $c[1]=='select' ) : ?>
                <div class="filter_combo_div">
                    <img class="filter_select" data="<?php echo $c[0]?>" datatype="<?php echo $descripteur['filterable_columns'][$c[0]] ?>" id="filter_select_<?php echo $c[0]?>" src="<?php echo base_url('assets/images/filter_20.png')?>" width="20" title="Select filter"/>
                    <select id="filter_input_<?php echo $c[0]?>"
                        class="filter_input select_mask resizable" 
                    >
                        <option value="">(sélectionner)</option>
                    </select>
                    <input type="text" class="filter_type"  id="filter_type_<?php echo $c[0]?>" disabled readonly>
                    <div class="filter_text" id="filter_text_<?php echo $c[0]?>">&nbsp;</div>
                    <div class="filter_clear"  id="filter_clear_<?php echo $c[0]?>" title="Clear filter">&#x2716;</div>
                </div>
                <?php     else: ?>
                <div class="filter_combo_div">
                    <img class="filter_select" data="<?php echo $c[0]?>" datatype="<?php echo $descripteur['filterable_columns'][$c[0]] ?>" id="filter_select_<?php echo $c[0]?>" src="<?php echo base_url('assets/images/filter_20.png')?>" width="20" title="Select filter"/>
                    <input type="text" id="filter_input_<?php echo $c[0]?>"
                        class="filter_input resizable   <?= ($descripteur['filterable_columns'][$c[0]]=='datetime')?' datetimepicker_mask':'' ?>
                                                        <?= ($descripteur['filterable_columns'][$c[0]]=='date'    )?' datepicker_mask':'' ?>" 
                        <?= ($descripteur['filterable_columns'][$c[0]]=='date'    )?' placeholder="DD/MM/YYYY"':'' ?>
                        <?= ($descripteur['filterable_columns'][$c[0]]=='datetime')?' placeholder="DD/MM/YYYY HH:MM"':'' ?>
                    />
                    <input type="text" class="filter_type"  id="filter_type_<?php echo $c[0]?>" disabled readonly>
                    <div class="filter_text" id="filter_text_<?php echo $c[0]?>">&nbsp;</div>
                    <div class="filter_clear"  id="filter_clear_<?php echo $c[0]?>" title="Clear filter">&#x2716;</div>
                </div>
                <?php     endif; ?>
                <?php endif; ?>
            </td>
<?php }?>
        </tr>
        </thead>
    </table>
</div>

<div id="filter_options_div">
    <ul id="filter_options" type="none" data="null">

    </ul>
</div>
<div id="select_mask_options_div">
    <ul id="select_mask_options" type="none" data="null">

    </ul>
</div>


<br/>
<div id="err_div" style="display:none">
    <span id="err_msg">:-{</span>
</div>

<div class="modal fade" id="popup_reglage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Organisation des colonnes</h4>
            </div>
            <div class="modal-body">
                <div class="checkbox"><label><input type="checkbox" id="toutes_colonnes" value="">Tout cocher / décocher</label></div>
                <small id="liste_colonnes_fixed_label" style="display:none;">Colonnes fixes, ne peut masquer ou modifier l'ordre d'affichage</small>
                <ul id="liste_colonnes_fixed"  style="display:none;"class="list-unstyled">
                </ul>
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