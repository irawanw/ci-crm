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
			<input id="ctc_numero" class="form-control" name="ctc_numero" value="<?php echo $values->ctc_numero;?>" placeholder="numero" type="text">
			</div>
		</div>
			</div>
		<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Borne</label>
			<div class="col-sm-9">
			<select id="ctc_borne" class="form-control" name="ctc_borne">
			<?php print_r($born_id); ?>		
			</select>
			</div>
		</div>
		</div>
		    <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Societe</label>
			<div class="col-sm-9">
			<select id="ctc_societe" class="form-control" name="ctc_societe">
			<?php print_r($soc_id); ?>		
			</select>
			</div>
			</div>
			</div>
		<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Type</label>
			<div class="col-sm-9">
			<select id="ctc_type" class="form-control" name="ctc_type">
			<option value="">sélectionner</option>
			<option value="client" <?php if($values->ctc_type=="client") { echo "selected";}?>>CLIENT</option>
			<option value="distributeur" <?php if($values->ctc_type=="distributeur") { echo "selected";}?>>DISTRIBUTEUR</option>
			<option value="non_affecte" <?php if($values->ctc_type=="non_affecte") { echo "selected";}?>>NON AFFECTE</option>
			<option value="autre" <?php if($values->ctc_type=="autre") { echo "selected";}?>>AUTRE</option>
			</select>
			</div>
		    </div>
            </div>   
			<div class="row guide" id="type_selected">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Client
			</label>
			<div class="col-sm-9">
			<select id="ctc_client" class="form-control" name="ctc_client" onchange="ajaxclient();">
			<?php print_r($client_name); ?>			      
			</select>
			</div>
			</div>
			</div> 
			<div id="type_data"><!---type_data---->
            <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Facture</label>
			<div class="col-sm-9">
			<select id="ctc_facture" class="form-control" name="ctc_facture" onchange="ajaxclient();">
			<?php print_r($facture_name); ?>		      
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Devis</label>
			<div class="col-sm-9">
			<select id="ctc_devis" class="form-control" name="ctc_devis">			
			<?php print_r($devis_name); ?>	
			</select>
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Adresse de livraison</label>
			<div class="col-sm-9">
			<select id="ctc_adresse" class="form-control" name="ctc_adresse">
			<?php print_r($adresse_id); ?>		
			</select>
			</div>
			</div>
			</div>
			</div>
			<!---type_data end---->
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Etat</label>
			<div class="col-sm-9">
			<select id="ctc_etat" class="form-control" name="ctc_etat">
			<option value="">sélectionner</option>
			<option value="en cours" <?php if($values->ctc_etat=="en cours") { echo "selected";}?>>en cours</option>
			<option value="perdu et supprimé" <?php if($values->ctc_etat=="perdu et supprimé") { echo "selected";}?>>perdu et supprimé</option>
			<option value="perdu non supprimé" <?php if($values->ctc_etat=="perdu non supprimé") { echo "selected";}?>>perdu non supprimé</option>		
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Tournee</label>
			<div class="col-sm-9">
			<select id="ctc_tournee" class="form-control" name="ctc_tournee">
			<?php print_r($tournee_id); ?>		
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Nbre d'ouvertures</label>
			<div class="col-sm-9">
			<input id="ctc_ouvertures" class="form-control" name="ctc_ouvertures" value="<?php echo $values->ctc_ouvertures;?>" placeholder="Nbre d'ouvertures" type="text">
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Nbre de chargements</label>
			<div class="col-sm-9">
			<input id="ctc_chargements" class="form-control" name="ctc_chargements" value="<?php echo $values->ctc_chargements;?>" placeholder="Nbre de chargements" type="text">
			</div>
			</div>
			</div>
				
<?php
}
else{ ?>
		<div class="row guide">
		&nbsp;
			<div class="form-group">
			<label class="col-sm-3 control-label" >Numero</label>
			<div class="col-sm-9">
			<input id="ctc_numero" class="form-control" name="ctc_numero" value="<?php echo $values->ctc_numero;?>" placeholder="numero" type="text">
			</div>
		</div>
			</div>
		<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Borne</label>
			<div class="col-sm-9">
			<select id="ctc_borne" class="form-control" name="ctc_borne">
			<?php print_r($born_id); ?>		
			</select>
			</div>
		</div>
		</div>
		<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Societe</label>
			<div class="col-sm-9">
			<select id="ctc_societe" class="form-control" name="ctc_societe">
			<?php print_r($soc_id); ?>		
			</select>
			</div>
			</div>
			</div>
		<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Type</label>
			<div class="col-sm-9">
			<select id="ctc_type" class="form-control" name="ctc_type">
			<option value="">sélectionner</option>
			<option value="client" <?php if($values->ctc_type=="client") { echo "selected";}?>>CLIENT</option>
			<option value="distributeur" <?php if($values->ctc_type=="distributeur") { echo "selected";}?>>DISTRIBUTEUR</option>
			<option value="non_affecte" <?php if($values->ctc_type=="non_affecte") { echo "selected";}?>>NON AFFECTE</option>
			<option value="autre" <?php if($values->ctc_type=="autre") { echo "selected";}?>>AUTRE</option>
			</select>
			</div>
		    </div>
            </div>   
			<div class="row guide" id="type_selected">
			</div> 
			<div id="type_data"><!---type_data---->
           <!-- <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Facture</label>
			<div class="col-sm-9">
			<select id="ctc_facture" class="form-control" name="ctc_facture" onchange="ajaxclient();">
			<option value=''>sélectionner</option>		      
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Devis</label>
			<div class="col-sm-9">
			<select id="ctc_devis" class="form-control" name="ctc_devis">
			<option value=''>sélectionner</option>
			</select>
			</div>
			</div>
			</div>-->
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Adresse de livraison</label>
			<div class="col-sm-9">
			<select id="ctc_adresse" class="form-control" name="ctc_adresse">
			<?php print_r($adresse_id); ?>		
			</select>
			</div>
			</div>
			</div>
			</div><!---type_data end---->
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Etat</label>
			<div class="col-sm-9">
			<select id="ctc_etat" class="form-control" name="ctc_etat">
			<option value="">sélectionner</option>
			<option value="en cours" <?php if($values->ctc_etat=="en cours") { echo "selected";}?>>en cours</option>
			<option value="perdu et supprime" <?php if($values->ctc_etat=="perdu et supprime") { echo "selected";}?>>perdu et supprime</option>
			<option value="perdu non supprime" <?php if($values->ctc_etat=="perdu non supprime") { echo "selected";}?>>perdu non supprime</option>		
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Tournee</label>
			<div class="col-sm-9">
			<select id="ctc_tournee" class="form-control" name="ctc_tournee">
			<?php print_r($tournee_id); ?>		
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Nbre d'ouvertures</label>
			<div class="col-sm-9">
			<input id="ctc_ouvertures" class="form-control" name="ctc_ouvertures" value="<?php echo $values->ctc_ouvertures;?>" placeholder="Nbre d'ouvertures" type="text">
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Nbre de chargements</label>
			<div class="col-sm-9">
			<input id="ctc_chargements" class="form-control" name="ctc_chargements" value="<?php echo $values->ctc_chargements;?>" placeholder="Nbre de chargements" type="text">
			</div>
			</div>
			</div>
			
<?php } ?>
            </div><!---tab end-->
        <br />
        <p class="text-center"><button type="submit" class="btn btn-primary"><?php echo $confirmation?></button> <a href="<?php echo  base_url()."index.php/".$controleur;?>" class="btn btn-default">Retour</a></p>
		<p class="text-center">

</p>
        <?php echo form_close()?>
    </div>
</div>
