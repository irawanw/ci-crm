 <?php
 if($client!=""){
	 ?>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Dernière facture</label>
			<div class="col-sm-9">
			<input id="adr_derniere" class="form-control" name="adr_derniere" value="<?php echo $derniere;?>"  type="text">
			</div>
		    </div>
			</div>	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Dernière facture impayée</label>
			<div class="col-sm-9">
			<input id="adr_impayee" class="form-control" name="adr_impayee" value="<?php echo $impayee;?>" " type="text">
			</div>
		    </div>
			</div>	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Avant dernière facture impayée</label>
			<div class="col-sm-9">
			<input id="adr_avant" class="form-control" name="adr_avant" value="<?php echo $avant;?>"  type="text">
			</div>
		    </div>
			</div>	
	<?php } ?>		
			<!--<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Adresse</label>
			<div class="col-sm-9">
			
			<textarea id="ctc_adresse" class="form-control" name="ctc_adresse" rows="6" placeholder="Adresse"><?php  echo trim($cl_address); ?></textarea>
			</div>
			</div>
			</div>-->
 