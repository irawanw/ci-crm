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
                        <strong><?php echo $devis->scv_nom?><br /><?php echo formatte_texte_long($devis->scv_adresse)?></strong>
                        <br />Tél : <?php echo formatte_tel($devis->scv_telephone)?>
                        <br />Fax : <?php echo formatte_tel($devis->scv_fax)?>
                        <br />Capital : <?php echo $devis->scv_capital?>
                        <br />R.C.S. : <?php echo $devis->scv_rcs?>
                        <br />SIRET : <?php echo $devis->scv_siret?>
                    </td>
                    <td style="width: 60%;">
                        <table class="collapsed center" border="1">
                            <tr>
                                <th style="width:33.33%;">Devis N<sup>o</sup></th>
                                <th style="width:33.33%;">Date</th>
                                <th style="width:33.33%;">Client</th>
                            </tr>
                            <tr>
                                <td><?php echo $devis->dvi_reference?></td>
                                <td><?php echo formatte_date($devis->dvi_date)?></td>
                                <td><?php echo ($devis->ctc_id_comptable > 0)?$devis->ctc_id_comptable:'&nbsp;'?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td style="padding:25mm 0 15mm 10mm; line-height:5mm;">
                        <?php echo html_escape($devis->ctc_nom); ?>
                        <br />
                        <?php echo html_escape($devis->cor_nom.' '.$devis->cor_prenom); ?>
                        <br />
                        <?php echo formatte_texte_long($devis->ctc_adresse); ?>
                        <br />
                        <?php echo html_escape($devis->ctc_cp.' '.$devis->ctc_ville)?>
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
            $Taux_Tva = $devis->dvi_tva * 100;
            foreach ($articles As $article) {
                if ($article->ard_code != 'R') {
                    $prix_ligne = $article->ard_prix * $article->ard_quantite - $article->ard_remise_ht;
                    $remise_taux = $article->ard_remise_taux;
                    $remise_ht = $article->ard_remise_ht;
                }
                else {
                    $prix_ligne = - $article->ard_prix * $devis->dvi_montant_htnr;
                    $article->ard_quantite = '';
                    $remise_taux = '';
                    $remise_ht = '';
                }?>
                <tr>
                    <td><?php echo $article->ard_code?></td>
                    <td><?php echo $article->ard_description?></td>
                    <td style="text-align: right;"><?php echo $article->ard_quantite?></td>
                    <td style="text-align: right;"><?php echo number_format($article->ard_prix,3,',',' ')?></td>
                    <td style="text-align: right;"><?php echo ($remise_taux > 0)?number_format($remise_taux,2,',',' '):'&nbsp;'?></td>
                    <td style="text-align: right;"><?php echo ($remise_ht > 0)?number_format($remise_ht,2,',',' '):'&nbsp;'?></td>
                    <td style="text-align: right;"><?php echo number_format($prix_ligne,2,',',' ')?></td>
                    <td style="text-align: right;"><?php echo number_format($prix_ligne * $devis->dvi_tva,2,',',' ')?></td>
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
                            <td><?php echo number_format($devis->dvi_montant_ht,2,',',' ')?></td>
                            <td><?php echo number_format($Taux_Tva,2,',',' ')?> %</td>
                            <td><?php
                                $TVA = $devis->dvi_montant_ht * $devis->dvi_tva;
                                echo number_format($TVA,2,',',' ')?></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 32mm;">
                </td>
                <td style="width: 80mm;">
                    <table class="collapsed" style="width: 100%;" border="1">
                        <tr>
                            <th style="width: 50%; text-align:left;">Total HT</th><td style="width: 50%; text-align: right;"><?php echo number_format($devis->dvi_montant_ht,2,',',' ');?></td>
                        </tr>
                        <tr>
                            <th style="text-align:left;">Net HT</th><td style="text-align: right;"><?php echo number_format($devis->dvi_montant_ht,2,',',' ');?></td>
                        </tr>
                        <tr>
                            <th style="text-align:left;">Total TVA</th><td style="text-align: right;"><?php echo number_format($TVA,2,',',' ');?></td>
                        </tr>
                        <tr>
                            <th style="text-align:left;">Total TTC</th><td style="text-align: right;"><?php echo number_format($devis->dvi_montant_ttc,2,',',' ');?></td>
                        </tr>
                        <tr>
                            <th style="text-align:left;">NET A PAYER</th><td style="text-align: right;"><?php echo number_format($devis->dvi_montant_ttc,2,',',' ');?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <br /><br /><br />
    <div style="font-size:1.8mm;">Pénalités de retard (taux annuel) : <?php echo sprintf("%.2f",$devis->scv_taux_annuel)?>% - Escompte pour paiement anticipé (taux mensuel) : <?php echo sprintf("%.2f",$devis->scv_taux_mensuel)?>%
        <br /><br /><strong>RESERVE DE PROPRIETE</strong> : Nous nous réservons la propriété des marchandises jusqu'au paiement du prix par l'acheteur. Notre droit de revendication porte aussi bien sur les marchandises que sur leur prix si elles ont déjà été revendues (Loi du 12 mai 1980).
    </div>
</page>