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
            <input type="file" name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control" accept="<?php echo $types?>">
        </div>
    </div>
</div>