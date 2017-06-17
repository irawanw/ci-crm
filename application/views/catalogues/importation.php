<div class="row">
    <div class="col-md-10 col-md-offset-1">
<?php if ($resultat === false) {
        echo form_open_multipart(current_url(),array('role'=>'form','class'=>'form-horizontal'));
        echo ctrl_upload('catalogue',"Catalogue",'.xls,.xlsx',true);?>
        <br />
        <p class="text-center"><button type="submit" class="btn btn-primary">Importer</button></p>
<?php echo form_close();
}
else {?>
        <p>Le chargement du catalogue a été abandonné à cause des erreurs suivantes :</p>
        <ul>
<?php foreach ($resultat as $r) {?>
            <li><?php echo $r?></li>
<?php }?>
        </ul>
<?php }?>
    </div>
</div>
