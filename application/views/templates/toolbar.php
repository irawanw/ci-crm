<?php $rang = 0;
$nb = array();
$i = 0;
foreach ($menus['indicateurs_toolbar'] as $m) {
    $nombre = eval("return $m();");
    if ($nombre > 0) {
        $nb[$i++] = " ($nombre)";
    }
    else {
        $menus['couleurs_toolbar'][$i] = '#9d9d9d';
        $nb[$i++] = '';
    }

}
foreach ($menus['toolbar'] as $k=>$m) {
    if (!isset($m[0])) {
        $compte = 0;
        foreach ($m as $c) {
            if (verifie_droits($droits,$profil,$c)) {
                $compte++;
            }
        }
        if ($compte > 0) { ?>
            <li class="dropdown">
                <a style="color:<?php echo $menus['couleurs_toolbar'][$rang] ?>"
                   href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                   aria-expanded="false"><?php echo $k ?><?php echo $nb[$rang]?><span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    <?php foreach ($m as $nom => $c) {
                        if (verifie_droits($droits,$profil,$c)) { ?>
                            <li><?php echo anchor($c, $nom) ?></li>
                        <?php }
                    } ?>
                </ul>
            </li>
<?php   }
    }
    else {
        if (verifie_droits($droits,$profil,$m)) {?>
            <li><a style="color:<?php echo $menus['couleurs_toolbar'][$rang] ?>"
                   href="<?php echo site_url($m)?>"><?php echo $k ?><?php echo $nb[$rang]?></a></li>
<?php }
    }
    $rang++;
}?>
