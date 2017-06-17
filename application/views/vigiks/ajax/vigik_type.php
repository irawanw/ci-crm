   <?php if($type=="client"){ ?>
			<div class="form-group">
			<label class="col-sm-3 control-label" >Client</label>
			<div class="col-sm-9">
			<select id="ctc_client" class="form-control" name="ctc_client" onchange="ajaxclient();">
			<?php print_r($id); ?>			      
			</select>
			</div>
			</div>
   <?php } ?>
			