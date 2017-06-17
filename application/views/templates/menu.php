<?php if (isset($menus['image'])) {
    $image = $menus['image'];
}
else {
    $image = '';
}
$info_menu = explode("|",$menu);
?>
<nav class="navbar navbar-fixed-top navbar-inverse">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo site_url()?>">
                <?php if ($image == '') {
                    echo $menus['nom'];
                }
                else {
                    echo '<img alt="Brand" src="'.base_url("assets/images/$image"). '">';
                }?></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <?php foreach ($menus['gauche'] as $k=>$m) {
                    if (!isset($m[0])) {
                        $compte = 0;
                        foreach ($m as $c) {

                            if(!is_array($c)) {
                                if (verifie_droits($droits,$profil,$c)) {
                                    $compte++;
                                }
                            }
                        }
                        if ($compte > 0) { ?>
                            <li class="dropdown <?php if ($info_menu[0] == $k) echo "active" ?>">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-expanded="false"><?php echo $k ?><span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <?php foreach ($m as $nom => $c) {
                                        if(!is_array($c)) {
                                            if (substr($c,0,4) == 'http') {?>
                                                <li><a href="<?php echo $c?>" target="_blank"><?php echo $nom?></a></li>
                                            <?php }
                                            elseif (verifie_droits($droits,$profil,$c)) {?>
                                                <li><?php echo anchor($c, $nom, 'target="_blank"') ?></li>
                                            <?php }
                                        } else { ?>
                                            <li class="dropdown dropdown-submenu">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $nom;?></a>
                                                    <ul class="dropdown-menu">
                                                        <?php
                                                        /** list submenu **/
                                                        foreach ($c as $k => $v):
                                                            if (verifie_droits($droits,$profil,$v)):
                                                                ?>
                                                                <li>
                                                                    <?php echo anchor($v, $k);?>
                                                                </li>
                                                                <?php
                                                            endif;
                                                        endforeach;
                                                        /** eof list submenu **/
                                                        ?>
                                                    </ul>
                                            </li>
                                    <?php
                                        }
                                    } ?>
                                </ul>
                            </li>
                        <?php }
                    }
                    else {
                        if (verifie_droits($droits,$profil,$m[0])) {?>
                            <li><?php echo anchor($m, $k, 'target="_blank"') ?></li>
                        <?php }
                    }
                }
                // menu extra
                if ( $profil != 'public') {?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                        aria-expanded="false">Extra<span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <?php include "application/views/pieces/extra.php";
                                    foreach ($extra as $e):
                                        if (verifie_droits_extra($droits,$profil,$e[1])):
                                            //if menu within submenu
                                            if(is_array($e[2])):
                                                ?>
                                                <li class="dropdown dropdown-submenu">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $e[0];?></a>
                                                    <ul class="dropdown-menu">
                                                        <?php
                                                        /** list submenu **/
                                                        foreach ($e[2] as $submenu):
                                                            if (verifie_droits_extra($droits,$profil,$submenu[1])):
                                                                if($submenu[1] != "#"):    
                                                            ?>
                                                                <li>
                                                                    <a href="<?php echo $submenu[1]?>" <?php echo $submenu[2]?'target="_blank"':''?> tabindex="0"><?php echo $submenu[0]?></a>
                                                                </li>
                                                            <?php
                                                                else:
                                                            ?>
                                                            <li class="dropdown dropdown-submenu pull-left">
                                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $submenu[0];?></a>           
                                                                <ul class="dropdown-menu">
                                                                <?php
                                                                        /** list child sub menu */
                                                                        foreach($submenu[2] as $childsubmenu):
                                                                            if (verifie_droits_extra($droits,$profil,$childsubmenu[1])):
                                                                    ?>
                                                                    <li>
                                                                        <a href="<?php echo $childsubmenu[1]?>" <?php echo $childsubmenu[2]?'target="_blank"':''?> tabindex="0"><?php echo $childsubmenu[0]?></a>
                                                                    </li>
                                                                    <?php
                                                                            endif;
                                                                        endforeach;              
                                                                    ?>
                                                                </ul>
                                                            </li>
                                                            <?php
                                                                endif;
                                                            endif;
                                                        endforeach;
                                                        /** eof list submenu **/
                                                        ?>

                                                    </ul>
                                                </li>
                                            <?php else: ?>
                                                <li>
                                                    <a href="<?php echo $e[1]?>" <?php echo $e[2]?'target="_blank"':''?>><?php echo $e[0]?></a>
                                                </li>
                                            <?php endif; ?>
                                            <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </ul>
                            </li>
                <?php }
                ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if (isset($profil)) {
                    $this->load->view('templates/toolbar',array('menus'=>$menus,'droits'=>$droits,'profil'=>$profil));
                }?>
                <li><p class="navbar-text"><?php echo pseudo()?></p></li>
                <?php foreach ($menus['droit'] as $k=>$m) {
                if (!isset($m)) {
                    $compte = 0;
                    foreach ($m as $c) {
                        if (verifie_droits($droits,$profil,$c)) {
                            $compte++;
                        }
                    }
                    if ($compte > 0) { ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false"><?php echo $k ?><span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <?php foreach ($m as $nom => $c) {
                                    if (verifie_droits($droits,$profil,$c)) {?>
                                        <li><?php echo anchor($c, $nom, 'target="_blank"') ?></li>
                                    <?php }
                                } ?>
                            </ul>
                        </li>
                    <?php }
                }
                else {
                    if (verifie_droits($droits,$profil,$m)) {?>
                        <li><?php echo anchor($m, $k, 'target="_blank"') ?></li>
                    <?php }
                }
            }?>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>