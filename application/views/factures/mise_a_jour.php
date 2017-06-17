<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2">
                <strong><?php echo $values->scv_nom?><br /><?php echo formatte_texte_long($values->enseigne->scv_adresse)?></strong>
                <br />Tél : <?php echo formatte_tel($values->enseigne->scv_telephone)?>
                <br />Fax : <?php echo formatte_tel($values->enseigne->scv_fax)?>
                <br />Capital : <?php echo $values->enseigne->scv_capital?>
                <br />R.C.S. : <?php echo $values->enseigne->scv_rcs?>
                <br />SIRET : <?php echo $values->enseigne->scv_siret?>
            </div>
            <form id="form-facture-modification" action="<?php echo site_url('factures/modification');?>/<?php echo $values->fac_id; ?>/ajax" method="post">
                <input type="hidden" name="__form" value="<?php echo mt_rand(); ?>" />
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-sm-4">
                            <p><strong>Client :</strong></p>
                        </div>
                        <div class="col-sm-8">
                            <p><?php echo construit_lien_detail('contacts', $values->ctc_id, $values->ctc_nom)?><br>
                                <?php echo $values->ctc_adresse?><br>
                                <?php echo $values->ctc_cp?> <?php echo $values->ctc_ville?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <p><strong>Correspondant :</strong></p>
                        </div>
                        <div class="col-sm-8">
                            <p><?php echo construit_lien_detail('correspondants', $values->cor_id, $values->cor_nom)?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p><strong>Remarques&nbsp;:</strong></p>
                            <textarea name="fac_notes" maxlength="1000" class="form-control"><?php echo htmlspecialchars($values->fac_notes)?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row form-group">
                        <div class="col-sm-5">
                            <label for="fac_numero">Numéro comptable&nbsp;:</label>
                        </div>
                        <div class="col-sm-7">
                            <input type="number" id="fac_numero" required="required" value="<?php echo htmlspecialchars($values->fac_numero)?>" name="fac_numero" step="1" class="form-control" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-5">
                            <label for="fac_reference">N° de pièce&nbsp;:</label>
                        </div>
                        <div class="col-sm-7">
                            <input type="text" id="fac_reference" required="required" value="<?php echo htmlspecialchars($values->fac_reference)?>" name="fac_reference" class="form-control" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-5">
                            <label for="fac_date">Date&nbsp;:</label>
                        </div>
                        <div class="col-sm-7">
                            <input type="text" id="fac_date" required="required" value="<?php echo formatte_date($values->fac_date)?>" name="fac_date" class="form-control form-date-field" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-5">
                            <label for="fac_delai_paiement">Délai de paiement&nbsp;:</label>
                        </div>
                        <div class="col-sm-7">
                            <input type="number" id="fac_delai_paiement" step="1" min="0" required="required" value="<?php echo formatte_date($values->fac_delai_paiement)?>" name="fac_delai_paiement" class="form-control" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-5">
                            <label for="fac_type">Type de facture&nbsp;:</label>
                        </div>
                        <div class="col-sm-7">
                            <select name="fac_type" id="fac_type" class="form-control">
                                <?php
                                foreach ($listes_valeurs->fac_type as $lv) {
                                    $selected = ($values->fac_type == $lv->id) ? ' selected="selected"' : ''; ?>
                                    <option value="<?php echo htmlspecialchars($lv->id) ?>"<?php echo $selected;?>><?php echo htmlspecialchars($lv->value) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="grid-constitution-lignes-factures"></div>

<div class="hidden">
    <?php foreach ($familles as $f) {?>
        <div id="popup-<?php echo $f->vfm_code?>">
            <?php $this->load->view('_catalogues/'.$f->vfm_nom);?>
c        </div>
    <?php }?>
</div>
