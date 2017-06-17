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
			<label class="col-sm-3 control-label" >NUMERO DE LA TOURNEE</label>
			<div class="col-sm-9">
			<input id="trn_numero" class="form-control" name="trn_numero" value="<?php echo $values->trn_numero;?>" placeholder="numero de la tournee" type="text">
			</div>
		    </div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >NOM DE LA TOURNEE</label>
			<div class="col-sm-9">
			<input id="trn_nom" class="form-control" name="trn_nom" value="<?php echo $values->trn_nom;?>" placeholder="nom de la tournee" type="text">
			</div>
			</div>
			</div>
		
		    <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >LIVREUR</label>
			<div class="col-sm-9">
			<select id="trn_livreur" class="form-control" name="trn_livreur">
			<?php print_r($lvr_id); ?>		
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >REMARQUES</label>
			<div class="col-sm-9">
			<input id="trn_remarques" class="form-control" name="trn_remarques" value="<?php echo $values->trn_remarques;?>" placeholder="Remarques" type="text">	
			</select>
			</div>
			</div>
			</div>
			
<?php }
 else{
?>
		<div class="row guide">
		&nbsp;
			<div class="form-group">
			<label class="col-sm-3 control-label" >NUMERO DE LA TOURNEE</label>
			<div class="col-sm-9">
			<input id="trn_numero" class="form-control" name="trn_numero" value="<?php echo $values->trn_numero;?>" placeholder="numero de la tournee" type="text">
			</div>
		    </div>
			</div>
		   <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >NOM DE LA TOURNEE</label>
			<div class="col-sm-9">
			<input id="trn_nom" class="form-control" name="trn_nom" value="<?php echo $values->trn_nom;?>" placeholder="nom de la tournee" type="text">
			</div>
			</div>
			</div>
		    <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >LIVREUR</label>
			<div class="col-sm-9">
			<select id="trn_livreur" class="form-control" name="trn_livreur">
			<?php print_r($lvr_id); ?>		
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >REMARQUES</label>
			<div class="col-sm-9">
			<input id="trn_remarques" class="form-control" name="trn_remarques" value="<?php echo $values->trn_remarques;?>" placeholder="Remarques" type="text">	
			</select>
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
