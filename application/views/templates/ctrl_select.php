<?php
$label = html_escape($label);
$oblig = $label;
$oblig_attr = '';
if ($obligatoire) {
    $oblig = '* '.$label;
    $oblig_attr = ' required="required"';
}?>
<div class="row guide">
    <div class="form-group">
        <label class="col-sm-3 control-label" for="<?php echo $controle?>"><?php echo $oblig?></label>
<?php
$firstId = isset($valeurs[0]) ? $valeurs[0]->$id : ""; 
$firstValue =  isset($valeurs[0]) ? $valeurs[0]->$value : "";
if (! $obligatoire) {
    if($firstId != "ajouter") {
        $void = new stdClass();
        $void->$id = 0;
        $void->$value = '(choisissez)';
        array_unshift($valeurs,$void);
    }
}
?>
        <div class="col-sm-9">
            <?php
            if($firstId === "ajouter"):
                $labelAjouter = $firstValue;
                unset($valeurs[0]);    
                $selectedValues = explode(",", $valeur);
            ?>
                <div class="input-group">
                    <span class="input-group-btn">
                        <a class="btn btn-default ajouters" data-id="<?php echo $controle?>" >
                            <?php echo $labelAjouter;?>        
                        </a>
                    </span>
                    <select name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control">
                        <?php foreach ($valeurs as $lv) {
                            $selected = ($valeur == $lv->$id)?" selected=\"selected\"":""?>
                            <option value="<?php echo $lv->$id?>"<?php echo $selected?>><?php echo html_escape($lv->$value)?></option>
                        <?php }?>
                    </select>
                </div>
            <?php else: ?>
                <select name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control">
                    <?php foreach ($valeurs as $lv) {
                        $selected = ($valeur == $lv->$id)?" selected=\"selected\"":""?>
                        <option value="<?php echo $lv->$id?>"<?php echo $selected?>><?php echo html_escape($lv->$value)?></option>
                    <?php }?>
                </select>
            <?php endif; ?>
        </div>
    </div>
</div>