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
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-sm-4">
                        <p><strong>Numéro :</strong></p>
                    </div>
                    <div class="col-sm-8">
                        <p><?php echo $values->fac_reference?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <p><strong>Date :</strong></p>
                    </div>
                    <div class="col-sm-8">
                        <p><?php echo formatte_date($values->fac_date)?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <p><strong>Remarques :</strong></p>
                    </div>
                    <div class="col-sm-8">
                        <p><?php echo $values->fac_notes?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="grid-lignes-factures"></div>
