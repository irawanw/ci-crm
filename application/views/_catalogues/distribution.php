<!-- DataTables CSS -->
<link href="<?php echo base_url();?>assets/css/jquery.dataTables.css" rel="stylesheet" />
<!-- <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" /> -->
<link href="https://cdn.datatables.net/scroller/1.4.2/css/scroller.dataTables.min.css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/dt_customisations.css" rel="stylesheet" />
<link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.3/summernote.css" rel="stylesheet">
<!-- Datetime picker jquery plugin -->
<link href="<?php echo base_url();?>assets/css/jquery.datetimepicker.css" rel="stylesheet" /> 
<link href="<?php echo base_url();?>assets/css/jquery.periodpicker.min.css" rel="stylesheet" />
<!-- <link href="<?php echo base_url();?>crm/assets/css/jquery.timepicker.min.css" rel="stylesheet" /> -->

<div id="popup-distribution-table">
    <div id="grid_D">
        <div class="form-group" id="filter-date-content">
            <input type="text" class="filter_type form-control" id="filter_type_art_code" />
            <input type="hidden" class="filter_input" id="filter_input_art_code">
        </div>  

        <div class="row" style="margin-bottom: 15px">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="form_D_villes">Ville</label>
                <div class="col-sm-6">
                    <input type="text" name="form_D_villes" id="form_D_villes" class="form-control" style="width:400px" value="" placeholder="Ville">
                </div>
            </div>
        </div>
        <div class="row hidden" style="margin-bottom: 15px">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="form_D_secteurs">Secteur</label>
                <div class="col-sm-6">
                    <input type="text" name="form_D_secteurs" id="form_D_secteurs" class="form-control" style="width:400px" value="" placeholder="Secteur">
                </div>
            </div>
        </div>
        <div class="row" style="margin-bottom: 15px" id="secteurs-content">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="form_D_integral">Secteurs</label>
                <div class="col-sm-6" id="form_D_div_ville">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="form_D_integral" value="integral">Ville entière
                        </label>
                    </div>
                </div>
                <label class="col-sm-3 control-label" for="form_D_integral">&nbsp;</label>
                <div class="col-sm-6" id="form_D_div_secteurs">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="form_D_secteur[]" class="chk-secteur" value="secteur">
                        </label>
                    </div>
                </div>
            </div>
        </div>
    
    <?php
    $descripteur = array(
        'datasource'         => 'devis/catalogues_distribution',
        'champs'             => array(       
            array('art_code', 'text', "Code", 'art_code'),
            array('art_description', 'text', "Description", 'art_description'),
            array('art_habitat', 'text', "Habitat", 'art_habitat'),
            array('art_document', 'text', "Type Document", 'art_document'),
            array('art_distribution', 'text', "Type Distribution", 'art_distribution'),
            array('art_delai', 'text', "Delai", 'art_delai'),
            array('art_controle', 'text', "Controle", 'art_controle'),
            array('art_prix', 'text', "Prix Unitaire", 'art_prix'),
            array('art_prix_total', 'text', "Prix Total", 'art_prix_total'),
        ),
        'filterable_columns' => array(
            'art_code' => 'char',
            'art_description' => 'char',
            'art_habitat' => 'char',
            'art_document' => 'char',
            'art_distribution' => 'char',
            'art_delai' => 'char',
            'art_controle' => 'char',
            'art_prix' => 'double',
            'art_prix_total' => 'double',
        ),
    );
    ?>
    <!-- <div id="datatable_loading">Loading DataTable...</div> -->
    <div id="datatable_div" style="width: 1200px;">
        <table id="datatable" class="cell-border stripe">
            <thead>
            <tr id="data_table_columns_header" class="labels">
    <?php 
    foreach($descripteur['champs'] as $c) {
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
                        <img class="filter_select" data="<?php echo $c[0]?>" datatype="<?php echo $descripteur['filterable_columns'][$c[0]] ?>" id="filter_select_<?php echo $c[0]?>" src="<?php echo base_url('assets/images/filter_20.png')?>" title="Select filter"/>
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
                        <img class="filter_select" data="<?php echo $c[0]?>" datatype="<?php echo $descripteur['filterable_columns'][$c[0]] ?>" id="filter_select_<?php echo $c[0]?>" src="<?php echo base_url('assets/images/filter_20.png')?>" title="Select filter"/>
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
    <?php 
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

        
    </div>

    <p class="text-center"><button type="submit" disabled="disabled" class="btn btn-primary" id="form_D">Ajouter</button>
    <button type="button" class="btn btn-default" id="form_D_close">Fermer</button></p>
</div>