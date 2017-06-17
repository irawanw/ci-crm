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
        <div class="col-sm-9">
            <?php if ($type == 'checkbox') {?>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="<?php echo $controle?>" value="1" <?php echo $checked?><?php echo $oblig_attr?>>
                    </label>
                </div>
            <?php }
            else {?>
                <label class="checkbox-inline">
                    <input type="checkbox" name="<?php echo $controle?>" value="1" <?php echo $checked?><?php echo $oblig_attr?>>
                </label>
            <?php }?>
        </div>
    </div>
</div>