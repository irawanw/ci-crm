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
$selectedValues = isset($selectedValues) ? $selectedValues : array();
if (! $obligatoire) {
    $void = new stdClass();
    $void->$id = 0;
    
}?>
        <div class="col-sm-9">
            <?php 
            if($firstId === "ajouter"):
                $labelAjouter = $firstValue;
                unset($valeurs[0]);    
            ?>
                <div class="input-group">
                    <span class="input-group-btn">
                        <a class="btn btn-default ajouters" data-id="<?php echo $controle?>" >
                            <?php echo $labelAjouter;?>        
                        </a>
                    </span>
                    <select name="<?php echo $controle?>[]" id="<?php echo $controle?>" class="form-control" multiple="multiple">
                        <?php foreach ($valeurs as $lv):
                            $selected = (in_array($lv->$id, $selectedValues)) ? " selected=\"selected\"":""
                        ?>
                            <option <?php echo $selected;?> value="<?php echo $lv->$id?>"><?php echo html_escape($lv->$value)?></option>
                        <?php endforeach?>
                    </select>
                </div>
            <?php else: ?>
                <select name="<?php echo $controle?>[]" id="<?php echo $controle?>" class="form-control" multiple="multiple">
                        <?php 
                        foreach ($valeurs as $lv):
                            $selected = (in_array($lv->$id, $selectedValues)) ? " selected=\"selected\"":""
                        ?>
                            <option <?php echo $selected;?> value="<?php echo $lv->$id?>"><?php echo html_escape($lv->$value)?></option>
                        <?php endforeach?>
                </select>
            <?php endif; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    
    $(document).ready(function() {
		var dataSelect = "<?php echo $valeur?>";
        $('#template-modal-detail').on('shown.bs.modal',function(e) {
            $('<?php echo '#'.$controle?>').multiselect({
                includeSelectAllOption: false,
                enableFiltering: true,
                numberDisplayed: 1,
				maxHeight: 300,
                enableCaseInsensitiveFiltering: true,
            });
            $('<?php echo '#'.$controle?>').multiselect('rebuild');
        });
		if(dataSelect !== 0)
        {
			var res = dataSelect.split(",");
			if(res.length > 1)
			{
				res.forEach(function(elm){
					$('<?php echo '#'.$controle;?>').multiselect('select', [elm]);
				});
			}
			else
			{
				$('<?php echo '#'.$controle;?>').multiselect('select', [res]); 
			}
        }
	});
</script>