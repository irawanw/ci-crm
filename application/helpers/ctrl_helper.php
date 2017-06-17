<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 31/03/15
 * Time: 16:35
 */

/******************************
 * Renvoie l'entete
 ******************************/
if ( ! function_exists('ctrl_entete')) {
    function ctrl_entete($controle,$label,$obligatoire=false) {
        if ($obligatoire) {
            $oblig = '* ';
        }
        else {
            $oblig = '';
        }?>
    <div class="row" style="margin-bottom: 15px">
        <div class="form-group">
            <label class="col-sm-3 control-label" for="<?php echo $controle?>"><?php echo $oblig.$label?></label>
    <?php }
}

/******************************
 * Renvoie le pied
 ******************************/
if ( ! function_exists('ctrl_pied')) {
    function ctrl_pied() {?>
        </div>
    </div>
    <?php }
}

/******************************
 * Renvoie un contrôle standard
 ******************************/
if ( ! function_exists('ctrl_standard')) {
    function ctrl_standard($type,$controle,$label,$valeur,$obligatoire=false) {
        $label = html_escape($label);
        $valeur = html_escape($valeur);
        if ($type == 'number') {
            $step = ' step="any"';
        }
        else {
            $step = '';
        }
        ctrl_entete($controle,$label,$obligatoire);?>
        <div class="col-sm-9">
            <input type="<?php echo $type?>" name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control" value="<?php echo html_escape($valeur)?>" placeholder="<?php echo $label?>"<?php echo $step?>>
        </div>
<?php ctrl_pied();
    }
}

/******************************
 * Renvoie un contrôle standard avec largeur fixée
 ******************************/
if ( ! function_exists('ctrl_standard_l')) {
    function ctrl_standard_l($type,$controle,$label,$valeur,$largeur,$obligatoire=false) {
        $label = html_escape($label);
        $valeur = html_escape($valeur);
        if ($type == 'number') {
            $step = ' step="any"';
        }
        else {
            $step = '';
        }
        ctrl_entete($controle,$label,$obligatoire);?>
        <div class="col-sm-9">
            <input type="<?php echo $type?>" name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control" style="width:<?php echo $largeur?>px" value="<?php echo html_escape($valeur)?>" placeholder="<?php echo $label?>"<?php echo $step?>>
        </div>
        <?php ctrl_pied();
    }
}

/******************************
 * Renvoie un contrôle date
 ******************************/
if ( ! function_exists('ctrl_date')) {
    function ctrl_date($type,$controle,$label,$valeur,$obligatoire=false) {
        $label = html_escape($label);
        $valeur = html_escape($valeur);
        if (strlen($valeur) > 10) {
            $valeur = substr($valeur,0,10);
        }
        ctrl_entete($controle,$label,$obligatoire);?>
        <div class="col-sm-9">
            <input type="text" name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control" value="<?php echo $valeur?>" placeholder="<?php echo $label?>">
        </div>
        <?php ctrl_pied();
    }
}

/******************************
 * Renvoie un contrôle textarea
 ******************************/
if ( ! function_exists('ctrl_textarea')) {
    function ctrl_textarea($controle,$label,$valeur,$obligatoire=false) {
        $label = html_escape($label);
        $valeur = html_escape($valeur);
        ctrl_entete($controle,$label,$obligatoire);?>
        <div class="col-sm-9">
            <textarea name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control" rows="6" placeholder="<?php echo $label?>"><?php echo $valeur?></textarea>
        </div>
<?php ctrl_pied();
    }
}

/******************************
 * Renvoie un contrôle checkbox
 ******************************/
if ( ! function_exists('ctrl_checkbox')) {
    function ctrl_checkbox($type,$controle,$label,$checked,$obligatoire=false) {
        $label = html_escape($label);
        ctrl_entete($controle,$label,$obligatoire);?>
        <div class="col-sm-9">
<?php if ($type == 'checkbox') {?>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="<?php echo $controle?>" value="1" <?php echo $checked?>>
                </label>
            </div>
<?php }
else {?>
            <label class="checkbox-inline">
                <input type="checkbox" name="<?php echo $controle?>" value="1" <?php echo $checked?>>
            </label>
<?php }?>
        </div>
<?php ctrl_pied();
    }
}

/******************************
 * Renvoie un groupe de contrôles checkbox horizontaux
 ******************************/
if ( ! function_exists('ctrl_checkbox_g')) {
    function ctrl_checkbox_g($controle,$label,$labels,$checked,$obligatoire=false) {
        $label = html_escape($label);
        ctrl_entete($controle,$label,$obligatoire);?>
        <div class="col-sm-9">
            <?php foreach($labels as $i=>$l) {?>
            <label class="checkbox-inline">
                <input type="checkbox" name="<?php echo $controle?>[]" value="<?php echo $l?>" <?php echo $checked[$i]?>><?php echo $l?>
            </label>
            <?php }?>
        </div>
        <?php ctrl_pied();
    }
}

/******************************
 * Renvoie un contrôle radio
 ******************************/
if ( ! function_exists('ctrl_radio')) {
    function ctrl_radio($type,$controle,$label,$valeurs,$valeur,$id,$value,$obligatoire=false) {
        $label = html_escape($label);
        $valeur = html_escape($valeur);
        ctrl_entete($controle,$label,$obligatoire);?>
        <div class="col-sm-9">
<?php if ($type == 'radio') {
            foreach ($valeurs as $lv) {
                $checked = ($valeur == $lv->$id)?" checked=\"checked\"":""?>
            <div class="radio">
                <label>
                    <input type="radio" name="<?php echo $controle?>" value="<?php echo $lv->$id?>"<?php echo $checked?>>
                    <?php echo html_escape($lv->$value)?>
                </label>
            </div>
<?php }
}
else {
            foreach ($valeurs as $lv) {
            $checked = ($valeur == $lv->$id)?" checked=\"checked\"":""?>
            <label class="radio-inline">
                <input type="radio" name="<?php echo $controle?>" value="<?php echo $lv->$id?>"<?php echo $checked?>>
                <?php echo html_escape($lv->$value)?>
            </label>
<?php }
}?>
        </div>
<?php ctrl_pied();
    }
}

/******************************
 * Renvoie un contrôle select
 ******************************/
if ( ! function_exists('ctrl_select')) {
    function ctrl_select($controle,$label,$valeurs,$valeur,$id,$value,$obligatoire=false) {
        $label = html_escape($label);
        $valeur = html_escape($valeur);
        ctrl_entete($controle,$label,$obligatoire);
        if (! $obligatoire) {
            $void = new stdClass();
            $void->$id = 0;
            $void->$value = '(choisissez)';
            array_unshift($valeurs,$void);
        }?>
        <div class="col-sm-9">
            <select name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control">
<?php foreach ($valeurs as $lv) {
            $selected = ($valeur == $lv->$id)?" selected=\"selected\"":""?>
                 <option value="<?php echo $lv->$id?>"<?php echo $selected?>><?php echo html_escape($lv->$value)?></option>
<?php }?>
            </select>
        </div>
<?php ctrl_pied();
    }
}

/******************************
 * Renvoie un contrôle select avec un champ data
 ******************************/
if ( ! function_exists('ctrl_select_data')) {
    function ctrl_select_data($controle,$label,$valeurs,$valeur,$id,$value,$data,$obligatoire=false) {
        $label = html_escape($label);
        $valeur = html_escape($valeur);
        ctrl_entete($controle,$label,$obligatoire);
        if (! $obligatoire) {
            $void = new stdClass();
            $void->$id = 0;
            $void->$value = '(choisissez)';
            array_unshift($valeurs,$void);
        }?>
        <div class="col-sm-9">
            <select name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control">
                <?php foreach ($valeurs as $lv) {
                    $selected = ($valeur == $lv->$id)?" selected=\"selected\"":""?>
                    <option data-compl="<?php echo $lv->$data?>" value="<?php echo $lv->$id?>"<?php echo $selected?>><?php echo html_escape($lv->$value)?></option>
                <?php }?>
            </select>
        </div>
        <?php ctrl_pied();
    }
}

/******************************
 * Renvoie un contrôle select "fils
 ******************************/
if ( ! function_exists('ctrl_select_fils')) {
    function ctrl_select_fils($controle,$label,$valeurs,$valeur,$id,$value,$pere,$obligatoire=false) {
        $label = html_escape($label);
        $valeur = html_escape($valeur);
        ctrl_entete($controle,$label,$obligatoire);
        if (! $obligatoire) {
            $void = new stdClass();
            $void->$id = 0;
            $void->$value = '(choisissez)';
            array_unshift($valeurs,$void);
        }?>
        <div class="col-sm-9">
            <select name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control">
<?php foreach ($valeurs as $lv) {
            $selected = ($valeur == $lv->$id)?" selected=\"selected\"":""?>
                <option data-pere="<?php echo $lv->$pere?>" value="<?php echo $lv->$id?>"<?php echo $selected?>><?php echo html_escape($lv->$value)?></option>
<?php }?>
            </select>
        </div>
        <?php ctrl_pied();
    }
}

/******************************
 * Renvoie un contrôle upload
 ******************************/
if ( ! function_exists('ctrl_upload')) {
    function ctrl_upload($controle,$label,$types,$obligatoire=false) {
        $label = html_escape($label);
        ctrl_entete($controle,$label,$obligatoire);?>
        <div class="col-sm-9">
            <input type="file" name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control" accept="<?php echo $types?>">
        </div>
        <?php ctrl_pied();
    }
}