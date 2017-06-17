<p class="text-center">
<?php foreach($familles as $f) {?>
    &nbsp;<?php echo anchor("preparation/preparation/$f->vfm_code","$f->vfm_famille",'class="btn btn-default btn-sm" role="button"')?>&nbsp;
<?php }?>
</p>
