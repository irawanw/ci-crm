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
                <ul class="dropdown-menu" id="liste_vues" style="width: 300px">
                    <?php $vue_index=0; foreach($vues as $v) { ?>
                        <li style="border-top: <?php echo ($vue_index++>0)?1:0; ?>px solid #D5D5D5"><a class="vue" href="#<?php echo $v->vue_id?>" style ="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo $v->vue_nom?>" >
                            <?php echo $v->vue_nom?>
                            <?php if ($v->vue_default_by_admin==1): ?>
                                <span id="default_by_admin_view_label" title="Global default by admin" style="float:right; font-size:8pt; color:#ff1111">&nbsp;<img src="<?php echo base_url('assets/images/default_view_global.png')?>" style="width:20px; height:20px;"/>&nbsp;</span>
                            <?php else: ?>
                                <button class="btn btn-default delete_view_btn" style="float:right; font-size:8pt; color:#EEE60B; background-color:#E49C05; padding:1px; padding-left:3px; padding-right:3px; ">DEL</button>
                            <?php endif;?>
                            <?php if ($v->vue_default==1): ?>
                                <span id="default_view_label" title="User default" style="float:right; font-size:10pt; font-weoght:bold; color:#11aa11">&nbsp;<img src="<?php echo base_url('assets/images/default_view_user.png')?>" style="width:20px; height:20px;"/>&nbsp;</span>
                            <?php endif;?>
                            </a>
                        </li>
                    <?php }?>
                </ul>
            </div>
            <button type="button" class="btn btn-default" id="make_default_list">Définir comme vue par défaut</button>
            <span id="current_view_label" class="btn btn-default" style="margin-left:2px; disable:none; border:none;"></span>
            <?php if($this->session->userdata('utl_profil')==1): ?>
            <button type="button" class="btn btn-default" id="make_global_default_list" style="background-color:#E4C005" title="Définir comme vue par défaut globale (pour tous les utilisateurs)">Définir comme vue par défaut globale</button>
            <?php endif; ?>
        </div>
    <?php }?>
</div><br />
<div id="datatable_loading">Loading DataTable...</div>
<div id="datatable_div">
    <div class="row custom-toolbar">
    <?php
        //load mass action toolbar
        if(isset($mass_action_toolbar)) {
            if($mass_action_toolbar == true) {
                if(isset($external_toolbar_data)) {
                    $external_toolbar_data['controleur'] = $controleur;
                    $this->view('templates/mass_action_toolbar', $external_toolbar_data);
                } else {
                    $this->view('templates/mass_action_toolbar', $controleur);
                }
            }
        }
        //load external toolbar view
        if(isset($external_toolbar)){
            $this->view($controleur.'/'.$external_toolbar, $external_toolbar_data);
        }
    ?>
    </div>
    <div class="filter_clear_all"  id="filter_clear_all">Effacer tous les filtres de colonnes &#x2716;</div>
    <input type="text" class="filter_global" id="filter_input_global" placeholder="Chercher dans toutes les colonnes"></input>
    <div class="filter_global_clear"  id="filter_global_clear" title="Effacer le filtre global">&#x2716;</div>
    <table id="datatable" class="cell-border stripe">
        <thead>
        <tr id="data_table_columns_header" class="labels">
<?php 
$viewable_fields = viewable_fields($controleur, 'read');
foreach($descripteur['champs'] as $c) {
    $is_viewable = verify_viewable_field($c[0], $viewable_fields);

    if($is_viewable || $c[0] == "checkbox"):
?>
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
                    <div class="deleteicon resizable">
                        <select id="filter_input_<?php echo $c[0]?>"
                            class="filter_input select_mask resizable" 
                        >
                            <option value="">(sélectionner)</option>
                        </select>
                        <span class="" style="right:-2px;"></span>
                    </div>
                    <input type="text" class="filter_type"  id="filter_type_<?php echo $c[0]?>" disabled readonly>
                    <div class="filter_text" id="filter_text_<?php echo $c[0]?>">&nbsp;</div>
                    <div class="filter_clear"  id="filter_clear_<?php echo $c[0]?>" title="Clear filter">&#x2716;</div>
                </div>
                <?php     elseif ($descripteur['filterable_columns'][$c[0]]=='datetime' || $descripteur['filterable_columns'][$c[0]]=='date'): ?>
                <div class="filter_combo_div">
                    <img class="filter_select" data="<?php echo $c[0]?>" datatype="<?php echo $descripteur['filterable_columns'][$c[0]] ?>" id="filter_select_<?php echo $c[0]?>" src="<?php echo base_url('assets/images/filter_20.png')?>" width="20" title="Select filter"/>
                        <div class="deleteicon datetime" style="display: inline-block; position: relative; width:16px; top:-13px">
                            <span class="is_hidden"></span>
                        </div>
                        <input type="text" id="filter_input_<?php echo $c[0]?>"
                                class="filter_input resizable clearable 
                                                                <?= ($descripteur['filterable_columns'][$c[0]]=='datetime')?' datetimepicker_mask':'' ?>
                                                                <?= ($descripteur['filterable_columns'][$c[0]]=='date'    )?' datepicker_mask':'' ?>" 
                                <?= ($descripteur['filterable_columns'][$c[0]]=='date'    )?' placeholder="DD/MM/YYYY"':'' ?>
                                <?= ($descripteur['filterable_columns'][$c[0]]=='datetime')?' placeholder="DD/MM/YYYY HH:MM"':'' ?>
                                style="display: inline-block; position: relative;"
                        />
                    <input type="text" class="filter_type"  id="filter_type_<?php echo $c[0]?>" disabled readonly>
                    <div class="filter_text" id="filter_text_<?php echo $c[0]?>">&nbsp;</div>
                    <div class="filter_clear"  id="filter_clear_<?php echo $c[0]?>" title="Clear filter">&#x2716;</div>
                </div>
                <?php     else: ?>
                <div class="filter_combo_div">
                    <img class="filter_select" data="<?php echo $c[0]?>" datatype="<?php echo $descripteur['filterable_columns'][$c[0]] ?>" id="filter_select_<?php echo $c[0]?>" src="<?php echo base_url('assets/images/filter_20.png')?>" width="20" title="Select filter"/>
                    <div class="deleteicon resizable">
                        <input type="text" id="filter_input_<?php echo $c[0]?>" class="filter_input resizable" />
                        <span class="is_hidden"></span>
                    </div>
                    <input type="text" class="filter_type"  id="filter_type_<?php echo $c[0]?>" disabled readonly>
                    <div class="filter_text" id="filter_text_<?php echo $c[0]?>">&nbsp;</div>
                    <div class="filter_clear"  id="filter_clear_<?php echo $c[0]?>" title="Clear filter">&#x2716;</div>
                </div>
                <?php     endif; ?>
                <?php endif; ?>
            </td>
<?php 
    endif;
}
?>
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

<div class="modal fade" id="popup_reglage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-reglage" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel-reglage">Organisation des colonnes</h4>
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

<div class="modal fade action-confirmation-modal" id="modal-retour-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-retour-confirm" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="close-retour" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel-servers-index-1">Confirmation d'opération</h4>
            </div>
            <div class="modal-body">
                voulez-vous sauvegarder les modifications effectuées?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="annuler-retour">NE PAS ENREGISTRER</button>
                <button type="button" class="btn btn-danger btn-confirm-action" role="button" id="enregister-retour">OUI ENREGISTRER</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade action-confirmation-modal" id="modal-generate-export" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-retour-confirm" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="close-retour" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel-servers-index-1">Generating File</h4>
            </div>
            <div class="modal-body">
                Please wait a while exporting data in progress..
                <img src="<?php echo base_url().'/assets/images/loading-circles-30.gif';?>">
            </div>
        </div>
    </div>
</div>
