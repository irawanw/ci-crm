<?php if (count($menus['lateral']) > 0) {?>
    <div class="btn-group-vertical" role="group" aria-label="Menu latéral">
<?php foreach ($menus['lateral'] as $k => $m) {
        $compte = 0;
        foreach ($m as $c) {
            if (in_array($profil,$c[1])) {
                $compte++;
            }
        }
        if ($compte > 0) { ?>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
<?php       echo $k ?><span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
<?php       foreach ($m as $nom => $c) {
                if (in_array($profil,$c[1])) { ?>
                 <li><?php echo anchor($c[0], $nom, 'target="_blank"') ?></li>
<?php           }
            }?>
            </ul>
        </div>
<?php   } ?>
    </div>
<?php }
}?>
