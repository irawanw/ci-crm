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
<div class="row" style="margin-bottom: 15px">
    <div class="form-group">
        <label class="col-sm-3 control-label" for="<?php echo $controle?>"><?php echo $oblig?></label>
<?php
$valeur = html_escape($valeur);?>
        <div class="col-sm-9">
            <textarea name="<?php echo $controle?>" id="<?php echo $controle?>" class="form-control" rows="6" placeholder="<?php echo $placeholder?>"<?php echo $disabled ?>><?php echo $valeur?></textarea>
        </div>
    </div>
</div>