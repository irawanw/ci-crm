<div class="row" style="margin-top: 20px">
    <div class="col-md-12 fiche">
        <?php if ($action == 'modif') {
            $cible = "$controleur/$methode/$id";
        }
        else {
            $cible = "$controleur/$methode";
        }
        if ($multipart) {
            echo form_open_multipart(current_url(),array('role'=>'form','class'=>'form-horizontal'),array('__form'=>'x'));
        }
        else {
            echo form_open(current_url(),array('role'=>'form','class'=>'form-horizontal'),array('__form'=>'x'));
        }
		
        //include 'application/views/templates/form_champs.php';
       ?>
	   <style>
	   .guide{
		   border-bottom:0px!important;
	   }
	   </style>

<div class="tab-content">
<?php if($id !="0"){
?>
	<div class="row guide">
		&nbsp;
			<div class="form-group">
			<label class="col-sm-3 control-label" >Numero</label>
			<div class="col-sm-9">
			<input id="ctc_numero" class="form-control" name="brn_numero" value="<?php echo $values->brn_numero;?>" placeholder="numero" type="text">
			</div>
		</div>
			</div>
		
		    <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Societe</label>
			<div class="col-sm-9">
			<select id="brn_societe" class="form-control" name="brn_societe">
			<?php print_r($soc_id); ?>		
			</select>
			</div>
			</div>
			</div>
	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Adresse</label>
			<div class="col-sm-9">
			
			<textarea id="brn_adresse" class="form-control" name="brn_adresse" rows="6" placeholder="Adresse"> <?php echo trim($values->brn_adresse);?></textarea>
			</div>
			</div>
			</div>
			
<?php }
 else{
?>
		<div class="row guide">
		&nbsp;
			<div class="form-group">
			<label class="col-sm-3 control-label" >Numero</label>
			<div class="col-sm-9">
			<input id="ctc_numero" class="form-control" name="brn_numero" value="<?php echo $values->brn_numero;?>" placeholder="numero" type="text">
			</div>
		</div>
			</div>
		
		    <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Societe</label>
			<div class="col-sm-9">
			<select id="brn_societe" class="form-control" name="brn_societe">
			<?php print_r($soc_id); ?>		
			</select>
			</div>
			</div>
			</div>
	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Adresse</label>
			<div class="col-sm-9">
			
			<textarea id="brn_adresse" class="form-control" name="brn_adresse" rows="6" placeholder="Adresse">    <?php echo trim($values->brn_adresse);?></textarea>
			</div>
			</div>
			</div>
			
 <?php } ?>	

            </div><!---tab end-->
        <br />
        <p class="text-center"><button type="submit" class="btn btn-primary"><?php echo $confirmation?></button>  <a href="<?php echo  base_url()."index.php/".$controleur;?>" class="btn btn-default">Retour</a></p>
        <?php echo form_close()?>
    </div>
</div>
