 <?php
 if($client!=""){
	 ?>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Facture</label>
			<div class="col-sm-9">
			
			<select id="ctc_facture" class="form-control" name="ctc_facture">
			<?php print_r($facture); ?>
			</select>
			
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Devis</label>
			<div class="col-sm-9">
			<select id="ctc_devis" class="form-control" name="ctc_devis">
			<?php print_r($devis); ?>	
			</select>
			</div>
			</div>
			</div>
<?php if($adresse!="create"){ ?>			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Adresses de livraison</label>
			<div class="col-sm-9">
			<select id="ctc_adresse" class="form-control" name="ctc_adresse">
			<?php print_r($adresse); ?>	
			</select>
			</div>
			</div>
			</div>
<?php } else{
?>
            <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Adresses de livraison</label>
			<div class="col-sm-9">
			<input type="hidden" name="ctc_adresse" value="aucun" id="ctc_adresse">
			<a href="<?php echo site_url().'/newadresse/create';?>" target="_blank">Creer une Adresses de livraison</a>
			</div>
			</div>
			</div>
<?php	
}
?>
	<?php } ?>		
			<!--<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Adresse</label>
			<div class="col-sm-9">
			
			<textarea id="ctc_adresse" class="form-control" name="ctc_adresse" rows="6" placeholder="Adresse"><?php  echo trim($cl_address); ?></textarea>
			</div>
			</div>
			</div>-->
 