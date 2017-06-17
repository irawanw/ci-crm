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
            <form id="form-avoir-modification" action="<?php echo site_url('avoirs/modification');?>/<?php echo $values->avr_id; ?>/ajax" method="post">
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
                            <p><?php /* echo construit_lien_detail('correspondants', $values->cor_id, $values->cor_nom) */?>
                                <select name="avr_correspondant" class="form-control">
                                    <?php
                                        foreach ($listes_valeurs->avr_correspondant as $lv) {
                                            $selected = ($values->avr_correspondant == $lv->id) ? ' selected="selected"' : ''; ?>
                                            <option value="<?php echo htmlspecialchars($lv->id) ?>"<?php echo $selected;?>><?php echo htmlspecialchars($lv->value) ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-sm-4">
                            <p><strong>Numéro :</strong></p>
                        </div>
                        <div class="col-sm-8">
                            <p><?php echo $values->avr_reference?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <p><strong>Date :</strong></p>
                        </div>
                        <div class="col-sm-8">
                            <p><?php /* echo formatte_date($values->avr_date) */?>
                                <input type="text" required="required" value="<?php echo formatte_date($values->avr_date)?>" name="avr_date" class="form-control form-date-field" />
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <p><strong>Type d'avoir :</strong></p>
                        </div>
                        <div class="col-sm-8">
                            <p>
                                <select name="avr_type" class="form-control">
                                    <?php
                                    foreach ($listes_valeurs->avr_type as $lv) {
                                        $selected = ($values->avr_type == $lv->id) ? ' selected="selected"' : ''; ?>
                                        <option value="<?php echo htmlspecialchars($lv->id) ?>"<?php echo $selected;?>><?php echo htmlspecialchars($lv->value) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <p><strong>Remarques :</strong></p>
                        </div>
                        <div class="col-sm-8">
                            <textarea name="avr_justification" maxlength="1000" class="form-control"><?php echo htmlspecialchars($values->avr_justification)?></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="grid-constitution-lignes-avoirs"></div>

<div class="hidden">
    <?php foreach ($familles as $f) {?>
        <div id="popup-<?php echo $f->vfm_code?>">
            <?php $this->load->view('_catalogues/'.$f->vfm_nom);?>
        </div>
    <?php }?>
</div>
