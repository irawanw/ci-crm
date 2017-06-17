<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2">
                <p><strong><?php echo $values->scv_nom?><br /><?php echo formatte_texte_long($values->enseigne->scv_adresse)?></strong>
                <br />Tél : <?php echo formatte_tel($values->enseigne->scv_telephone)?>
                <br />Fax : <?php echo formatte_tel($values->enseigne->scv_fax)?>
                <br />Capital : <?php echo $values->enseigne->scv_capital?>
                <br />R.C.S. : <?php echo $values->enseigne->scv_rcs?>
                    <br />SIRET : <?php echo $values->enseigne->scv_siret?></p>
                <p><strong>Commercial :</strong><br />
                    <span id="ctc_commercial"><a class="view-detail" href="<?php echo site_url('employes/detail/'.$values->ctc_commercial) ?>"><?php echo html_escape($values->vcv_civilite.' '.$values->emp_nom.' '.$values->emp_prenom); ?></a></span></p>
            </div>
            <form id="form_devis" action="<?php echo site_url('devis/modification');?>/<?php echo $values->dvi_id; ?>/ajax" method="post">
                <input type="hidden" name="__form" value="<?php echo mt_rand(); ?>" />
                <input type="hidden" name="dvi_tva" value="<?php echo $tva ?>" />
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php echo ctrl_select('dvi_client', "Client", $listes_valeurs->dvi_client,$values->dvi_client,'ctc_id', 'ctc_nom',true) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php echo ctrl_select('dvi_correspondant',"Contact client",$listes_valeurs->dvi_correspondant,$values->dvi_correspondant,'cor_id',"cor_nom",false)?>
                        </div>
                        <div class="col-sm-8 col-sm-offset-3">
                            <p id="dvi_adresse_client"><?php echo construit_lien_detail('contacts', $values->ctc_id, $values->ctc_nom)?><br>
                                <?php echo html_escape($values->ctc_adresse)?><br>
                                <?php echo html_escape($values->ctc_cp.' '.$values->ctc_ville)?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p><strong>Remarques&nbsp;:</strong></p>
                            <textarea name="dvi_notes" maxlength="1000" class="form-control"><?php echo htmlspecialchars($values->dvi_notes)?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row form-group">
                        <div class="col-sm-5">
                            <label for="dvi_numero">Numéro série&nbsp;:</label>
                        </div>
                        <div class="col-sm-7">
                            <input type="number" id="dvi_numero" required="required" value="<?php echo htmlspecialchars($values->dvi_numero)?>" name="dvi_numero" step="1" class="form-control" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-5">
                            <label for="dvi_reference">Numéro de pièce&nbsp;:</label>
                        </div>
                        <div class="col-sm-7">
                            <input type="text" id="dvi_reference" required="required" value="<?php echo htmlspecialchars($values->dvi_reference)?>" name="dvi_reference" class="form-control" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-5">
                            <label for="dvi_date">Date&nbsp;:</label>
                        </div>
                        <div class="col-sm-7">
                            <input type="text" id="dvi_date" required="required" value="<?php echo formatte_date($values->dvi_date)?>" name="dvi_date" class="form-control form-date-field" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
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
