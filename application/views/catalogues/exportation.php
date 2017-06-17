<div class="row">
    <div class="col-md-10 col-md-offset-1">
<?php if ($export == false) {?>
        <p>Un problème technique est survenu - veuillez reessayer ultérieurement.</p>
<?php }
else {?>
        <p>Vous pouvez télécharger le catalogue au moyen du lien suivant :<a href="<?php echo base_url($export)?>"><?php echo $export?></a>.</p>
<?php }?>
    </div>
</div>
