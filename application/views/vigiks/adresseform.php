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
			<label class="col-sm-3 control-label">Numero de la Rue</label>
			<div class="col-sm-9">
			<input id="adr_numero" class="form-control" name="adr_numero" value="<?php echo $values->adr_numero;?>" placeholder="Numero de la Rue" type="text">
			</div>
		    </div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Rue</label>
			<div class="col-sm-9">
			<input id="adr_numero" class="form-control" name="adr_rue" value="<?php echo $values->adr_rue;?>" placeholder="Rue" type="text">
			</div>
		    </div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Type de voie</label>
			<div class="col-sm-9">
			<select id="adr_voie" class="form-control" name="adr_voie">
			<option value="">sélectionner</option>
			<option value="rue" <?php if($values->adr_voie=="rue") { echo "selected";}?>>rue</option>
			<option value="avenue" <?php if($values->adr_voie=="avenue") { echo "selected";}?>>avenue</option>
			<option value="boulevard" <?php if($values->adr_voie=="boulevard") { echo "selected";}?>>boulevard</option>
			<option value="place" <?php if($values->adr_voie=="place") { echo "selected";}?>>place</option>
			</select>
			</div>
		    </div>
			</div>
			<div class="row guide">
		    <div class="form-group">
			<label class="col-sm-3 control-label">Ville</label>
			<div class="col-sm-9">
			<input id="adr_ville" class="form-control" name="adr_ville" value="<?php echo $values->adr_ville;?>" placeholder="Ville" type="text">
			</div>
		    </div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Code Postal</label>
			<div class="col-sm-9">
			<input id="adr_code" class="form-control" name="adr_code" value="<?php echo $values->adr_code;?>" placeholder="Code Postal" type="text">
			</div>
		    </div>
			</div>		
		    <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Tournee</label>
			<div class="col-sm-9">
			<select id="adr_tournee" class="form-control" name="adr_tournee">
			<?php print_r($tournee_id); ?>		
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Ordre dans la tournée</label>
			<div class="col-sm-9">
			<input id="adr_ordretournee" class="form-control" name="adr_ordretournee" value="<?php echo $values->adr_ordretournee;?>" placeholder="Ordre dans la tournée" type="text">
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Client</label>
			<div class="col-sm-9">
			<select id="adr_client" class="form-control" name="adr_client" onchange="adrajaxclient();">
			<?php print_r($client_id); ?>		
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Heure de livraison</label>
			<div class="col-sm-9">
			<input id="adr_heure" class="form-control" name="adr_heure" value="<?php echo $values->adr_heure;?>" placeholder="Heure de livraison" type="text">
			</div>
		    </div>
			</div>	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Type de livraison</label>
			<div class="col-sm-9">
			<select id="adr_livraison" class="form-control" name="adr_livraison">
			<option value="">sélectionner</option>
			<option value="boite_aux" <?php if($values->adr_livraison=="boite_aux") { echo "selected";}?>>boite aux lettres or sous la porte</option>
			<option value="or_mains" <?php if($values->adr_livraison=="or_mains") { echo "selected";}?>>or mains propres</option>
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Horaires de livraison</label>
			<div class="col-sm-9">
			<input id="adr_horaires" class="form-control" name="adr_horaires" value="<?php echo $values->adr_horaires;?>" placeholder="Horaires de livraison" type="text">
			</div>
		    </div>
			</div>	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Contact</label>
			<div class="col-sm-9">
			<input id="adr_contact" class="form-control" name="adr_contact" value="<?php echo $values->adr_contact;?>" placeholder="Contact" type="text">
			</div>
		    </div>
			</div>	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Telephone du contact</label>
			<div class="col-sm-9">
			<input id="adr_telcontact" class="form-control" name="adr_telcontact" value="<?php echo $values->adr_telcontact;?>" placeholder="Telephone du contact" type="text">
			</div>
		    </div>
			</div>	
			<div id="type_data"><!---type_data---->
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Dernière facture</label>
			<div class="col-sm-9">
			<input id="adr_derniere" class="form-control" name="adr_derniere" value="<?php echo $values->adr_derniere;?>" placeholder="Dernière facture" type="text">
			</div>
		    </div>
			</div>	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Dernière facture impayée</label>
			<div class="col-sm-9">
			<input id="adr_impayee" class="form-control" name="adr_impayee" value="<?php echo $values->adr_impayee;?>" placeholder="Dernière facture impayée" type="text">
			</div>
		    </div>
			</div>	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Avant dernière facture impayée</label>
			<div class="col-sm-9">
			<input id="adr_avant" class="form-control" name="adr_avant" value="<?php echo $values->adr_avant;?>" placeholder="Avant dernière facture impayée" type="text">
			</div>
		    </div>
			</div>		
			</div><!---type_data end---->	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Bloqué</label>
			<div class="col-sm-9">
			<select id="adr_bloque" class="form-control" name="adr_bloque">
			<option value="">sélectionner</option>
			<option value="oui" <?php if($values->adr_bloque=="oui") { echo "selected";}?>>oui</option>
			<option value="non" <?php if($values->adr_bloque=="non") { echo "selected";}?>>non</option>
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
			<label class="col-sm-3 control-label">Numero de la Rue</label>
			<div class="col-sm-9">
			<input id="adr_numero" class="form-control" name="adr_numero" value="<?php echo $values->adr_numero;?>" placeholder="Numero de la Rue" type="text">
			</div>
		    </div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Rue</label>
			<div class="col-sm-9">
			<input id="adr_numero" class="form-control" name="adr_rue" value="<?php echo $values->adr_rue;?>" placeholder="Rue" type="text">
			</div>
		    </div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Type de voie</label>
			<div class="col-sm-9">
			<select id="adr_voie" class="form-control" name="adr_voie">
			<option value="">sélectionner</option>
			<option value="rue" <?php if($values->adr_voie=="rue") { echo "selected";}?>>rue</option>
			<option value="avenue" <?php if($values->adr_voie=="avenue") { echo "selected";}?>>avenue</option>
			<option value="boulevard" <?php if($values->adr_voie=="boulevard") { echo "selected";}?>>boulevard</option>
			<option value="place" <?php if($values->adr_voie=="place") { echo "selected";}?>>place</option>
			</select>
			</div>
		    </div>
			</div>
			<div class="row guide">
		    <div class="form-group">
			<label class="col-sm-3 control-label">Ville</label>
			<div class="col-sm-9">
			<input id="adr_ville" class="form-control" name="adr_ville" value="<?php echo $values->adr_ville;?>" placeholder="Ville" type="text">
			</div>
		    </div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Code Postal</label>
			<div class="col-sm-9">
			<input id="adr_code" class="form-control" name="adr_code" value="<?php echo $values->adr_code;?>" placeholder="Code Postal" type="text">
			</div>
		    </div>
			</div>		
		    <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Tournee</label>
			<div class="col-sm-9">
			<select id="adr_tournee" class="form-control" name="adr_tournee">
			<?php print_r($tournee_id); ?>		
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Ordre dans la tournée</label>
			<div class="col-sm-9">
			<input id="adr_ordretournee" class="form-control" name="adr_ordretournee" value="<?php echo $values->adr_ordretournee;?>" placeholder="Ordre dans la tournée" type="text">
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Client</label>
			<div class="col-sm-9">
			<select id="adr_client" class="form-control" name="adr_client" onchange="adrajaxclient();">
			<?php print_r($client_id); ?>		
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Heure de livraison</label>
			<div class="col-sm-9">
			<input id="adr_heure" class="form-control" name="adr_heure" value="<?php echo $values->adr_heure;?>" placeholder="Heure de livraison" type="text">
			</div>
		    </div>
			</div>	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Type de livraison</label>
			<div class="col-sm-9">
			<select id="adr_livraison" class="form-control" name="adr_livraison">
			<option value="">sélectionner</option>
			<option value="boite_aux" <?php if($values->adr_livraison=="boite_aux") { echo "selected";}?>>boite aux lettres or sous la porte</option>
			<option value="or_mains" <?php if($values->adr_livraison=="or_mains") { echo "selected";}?>>or mains propres</option>
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Horaires de livraison</label>
			<div class="col-sm-9">
			<input id="adr_horaires" class="form-control" name="adr_horaires" value="<?php echo $values->adr_horaires;?>" placeholder="Horaires de livraison" type="text">
			</div>
		    </div>
			</div>	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Contact</label>
			<div class="col-sm-9">
			<input id="adr_contact" class="form-control" name="adr_contact" value="<?php echo $values->adr_contact;?>" placeholder="Contact" type="text">
			</div>
		    </div>
			</div>	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Telephone du contact</label>
			<div class="col-sm-9">
			<input id="adr_telcontact" class="form-control" name="adr_telcontact" value="<?php echo $values->adr_telcontact;?>" placeholder="Telephone du contact" type="text">
			</div>
		    </div>
			</div>	
			<div id="type_data"><!---type_data---->
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Dernière facture</label>
			<div class="col-sm-9">
			<input id="adr_derniere" class="form-control" name="adr_derniere" value="<?php echo $values->adr_derniere;?>" placeholder="Dernière facture" type="text">
			</div>
		    </div>
			</div>	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Dernière facture impayée</label>
			<div class="col-sm-9">
			<input id="adr_impayee" class="form-control" name="adr_impayee" value="<?php echo $values->adr_impayee;?>" placeholder="Dernière facture impayée" type="text">
			</div>
		    </div>
			</div>	
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Avant dernière facture impayée</label>
			<div class="col-sm-9">
			<input id="adr_avant" class="form-control" name="adr_avant" value="<?php echo $values->adr_avant;?>" placeholder="Avant dernière facture impayée" type="text">
			</div>
		    </div>
			</div>		
			</div><!---type_data end---->
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label">Bloqué</label>
			<div class="col-sm-9">
			<select id="adr_bloque" class="form-control" name="adr_bloque">
			<option value="">sélectionner</option>
			<option value="oui" <?php if($values->adr_bloque=="oui") { echo "selected";}?>>oui</option>
			<option value="non" <?php if($values->adr_bloque=="non") { echo "selected";}?>>non</option>
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
