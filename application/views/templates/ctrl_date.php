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
if ($obligatoire) {
    $oblig = '* '.$label;
}?>
<div class="row guide">
    <div class="form-group">
        <label class="col-sm-3 control-label" for="<?php echo $controle?>"><?php echo $oblig?></label>
<?php
$valeur = html_escape($valeur);
$valeur = formatte_date($valeur);
?>
        <div class="col-sm-9">
            <input type="text" name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control form-date-field" value="<?php echo $valeur?>" placeholder="<?php echo $placeholder?>"<?php echo $disabled ?>>
        </div>
    </div>
</div>