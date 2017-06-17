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
<!-- <?= json_encode($descripteur) ?><br/>
<?= json_encode($descripteur['filterable_columns']) ?><br/>
<?= json_encode( array_keys($descripteur['filterable_columns']) ) ?> //-->
<div id="datatable_div" class="hidden">
<div id="datatable_div" style="width: 1592px;">
<style>

	.toolbar-container{
		border: 1px solid grey;
		padding: 5px;
		margin-bottom: 5px;
	}
	.context-title{
		float: right;
		color: \#337ab7;
		font-size: 15px;
	}
	.toolbar-container .form-group{
		margin-top: 0px;
		margin-bottom: 5px;
		font-size: 11px;
		float: left;
	}
	.toolbar-container .form-group label{
		float: left;
		margin-top: 7px;
		margin-right: 5px;
	}
	.toolbar-container .form-group .btn{
		float: left;
		margin-left: -5px;
		height: 30px;
	}
	.toolbar-container .form-group select{
		width: 110px;
		float: left;
		margin-right: 10px;
	}
	.toolbar-container .form-group input{
		width: 70%;
		float: left;
	}
	.nav-pills-custom{
		float: right;		
		margin-top: 5px;
	}
	.nav-pills-custom span{
		line-height: 18px
	}
	.nav-pills-custom li a{		
		padding: 3px 8px;
	}
	.form-group{
		margin-top: 5px;
		margin-bottom: 5px;
		font-size: 11px;
		float: left;
		padding: 0 5px;
	}
	.form-group label{
		float: left;
		margin-top: 7px;
		margin-right: 5px;
	}
	.form-group .btn{
		float: left;
		margin-left: 5px;
		height: 30px;
	}
	.form-group select{
		width: 200px;
		float: left;
	}
	.form-group input{
		width: 70%;
		float: left;
	}
	#sel_action_all, 
	#sel_view{
		width: 100px;
	}
	#datatable tfoot th{
	    padding: 0px;
		font-size: 11px;
		background-color: #9a9a9a;
		color: white;
	}	
	#group-title{
		font-size: 15px;
		text-align: center;
	}
</style>
<div class="toolbar-container">
<div id="group-title">
<b>VOUS CONSULTEZ LA TOURNEE JOURNALIERE</b>
<?php
if($valider!="yes"){
$valid_name="Devalider";
}
else{
$valid_name="Valider";
}
?>

<br>

<i><?php echo '<b>'.$descripteur['journ_nom'].'</b> - '.$descripteur['journ_date'].'-'.$valid_name;?> <!--1-October-2016 NON VALIDE--> </i>
</div>
<div class="form-group " >
<label for="action">Résultat de la livraison</label>
<?php if($valider!="yes"){ ?>
<select class="form-control input-sm" id="sel_action_all" name="sel_action_all" style="width:115px;" >
<option value="">sélectionner</option>
<option value="livre">livre</option>
<option value="non livre">non livre</option>
</select>
<button id="btnjourn_action_all" class="btn btn-default btn-xs" type="button">Ok</button>
<?php
 }
else{
?>	
<select class="form-control input-sm disabled" id="sel_action_all" name="sel_action_all" style="width:115px;color:#ccc;" >
<option value="">sélectionner</option>
</select>
<?php } ?>
</div>
<div class="form-group">
<label for="action">Vue</label>
<select id="sel_view" name="sel_view" class="form-control input-sm" onchange="location = this.value;">
<option selected="" value="<?php echo site_url().'/newtournee_journalieres/listnew/'.$descripteur['journ_id']."/encours";?>">En cours</option>
<option value="<?php echo site_url().'/newtournee_journalieres/listnew/'.$descripteur['journ_id']."/sup";?>">Supprimées</option>
<option value="<?php echo site_url().'/newtournee_journalieres/listnew/'.$descripteur['journ_id']."/touts";?>">Tout</option>
</select>
</div>
<ul class="nav nav-pills nav-pills-custom btn-single-action">
<?php if($valider!="yes"){
	?>
<li class="text-center enabled">
<a class="btn btn-default btn-xs" href="<?php echo site_url().'/newtournee_journalieres/new_adresse/'.$descripteur['journ_id'];?>">
<span>
Ajouter
<br>
Adresse
</span>
</a>
</li>
<?php }else { ?>
<li class="text-center disabled">
<a class="btn btn-default btn-xs" href="<?php echo site_url().'/newtournee_journalieres/adresse_error/';?>">
<span>
Ajouter
<br>
Adresse
</span>
</a>
</li>
<?php } ?>
<?php if($valider!="yes"){
	?>	
<li id="amalgame_modification" class="text-center enabled">
<div id="modify_adresse">
<a class="btn btn-default btn-xs" href="<?php echo site_url().'/newtournee_journalieres/modify_adresse_error/1';?>" data-cible="">
<span>
Consulter/Modifier
<br>
Adresse
</span>
</a>
</div>
</li>

<?php } else { ?>
<li id="amalgame_modification" class="text-center disabled">
<a class="btn btn-default btn-xs" href="<?php echo site_url().'/newtournee_journalieres/modify_adresse_error/0';?>" data-cible="">
<span>
Consulter/Modifier
<br>
Adresse
</span>
</a>
</li>
<?php } ?>

<li id="amalgame_voir_liste" class="text-center">
<a class="btn btn-default btn-xs" href="<?php echo site_url().'/newtournee_journalieres/listnew/'.$descripteur['journ_id']."/voir";?>" data-cible="">
<span>
Voir la liste
<br>
des tous les Adresses
</span>
</a>
</li>
</ul>
<div style="clear: both"></div>
</div>
    <div class="filter_clear_all"  id="filter_clear_all">Effacer tous les filtres de colonnes &#x2716;</div>
    <input type="text" class="filter_global" id="filter_input_global" placeholder="Chercher dans toutes les colonnes"></input>
    <div class="filter_global_clear"  id="filter_global_clear" title="Effacer le filtre global">&#x2716;</div>
    <table id="datatable" class="cell-border stripe">
        <thead>
        <tr id="data_table_columns_header" class="labels">
<?php foreach($descripteur['champs'] as $c) {?>
            <td class="filter_label" data="<?php echo $c[0]?>">
                <div class="movable"><?php echo $c[2]?>
                    <div class="sorter" id="sorter_<?php echo $c[0]?>" title="Sort">Sort</div> 
                </div>
                <?php if ( in_array($c[0], array_keys($descripteur['filterable_columns']))) : ?>
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
                <?php endif; ?>
            </td>
<?php }?>
        </tr>
<!--        <tr>
<?php foreach($descripteur['champs'] as $c) {?>
            <td class="movable" data="<?php echo $c[0]?>" >
                <?php if ( in_array($c[0], array_keys($descripteur['filterable_columns']))) : ?>
                <div class="filter_div">
                    <img class="filter_select" data="<?php echo $c[0]?>" datatype="<?php echo $descripteur['filterable_columns'][$c[0]] ?>" id="filter_select_<?php echo $c[0]?>" src="<?php echo base_url('assets/images/filter.png')?>" width="20" title="Select filter"/>
                    <div class="filter_text" id="filter_text_<?php echo $c[0]?>">&nbsp;</div>
                    <div class="filter_clear"  id="filter_clear_<?php echo $c[0]?>" title="Clear filter">&#x2716;</div>
                </div>
                <div class="filter_input_div">
                    <input type="text" id="filter_input_<?php echo $c[0]?>"
                        class="filter_input <?= ($descripteur['filterable_columns'][$c[0]]=='datetime')?' datetimepicker_mask':'' ?>
                                            <?= ($descripteur['filterable_columns'][$c[0]]=='date'    )?' datepicker_mask':'' ?>" 
                        <?= ($descripteur['filterable_columns'][$c[0]]=='date')?' placeholder="DD/MM/YYYY"':'' ?>
                        <?= ($descripteur['filterable_columns'][$c[0]]=='datetime')?' placeholder="DD/MM/YYYY HH:MM"':'' ?>
                    />
                    <input type="text" class="filter_type"  id="filter_type_<?php echo $c[0]?>" disabled readonly>
                </div>
                <?php endif; ?>
            </td>
<?php }?>
        </tr>
-->        </thead>
    </table>
</div>

<div id="filter_options_div">
    <ul id="filter_options" type="none" data="null">

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