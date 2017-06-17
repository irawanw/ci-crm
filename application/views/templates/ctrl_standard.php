<?php
$label = html_escape($label);
if (!empty($attr['placeholder']) && strlen(trim($attr['placeholder'])) > 0) {
    $placeholder = html_escape($attr['placeholder']);
} else {
    $placeholder = $label;
    unset($attr['placeholder']);
}
$disabled = (!empty($attr['disabled'])) ? ' disabled' : '';
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
$valeur = html_escape($valeur);
if ($type == 'number') {
    $step = ' step="any"';
}
else {
    $step = '';
}?>
        <div class="col-sm-9">
            <input type="<?php echo $type?>" name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control" value="<?php echo $valeur?>" placeholder="<?php echo $placeholder?>"<?php echo $step.$oblig_attr.$disabled ?>>
        </div>
    </div>
</div>