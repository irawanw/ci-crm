<?php
$commande = $values['commande'];
$devis = $values['devis'];
$secteurs = $values['secteurs'];
?>

<div class="col-md-12 fiche">   
    <div class="row">
        <p>Consulter Une Commande</p>
    </div>
</div>
<div class="col-md-12 fiche">   
    <?php
    if(is_object($commande)):
    ?>
    <div class="row">
        <div class="col-sm-4">
            <p>Référence :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $commande->cmd_reference;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Date commande :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $commande->cmd_date;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Devis associé :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $commande->dvi_reference;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Client :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $commande->ctc_nom;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Nom :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $commande->cor_nom;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Montant devis HT :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $commande->dvi_montant_ht;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Montant devis TTC :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $commande->dvi_montant_ttc;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>% facturé :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $commande->cmd_p_facture;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>% réglé :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $commande->cmd_p_regle;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>État :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $commande->vec_etat;?></p>
        </div>
    </div>
    <?php else: ?>
    <div class="row">
        <p>Pas de commandes</p>
    </div>
    <?php endif; ?>
</div>

<div class="col-md-12 fiche">   
    <div class="row">
        <p>Détail d'un devis</p>
    </div>
</div>
<div class="col-md-12 fiche">
    <div class="row">
        <div class="col-sm-4">
            <p>Référence :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $devis->dvi_reference;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Date devis :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $devis->dvi_date;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Degré de chaleur :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $devis->vch_degre;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Client :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $devis->ctc_nom;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Correspondant :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $devis->cor_nom;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Enseigne :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $devis->scv_nom;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Montant devis HT :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $devis->dvi_montant_ht;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Montant devis TTC :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $devis->dvi_montant_ttc;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>État :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $devis->ved_etat;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>Remarques :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $devis->dvi_notes;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p>PDF :</p>
        </div>
        <div class="col-sm-8">
            <p><?php echo $devis->dvi_fichier;?></p>
        </div>
    </div>
</div>
<div class="col-md-12 fiche">
    <div class="row">
        <p>Secteurs rattachés à cette commande : Compte-rendu effectif</p>
    </div>
</div>
<div class="col-md-12 fiche">
    <div class="row">
        <table class="table table-bordered">
            <thead>
                <tr class="success">
                    <th>Secteur</th>
                    <th>HLM</th>
                    <th>RES</th>
                    <th>PAV</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($secteurs as $row): ?>
                <tr>
                    <td colspan="4" class="info"><?php echo $row['ville_name'];?></td>
                </tr>
                    <?php foreach($row['secteurs'] as $row): ?>
                    <tr>
                        <td><?php echo $row['secteur_name'];?></td>    
                        <td><?php echo $row['hlm'];?></td>
                        <td><?php echo $row['res'];?></td>
                        <td><?php echo $row['pav'];?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>