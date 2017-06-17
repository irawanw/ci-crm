 <?php
 if($tournee!="" && $tournee_journid =='0'){
	 ?>			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Nom de la tournee</label>
			<div class="col-sm-9">
			<select id="tr_nom" class="form-control" name="tr_nom" onchange="ajaxtournee();">
			<?php print_r($tournee_nom); ?>			      
			</select>
			</div>
		    </div>
			</div>
			
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Numero de la tournee</label>
			<div class="col-sm-9">
			<input id="tr_numero" class="form-control" name="tr_numero" value="<?php echo $tournee_numero;?>" placeholder="Numero de la tournee" type="text">
			</div>
		    </div>
			</div>
			 <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Nom du livreur</label>
			<div class="col-sm-9">
			<input id="tr_livreur" class="form-control" name="tr_livreur" value="<?php echo $tournee_livreur;?>" placeholder="Nom du livreur" type="text">
			</div>
		    </div>
			</div>	
			
		    
			 <input id="tr_existing_id" class="form-control" name="tr_existing_id" value="0" placeholder="Nom du livreur" type="hidden">
			
 <?php }
 if($tournee!="" && $tournee_journid!='0'){
 ?>
 <input id="tr_existing_id" class="form-control" name="tr_existing_id" value="<?php echo $tournee_journid;?>" placeholder="Nom du livreur" type="hidden">
            <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Nom de la tournee(Already exist)</label>
			<div class="col-sm-9">
			<select id="tr_nom" class="form-control" name="tr_nom" onchange="ajaxtournee();">
			<?php print_r($tournee_nom); ?>			      
			</select>
			</div>
		    </div>
			</div>
			
            
 <?php } ?>
 