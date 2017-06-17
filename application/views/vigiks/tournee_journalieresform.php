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
<div id="type_data"><!---type_data---->

	        <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Nom de la tournee</label>
			<div class="col-sm-9">
			<select id="tr_nom" class="form-control" name="tr_nom" onchange="ajaxtournee();">
			<?php print_r($tournee); ?>			      
			</select>
			</div>
		    </div>
			</div>
			
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Numero de la tournee</label>
			<div class="col-sm-9">
			<input id="tr_numero" class="form-control" name="tr_numero" value="<?php echo $values->tr_numero;?>" placeholder="Numero de la tournee" type="text">
			</div>
		    </div>
			</div>
			 <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Nom du livreur</label>
			<div class="col-sm-9">
			<input id="tr_livreur" class="form-control" name="tr_livreur" value="<?php echo $values->tr_livreur;?>" placeholder="Nom du livreur" type="text">
			</div>
		    </div>
			</div>
			 </div><!---type_data end---->
			 <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Date</label>
			<div class="col-sm-9">
			<input id="tr_date" class="form-control" name="tr_date" value="<?php echo $values->tr_date;?>" placeholder="Date" type="date">
			</div>
			</div>
			</div>
	
			
<?php }
 else{
?>
  	<div id="type_data"><!---type_data---->
		 <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Nom de la tournee</label>
			<div class="col-sm-9">
			<select id="tr_nom" class="form-control" name="tr_nom" onchange="ajaxtournee();">
			<?php print_r($tournee); ?>			      
			</select>
			</div>
		    </div>
			</div>
			
		
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Numero de la tournee</label>
			<div class="col-sm-9">
			<input id="tr_numero" class="form-control" name="tr_numero" value="<?php echo $values->tr_numero;?>" placeholder="Numero de la tournee" type="text">
			</div>
		    </div>
			</div>
			 <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Nom du livreur</label>
			<div class="col-sm-9">
			<input id="tr_livreur" class="form-control" name="tr_livreur" value="<?php echo $values->tr_livreur;?>" placeholder="Nom du livreur" type="text">
			</div>
		    </div>
			</div>
		   		    
			 </div><!---type_data end---->
			 
			 <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Date</label>
			<div class="col-sm-9">
			<input id="tr_date" class="form-control" name="tr_date" value="<?php echo $values->tr_date;?>" placeholder="Date" type="date">
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


       

