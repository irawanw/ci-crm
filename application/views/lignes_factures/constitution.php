<div id="grid-constitution-lignes-factures"></div>

<div class="hidden">
    <?php foreach ($familles as $f) {?>
        <div id="popup-<?php echo $f->vfm_code?>">
            <?php $this->load->view('_catalogues/'.$f->vfm_nom);?>
        </div>
    <?php }?>
</div>