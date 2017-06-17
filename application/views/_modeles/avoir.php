<style type="text/css">
    table.collapsed {
        border-collapse: collapse;
    }
    table.center {
        text-align:center;
    }
    table th {
        padding:6px 0;
        text-align:center;
        border: solid 1px #808080;
        background: #F0F0F0;
    }
    table.listing {
        border:2px #000 solid;
    }
    table.listing td {
        padding: 2mm;
        border: solid 1px #D0D0D0;
    }
</style>
<page backtop="0mm" backbottom="0mm" backleft="0mm" backright="0mm">
        <div>
            <table style="width: 100%;">
                <tr>
                    <td rowspan="2" style="width: 40%; line-height:5mm;">
                        <strong><?php echo $societe->scv_nom?><br /><?php echo formatte_texte_long($societe->scv_adresse)?></strong>
                        <br />Tél : <?php echo formatte_tel($societe->scv_telephone)?>
                        <br />Fax : <?php echo formatte_tel($societe->scv_fax)?>
                        <br />Capital : <?php echo $societe->scv_capital?>
                        <br />R.C.S. : <?php echo $societe->scv_rcs?>
                        <br />SIRET : <?php echo $societe->scv_siret?>
                    </td>
                    <td style="width: 60%;">
                        <table class="collapsed center" border="1">
                            <tr>
                                <th style="width:33.33%;">Avoir N°</th>
                                <th style="width:33.33%;">Date</th>
                                <th style="width:33.33%;">Client</th>
                            </tr>
                            <tr>
                                <td><?php echo $avoir->avr_reference?></td>
                                <td><?php echo formatte_date($avoir->avr_date)?></td>
                                <td><?php echo ($contact->ctc_id_comptable > 0)?$contact->ctc_id_comptable:'&nbsp;'?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td style="padding:25mm 0 15mm 10mm; line-height:5mm;">
                        <?php echo $contact->ctc_nom.'<br />'.formatte_texte_long($contact->ctc_adresse).'<br />'.$contact->ctc_cp.' '.$contact->ctc_ville?>
                    </td></tr>
            </table>
        </div>
    <div style="margin: 30mm 0 0 0;">
        <table class="listing collapsed" style="width: 180mm; font-size:2mm;">
            <tr>
                <th style="width: 12.5%;">Référence</th>
                <th style="width: 12.5%;">Désignation</th>
                <th style="width: 12.5%;">Quantité</th>
                <th style="width: 12.5%;">P.U. HT</th>
                <th style="width: 12.5%;">% REM</th>
                <th style="width: 12.5%;">Remise HT</th>
                <th style="width: 12.5%;">Montant HT</th>
                <th style="width: 12.5%;">TVA</th>
            </tr>
            <?php
            $Taux_Tva = $avoir->avr_tva * 100;
            foreach ($lignes As $ligne) {
                if ($ligne->lia_code != 'R') {
                    $prix_ligne = $ligne->lia_prix * $ligne->lia_quantite - $ligne->lia_remise_ht;
                    $remise_taux = $ligne->lia_remise_taux;
                    $remise_ht = $ligne->lia_remise_ht;
                }
                else {
                    $prix_ligne = - $ligne->lia_prix * $avoir->avr_montant_htnr;
                    $ligne->lia_quantite = '';
                    $remise_taux = '';
                    $remise_ht = '';
                }?>
                <tr>
                    <td><?php echo $ligne->lia_code?></td>
                    <td><?php echo $ligne->lia_description?></td>
                    <td style="text-align: right;"><?php echo $ligne->lia_quantite?></td>
                    <td style="text-align: right;"><?php echo number_format($ligne->lia_prix,3,',',' ')?></td>
                    <td style="text-align: right;"><?php echo ($remise_taux > 0)?number_format($remise_taux,2,',',' '):'&nbsp;'?></td>
                    <td style="text-align: right;"><?php echo ($remise_ht > 0)?number_format($remise_ht,2,',',' '):'&nbsp;'?></td>
                    <td style="text-align: right;"><?php echo number_format($prix_ligne,2,',',' ')?></td>
                    <td style="text-align: right;"><?php echo number_format($prix_ligne * $avoir->avr_tva,2,',',' ')?></td>
                </tr>
            <?php }?>
        </table>
        <br /><br /><br />
        <table style="font-size:2.5mm;">
            <tr>
                <td style="width: 80mm;">
                    <table class="collapsed center" border="1">
                        <tr>
                            <th style="width: 25%;">Code</th>
                            <th style="width: 25%;">Base HT</th>
                            <th style="width: 25%;">Taux TVA</th>
                            <th style="width: 25%;">Montant TVA</th>
                        </tr>
                        <tr>
                            <td><?php echo $Taux_Tva?></td>
                            <td><?php echo number_format($avoir->avr_montant_ht,2,',',' ')?></td>
                            <td><?php echo number_format($Taux_Tva,2,',',' ')?> %</td>
                            <td><?php
                                $TVA = $avoir->avr_montant_ht * $avoir->avr_tva;
                                echo number_format($TVA,2,',',' ')?></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 32mm;">
                </td>
                <td style="width: 80mm;">
                    <table class="collapsed" style="width: 100%;" border="1">
                        <tr>
                            <th style="width: 50%; text-align:left;">Total HT</th><td style="width: 50%; text-align: right;"><?php echo number_format($avoir->avr_montant_ht,2,',',' ');?></td>
                        </tr>
                        <tr>
                            <th style="text-align:left;">Net HT</th><td style="text-align: right;"><?php echo number_format($avoir->avr_montant_ht,2,',',' ');?></td>
                        </tr>
                        <tr>
                            <th style="text-align:left;">Total TVA</th><td style="text-align: right;"><?php echo number_format($TVA,2,',',' ');?></td>
                        </tr>
                        <tr>
                            <th style="text-align:left;">Total TTC</th><td style="text-align: right;"><?php echo number_format($avoir->avr_montant_ttc,2,',',' ');?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</page>