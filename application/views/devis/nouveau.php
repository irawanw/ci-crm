<div class="panel panel-default">
    <div class="panel-body">
        <?php echo form_open(site_url("devis/manipulation/$id/add"),array('id'=>'form_devis'),array('dvi_tva'=>$tva))?>
        <div class="row">
            <div class="col-md-2">
                <?php $class="";
                foreach ($listes_valeurs->dvi_societe_vendeuse as $enseigne) {?>
                <div id="enseigne<?php echo $enseigne->scv_id?>" class="<?php echo $class?>">
                    <strong><?php echo $enseigne->scv_nom?><br /><?php echo formatte_texte_long($enseigne->scv_adresse)?></strong>
                    <br />Tél : <?php echo formatte_tel($enseigne->scv_telephone)?>
                    <br />Fax : <?php echo formatte_tel($enseigne->scv_fax)?>
                    <br />Capital : <?php echo $enseigne->scv_capital?>
                    <br />R.C.S. : <?php echo $enseigne->scv_rcs?>
                    <br />SIRET : <?php echo $enseigne->scv_siret?>
                </div>
                <?php $class="hidden";
                }?>
            </div>
            <div class="col-md-6">
                <div style="border:1px solid lightgrey;padding:2px;margin-bottom:10px;">
                    <?php echo ctrl_select_data('dvi_client',"Client",$listes_valeurs->dvi_client,$contact,'ctc_id',"ctc_nom","ctc_activite",true)?>
                    <?php echo ctrl_select_fils('dvi_correspondant',"Correspondant",$listes_valeurs->dvi_correspondant,'','cor_id',"cor_nom","cor_contact",true)?>
                    <p class="text-center"><a class="btn btn-default" href="<?php echo site_url('contacts/nouveau')?>" role="button">Ajouter un contact</a></p>
                </div>
                <?php echo ctrl_select('dvi_societe_vendeuse',"Enseigne",$listes_valeurs->dvi_societe_vendeuse,'','scv_id',"scv_nom",true)?>
            </div>
            <div class="col-md-4">
                <p>&nbsp;</p>
                <?php echo ctrl_textarea('dvi_notes',"Remarques",'',false)?>
            </div>
        </div>
        <?php echo form_close()?>
        <span id="select-corr" style="visibility: hidden; position:absolute;"></span>
    </div>
</div>
<div id="grid"></div>

<div class="hidden">
<?php foreach ($familles as $f) {?>
    <div id="popup-<?php echo $f->vfm_code?>">
    <?php $this->load->view('_catalogues/'.$f->vfm_nom);?>
    </div>
<?php }?>
</div>
