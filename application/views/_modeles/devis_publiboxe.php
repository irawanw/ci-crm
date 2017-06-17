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
    .cg1 {
        padding-top: 1em
        padding-bottom:1em;
        text-align:center;
        font-weight: bold;
        font-size: 8 px;
    }
    .cg2 {
        padding-top: 1em
        padding-bottom:1em;
        text-align:center;
        font: italic;
        font-size: 8 px;
    }
    .cg3 {
        padding-top: 1em
        padding-bottom:1em;
        text-align:center;
        font-size: 7 px;
    }
    .cg4 {
        padding-top: 1em
        padding-bottom:1em;
        font-weight: bold;
        font-size: 7 px;
    }
    .cg5 {
        font-size: 7 px;
    }
    .pb {
        font-weight: bold;
        font-size: 7 px;
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
                                <th style="width:33.33%;">Devis N°</th>
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
                        <?php echo $devis->ctc_nom.'<br />'.formatte_texte_long($devis->ctc_adresse).'<br />'.$devis->ctc_cp.' '.$devis->ctc_ville?>
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
<pagebreak />
<p class="cg1">CONDITIONS GENERALES</p>

<p class="cg2">DISTRIBUTION et IMPRESSION</p>

<p class="cg3">PUBLIBOXE 37 bis rue du progres 93200 saint denis RCS  809567993 bobigny</p>



<p class="cg4">RESPONSABILITES</p>

<p class="cg5">Sauf stipulation préalable et pouvant entraîner un supplément tarifaire, PUBLIBOXE ne peut être tenu de faire une distribution en solo.<br />
En cas d’interdiction des autorités faisant obstacle au déroulement d’une diffusion ou d’une promotion, PUBLIBOXE n’est tenu à aucune forme de remboursement ni à aucun débit.<br />
Si les documents présentent un caractère non conforme aux lois ou aux mœurs, la diffusion peut être annulée, même après acceptation de l’ordre, sans qu’il puisse être réclamé des dommages-intérêts au PUBLIBOXE.</p>


<p class="cg4">LIVRAISON DELAIS DANS LE CAS OU LES DOCUMENTS AURAIENT ETES LIVRES CHEZ LE CLIENT</p>

<p class="cg5">Si la distribution est prévue pour une date ferme, les documents doivent être livrés dans les centres de distribution fixés une semaine au moins avant la date prévue pour le début de l’opération.<br />
Pour un retard de livraison de documents non communiqué au PUBLIBOXE 48 heures avant la date prévue du début de l’opération et qui entraînerait une modification de planning ou des frais non prévus initialement (paiement de distributeurs ou déplacements).<br />
PUBLIBOXE facturera le client défaillant du montant desdits frais supplémentaires.<br />
Si la distribution est prévue pour une date ferme, les intempéries (pluie, neige, verglas, etc.…) sont considérés comme des cas de force majeure pouvant entraîner des décalages dans les délais de distribution.</p>



<p class="cg4">RECLAMATIONS</p>

<p class="cg5">Les contrôles et sondages effectués directement par le client et les conclusions qui pourraient en être tirées ne seront pris en considération que dans la mesure où ils ont lieu contradictoirement pendant le déroulement de la distribution et en présence d’un membre du personnel du PUBLIBOXE dûment accrédité.<br />
En aucun cas PUBLIBOXE n’est tenu à une obligation sue le chiffre d’affaire réalisé suite à la distribution.<br />
Toute réclamation doit être transmise par courrier en recommande avec accusé de réception dans les 48 heures, après la date prévue ou réelle de fin de distribution.<br />
Il est convenu que PUBLIBOXE aura rempli ses engagement si 90 % des boîtes aux lettes retenues par le client et pouvant être atteintes, sont touchées par les documents à diffuser.<br />
Ceci suppose que en deçà de 10 % de défectuosité constatées et prouvées en présence des deux parties, PUBLIBOXE n’est tenu a aucun avoir ou remboursement sur la distribution.<br />
PUBLIBOXE décline toute responsabilité dans les cas suivants : absence de boîtes aux lettres, boîte trop étroites pour les documents, boîtes inaccessibles en raison de codes ou de dysfonctionnement des systèmes électroniques vigik, boîtes aux lettres avec des étiquettes ou indications de refus de publicité, habitations avec chiens méchants, poubelles ou véhicules empêchant l’accès aux boîtes aux lettres, ou documents enlevés par des tiers.<br />
Ces cas ne comptent pas comme des boîtes devant être touchées.<br />
En aucun cas PUBLIBOXE ne saurait être responsable de vols, dommages, incendie ou perte, causés par des tiers aux objets qui lui sont confiés aux fins de distribution.<br />
Les assurances pour couvrir tous ces risques sont à la charge du client.<br />
Quelque soit le cas de figure, PUBLIBOXE n’est jamais tenu de rembourser les documents qui lui sont confiés.<br />
Il est précisé que pour être efficace, le contrôle doit être effectué le soir de la distribution, ou au plus tard le lendemain. La méthode de contrôle consistant à demander à des proches ou clients s’ils ont reçu le prospectus n’est en général pas concluante : dans bien des cas les gens, de bonne foi croient ne pas avoir été distribués, pour un grand nombre de raisons :<br />
-	prospectus pris par un autre membre de la famille et jetés<br />
-	contrôle le mauvais jour<br />
-	prospectus passés inaperçus dans une liasse…</p>

<p class="cg4">CIBLAGE</p>

<p class="cg5">La société PUBLIBOXE propose, moyennant paiement supplémentaire, de distinguer pavillons, résidences privées, HLM. Cependant dans les secteurs (retenus par PUBLIBOXE) où le collectif privé ou locatif représente moins de 10% de l’habitat, la distinction n’est plus faite, elle doit obligatoirement l’être, quelque soit le cas de figure, dés que la résidence dépasse les 200 foyers.</p>

<p class="cg4">ORDRE</p>

<p class="cg5">Tout ordre passé directement ou par l’intermédiaire de nos attachés commerciaux ne nous engage que si nous avons accusé réception par écrit ou si le bon  de commande est signé par le client.<br />
La signature du bon de commande entraîne l’acceptation définitive du devis de facture.<br />
Toute modification ultérieure demandée par le client est subordonnée à l’acceptation par PUBLIBOXE.</p>


<p class="cg4">ANNULATION</p>

<p class="cg5">Une annulation devra parvenir 48 heures avant la date de réalisation prévue.
En tout état de cause, en cas d’annulation de commande, PUBLIBOXE se réserve le dr<br />oit de facturer le client des matières approvisionnées et des frais engagés pour son exécution.</p>


<p class="cg4">CONDITIONS DE REGLEMENTS-RETARDS ET CONTENTIEUX</p>

<p class="cg5">Nos factures sont payables à l’adresse indiquée au recto.
Nos lettres de change ou d’acceptation de règlement n’opèrent ni novation, ni dérogation à cette clause attributive de juridiction.<br />
Le retour des traites ou billets à ordre se fera dès réception de la facture ou du relevé par l’acheteur et au plus tard sous 48 heures comme en fait obligation l’article 125 du code du commerce.<br />
Passé 15 jours, nous nous réservons la possibilité de faire constater par projêt le refus d’acceptation conformément à l’article 148 du Code du Commerce.<br />
En cas de paiement par chèque, celui-ci doit être en possession du PUBLIBOXE au plus tard à l’échéance indiquée sur la facture et dûment acceptée par le client lors de la signature du bon de commande.<br />
Le non-paiement à l’échéance de tout ou partie de la facture rend immédiatement exigible le paiement de toutes les factures dues, y compris celles non échues.<br />
Tout retard de paiement est générateur de plein droit, et sans mise en demeure d’un intérêt légal au taux de base bancaire, majoré de 2 % sans que cette clause nuise à l’exigibilité de la dette.<br />
En plus de ces intérêts, sera à la charge du client défaillant tout autre frais directement lié à l’impayé (traite impayée, prorogation, protêt, relance, contentieux, etc.…).<br />
En cas d’action contentieuse, il sera appliqué à titre de clause pénale (article 1153 du Code Civil) une indemnité forfaitaire de 15 % sur les sommes restant dues.</p>

<p class="cg4">SPECIFICITES DE L’OFFRE DISTRIBUTION PLUS IMPRESSION</p>

<p class ="pb">Bon de commande :<br />
Les distributions s’effectuent sans autre bon de commande que le présent contrat, et, ce, jusqu’à écoulement du stock de prospectus. Du fait de l’impression des documents par la société PUBLIBOXE, la distribution est remisée de 50% sur les tarifs normaux (60 € les mille en secteur urbain, 70 € à 100 € les mille en secteur rural).<br />
    <br />
Choix des secteurs :<br />
Le choix des secteurs appartient au client, qui en remet la liste par écrit à la société PUBLIBOXE.<br />
Toute modification devra faire l’objet d’un fax ou d’un écrit.<br />
    <br />
Choix des dates de distribution :<br />
Le choix des dates de passage appartient dans le cadre de cette offre spécifique de distribution, à la société PUBLIBOXE, qui s’engage à couvrir les secteurs du client au minimum tous les deux mois et lui faire parvenir avec la facture un détail des dates et secteurs distribués.<br />
    <br />
Preuve de réalisation des imprimés :<br />
Compte tenu que la société PUBLIBOXE stocke les prospectus, afin de les distribuer immédiatement après fabrication, aucun bon de livraison chez le client ne pourra être demandé en cas de contestation. La société PUBLIBOXE enjoint, par fax ou mail, (ou en l’absence de ces deux outils, par téléphone) le client à venir constater la fabrication de la quantité demandée,  lequel client a deux jours pour venir constater les quantités au dépôt de la société PUBLIBOXE. Seules les preuves de fabrication feront foi passé ce délai, compte tenu que souvent la distribution des imprimés, et donc l’écoulement du stock, commence dés réception. La quantité ne pourra être conforme si le client a demandé à ce que la distribution commence avant son passage ; devront être déduits les imprimés déjà distribués.</p>


<p class="cg4">JURIDICTION</p>

<p class="cg5">En cas de contestations ou litiges, seul le tribunal de commerce dont dépend l’agence ayant pris la commande est compétent.<br />
Cette attribution de juridiction vaut également en cas pluralités de défendeurs et pour toutes demandes.</p>

