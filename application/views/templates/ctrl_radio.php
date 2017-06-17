<?php
$label = html_escape($label);
$oblig = $label;
if ($obligatoire) {
    $oblig = '* '.$label;
}?>
<div class="row" style="margin-bottom: 15px">
    <div class="form-group">
        <label class="col-sm-3 control-label" for="<?php echo $controle?>"><?php echo $oblig?></label>
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
    </div>
</div>