<?php
$rang = 0;
$popup = array();
if($this->input->is_ajax_request()){
    $retour = "javascript:$('.template-modal').modal('toggle')";
} else {
    //$retour = "javascript:history.back();";
    $retour = site_url($this->uri->segment(1));
    
}
if (!empty($cmd_globales)) {
?>
<p>
    <?php foreach ($cmd_globales as $c) {
        $cible = $c[1]."/$id";
        if ($cible == $this->uri->uri_string()) {
            $cmd = anchor("#",$c[0],'class="btn btn-'.$c[2].' btn-sm" role="button" disabled="disabled"').'&nbsp;';
        }
        else {
            $cmd = anchor($cible,$c[0],'class="btn btn-'.$c[2].' btn-sm" role="button"').'&nbsp;';
        }
        if (!isset($c[3]) || !empty($c[3])) {
            echo $cmd;
        }
    } echo "</p>\n";
}
?>
<div class="row">
    <div class="col-md-12 fiche">
<?php
// mise en forme des champs
$champs = array();
foreach($descripteur['champs'] as $k=>$c) {
    if (substr($k,0,1) == '_') continue;
    switch($c[1]) {
        case 'TELEPHONE':
            if (isset($id_parent)) {
                $valeur = construit_lien_tel($values->$id_parent,$values->$c[3]);
            }
            else {
                $valeur = $values->$c[3];
            }
            break;
        case 'EMAIL':
            if (isset($id_parent)) {
                $valeur = construit_lien_mail($values->$id_parent,$values->$c[3]);
            }
            else {
                $valeur = $values->$c[3];
            }
            break;
        case 'BOOL':
            $valeur = formatte_booleen($values->$c[3]);
            break;
        case 'URL':
            $valeur = formatte_url($values->$c[3]);
            break;
        case 'FICHIER':
            if (count($c) == 5) {
                $chemin = $values->$c[4];
            }
            else {
                $chemin = '';
            }
            $valeur = construit_lien_fichier($chemin,$values->$c[3]);
            break;
        default:
            if (/*($c[1] == 'REF' OR $c[1] == 'REF_INV') AND*/ $c[2] == 'ref') {
                //$champ = $c->valeurs->label;
                /*$table = $c->valeurs->table;
                if (substr($table,0,1) == 'v') {
                    $texte = '$values->'.$champ;
                }
                else {*/
                    $valeur = construit_lien_detail($c[3][0],$values->$c[3][1],$values->$c[3][2]);
                //}
            }
            else {
                switch($c[2]) {
                    case "date":
                        $valeur = formatte_date($values->$c[3]);
                        break;
                    case "datetime":
                        $valeur = formatte_dateheure($values->$c[3]);
                        break;
                    case "number":
                        $valeur = formatte_decimal($values->$c[3]);
                        break;
                    case "textarea":
                        $valeur = formatte_texte_long($values->$c[3]);
                        break;
                    default:
                        $valeur = $values->$c[3];
                }
            }
    }
    $champs[$k] = array($c[0], $valeur);
}
if (count($descripteur['onglets']) > 0) {?>
            <ul class="nav nav-tabs" role="tablist">
<?php $actif = ' class="active"';
    $tab = 1;
    foreach ($descripteur['onglets'] as $o) {
        $libelle = $o[0];
        $cle = 'tab-'.$tab++;
        ?>
                <li role="presentation"<?php echo $actif?>><a href="#<?php echo $cle?>" aria-controls="<?php echo $cle?>" role="tab" data-toggle="tab"><?php echo $libelle?></a></li>
<?php $actif = '';
    }?>
            <li><a href="<?php echo $retour; ?>"><span class="glyphicon glyphicon-menu-left"></span> retour à la liste</a></li>
            </ul>

            <div class="tab-content">
<?php $actif = ' active';
    $tab = 1;
    foreach ($descripteur['onglets'] as $o) {
        $cle = 'tab-'.$tab++;?>
                <div role="tabpanel" class="tab-pane <?php echo $actif?>" id="<?php echo $cle?>">
<?php   $actif = '';
        foreach($o[1] as $c) {
            if (substr($c,0,1) == '_') continue;
            $champ = $champs[$c]?>
                    <div class="row guide">
                        <div class="col-sm-4">
                            <p><?php echo $champ[0]?> :</p>
                        </div>
                        <div class="col-sm-8">
                            <p><?php echo $champ[1]?>&nbsp;</p>
                        </div>
                    </div>
<?php   }?>
                </div>
<?php }?>
            </div>
<?php }
else {
    echo '<ul class="nav nav-tabs" role="tablist">';
    echo    '<li><a href="'.$retour.'"><span class="glyphicon glyphicon-menu-left"></span> retour à la liste</a></li>';
    echo '</ul>';
    foreach($champs as $c) {?>
        <div class="row guide">
            <div class="col-sm-4">
                <p><?php echo $c[0]?> :</p>
            </div>
            <div class="col-sm-8">
                <p><?php echo $c[1]?>&nbsp;</p>
            </div>
        </div>
<?php }
}?>
<?php $cible = "$controleur/$methode/$id";
    echo form_open($cible,array('role'=>'form'));
    $rang = 0;
?>
    <input type="hidden" name="dummy" value="0">
    <p>&nbsp;</p>
    <p class="text-center">
<?php foreach ($cmd_locales as $c) {
    $button_id = ltrim(str_replace('/', '-', $c[1]), '*');
    if ($c[2] == 'danger') {
        $ancre = anchor($c[1]."/$id",$c[0],'class="btn btn-danger btn-confirm-action" role="button"');
        $popup[$rang] = <<<EOD
    <div class="modal fade" id="popup-$controleur-$methode-$rang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-$controleur-$methode-$rang" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel-$controleur-$methode-$rang">Confirmation d'opération</h4>
                </div>
                <div class="modal-body">
                    Veuillez confirmer l'opération.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    $ancre
                </div>
            </div>
        </div>
    </div>

EOD;
        $cmd = '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#popup-'.$controleur.'-'.$methode.'-'.$rang.'">'.$c[0].'</button>'."&nbsp;\n";
        if (isset($c[3])) {
            if ($c[3]) echo $cmd;
        }
        else {
            echo $cmd;
        }
        $rang++;
    }
    else {
        $target = '';
        if (substr($c[1],0,1) == '*') {
            $target = ' target="_blank"';
            $c[1] = substr($c[1],1);
        }
        $cmd = anchor($c[1]."/$id",$c[0],'class="btn btn-'.$c[2].'" role="button"'.$target).'&nbsp;';
        if (isset($c[3])) {
            if ($c[3]) echo $cmd;
        }
        else {
            echo $cmd;
        }
    }
}?>
        <!-- button type="submit" class="btn btn-default">Retour</button -->
    </p>
<?php echo form_close()?>
<?php foreach ($popup as $p) {
    echo $p;
}?>
    </div>
</div>