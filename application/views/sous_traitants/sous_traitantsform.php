<div class="row" style="margin-top: 20px">
    <div class="col-md-12 fiche">
<?php
$url = explode('/', current_url());
if(isset($url[8])){
array_pop($url);
$cur= implode('/', $url);
}
else{
$cur= current_url();
}
?>

        <?php if ($action == 'modif') {
            $cible = "$controleur/$methode/$id";
        }
        else {
            $cible = "$controleur/$methode";
        }
        if ($multipart) {
            echo form_open_multipart($cur,array('role'=>'form','class'=>'form-horizontal'),array('__form'=>'x'));
        }
        else {
            echo form_open($cur,array('role'=>'form','class'=>'form-horizontal'),array('__form'=>'x'));
        }		
        //include 'application/views/templates/form_champs.php';
       ?>
	   <style>
	   .guide{
		   border-bottom:0px!important;
	   }
	   </style>

<div class="tab-content"> 
<?php if($id==0){ ?>
		    <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Societe</label>
			<div class="col-sm-9">
			
			<select id="sous_societe" class="form-control" name="sous_societe">
				<option value="0">(choisissez)</option>
				<?php foreach ($societe as $sc):
                    $selected = ($values->sous_societe == $sc->id)?" selected=\"selected\"":""?>
                    <option value="<?php echo $sc->id?>"<?php echo $selected?>><?php echo html_escape($sc->value)?></option>
                <?php endforeach;?>
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			&nbsp;
			<div class="form-group">
			<label class="col-sm-3 control-label">Ville</label>
			<div class="col-sm-9">
			<select id="sous_ville" class="form-control" name="sous_ville">
			<?php print_r($ville); ?>		
			</select>
			</div>
		    </div>
			</div>	
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Total a distribuer</label>
			<div class="col-sm-9">
			<input id="sous_distribuer" class="form-control" name="sous_distribuer" value="<?php echo $values->sous_distribuer;?>" placeholder="Total a distribuer" type="text">
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Pavillons</label>
			<div class="col-sm-9">
			<select id="sous_pavillons" class="form-control" name="sous_pavillons">
			<option value="oui" <?php if($values->sous_pavillons=="oui"){ echo "selected"; }?>>oui</option>
			<option value="non" <?php if($values->sous_pavillons=="non"){ echo "selected"; }?>>non</option>
			</select>
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Residences</label>
			<div class="col-sm-9">
			
			<select id="sous_residences" class="form-control" name="sous_residences">
			<option value="oui" <?php if($values->sous_residences=="oui"){ echo "selected"; }?>>oui</option>
			<option value="non" <?php if($values->sous_residences=="non"){ echo "selected"; }?>>non</option>
			</select>
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Hlm</label>
			<div class="col-sm-9">
			
			<select id="sous_hlm" class="form-control" name="sous_hlm">
			<option value="oui" <?php if($values->sous_hlm=="oui"){ echo "selected"; }?>>oui</option>
			<option value="non" <?php if($values->sous_hlm=="non"){ echo "selected"; }?>>non</option>
			</select>
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Client</label>
			<div class="col-sm-9">
			<select id="sous_client" class="form-control" name="sous_client">
			<?php print_r($client); ?>		
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Prix max proposé</label>
			<div class="col-sm-9">
			<input id="sous_prix" class="form-control" name="sous_prix" value="<?php echo $values->sous_prix;?>" placeholder="Prix max proposé" type="text">
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Type doc</label>
			<div class="col-sm-9">
			
			<select id="sous_doc" class="form-control" name="sous_doc">
			<option value="flyer" <?php if($values->sous_doc=="flyer"){ echo "selected"; }?>>flyer</option>
			<option value="depliant" <?php if($values->sous_doc=="depliant"){ echo "selected"; }?>>dépliant</option>
			<option value="catalogue" <?php if($values->sous_doc=="catalogue"){ echo "selected"; }?>>catalogue</option>
			<option value="magazine" <?php if($values->sous_doc=="magazine"){ echo "selected"; }?>>magazine</option>
			</select>
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Type Client</label>
			<div class="col-sm-9">
			<select id="sous_typeclient" class="form-control" name="sous_typeclient">
			<option value="mairie" <?php if($values->sous_typeclient=="mairie"){ echo "selected"; }?>>mairie</option>
			<option value="client_exigeant" <?php if($values->sous_typeclient=="client_exigeant"){ echo "selected"; }?>>client exigeant</option>
			<option value="prix_bas" <?php if($values->sous_typeclient=="prix_bas"){ echo "selected"; }?>>prix bas</option>
			
			</select>
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Date limite</label>
			<div class="col-sm-9">
			<input id="sous_date" class="form-control form-date-field" name="sous_date" value="<?php echo $values->sous_date;?>" placeholder="Date limite" type="date" autocomplete="off">
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Semaine prevue</label>
			<div class="col-sm-9">
			<input id="sous_prevue" class="form-control" name="sous_prevue" value="<?php echo $values->sous_prevue;?>" placeholder="Semaine prevue" type="text">
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Sous-Traitant Demande</label>
			<div class="col-sm-9">		
			<select id="sous_demande" class="form-control" name="sous_demande">
			<?php print_r($demande); ?>		
			</select>
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Tel Sous-Traitant</label>
			<div class="col-sm-9">
			<input id="sous_tel" class="form-control" name="sous_tel" value="<?php echo $values->sous_tel;?>" placeholder="Tel Sous-Traitant" type="text">
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Mail</label>
			<div class="col-sm-9">
			<input id="sous_mail" class="form-control" name="sous_mail" value="<?php echo $values->sous_mail;?>" placeholder="Mail" type="text">
			</div>
			</div>
			</div>
            <?php } else{ ?>
		    <div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Societe</label>
			<div class="col-sm-9">
			<select id="sous_societe" class="form-control" name="sous_societe">
				<option value="0">(choisissez)</option>
				<?php foreach ($edit_societe as $sc):
                    $selected = ($values->sous_societe == $sc->id)?" selected=\"selected\"":""?>
                    <option value="<?php echo $sc->id?>"<?php echo $selected?>><?php echo html_escape($sc->value)?></option>
                <?php endforeach;?>
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			&nbsp;
			<div class="form-group">
			<label class="col-sm-3 control-label">Ville</label>
			<div class="col-sm-9">
			<select id="sous_ville" class="form-control" name="sous_ville">
			<?php print_r($edit_ville); ?>		
			</select>
			</div>
		    </div>
			</div>	
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Total a distribuer</label>
			<div class="col-sm-9">
			<input id="sous_distribuer" class="form-control" name="sous_distribuer" value="<?php echo $values->sous_distribuer;?>" placeholder="Total a distribuer" type="text">
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Pavillons</label>
			<div class="col-sm-9">
			<select id="sous_pavillons" class="form-control" name="sous_pavillons">
			<option value="oui" <?php if($values->sous_pavillons=="oui"){ echo "selected"; }?>>oui</option>
			<option value="non" <?php if($values->sous_pavillons=="non"){ echo "selected"; }?>>non</option>
			</select>
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Residences</label>
			<div class="col-sm-9">
			
			<select id="sous_residences" class="form-control" name="sous_residences">
			<option value="oui" <?php if($values->sous_residences=="oui"){ echo "selected"; }?>>oui</option>
			<option value="non" <?php if($values->sous_residences=="non"){ echo "selected"; }?>>non</option>
			</select>
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Hlm</label>
			<div class="col-sm-9">
			
			<select id="sous_hlm" class="form-control" name="sous_hlm">
			<option value="oui" <?php if($values->sous_hlm=="oui"){ echo "selected"; }?>>oui</option>
			<option value="non" <?php if($values->sous_hlm=="non"){ echo "selected"; }?>>non</option>
			</select>
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Client</label>
			<div class="col-sm-9">
			<select id="sous_client" class="form-control" name="sous_client">
			<?php print_r($edit_client); ?>		
			</select>
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Prix max proposé</label>
			<div class="col-sm-9">
			<input id="sous_prix" class="form-control" name="sous_prix" value="<?php echo $values->sous_prix;?>" placeholder="Prix max proposé" type="text">
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Type doc</label>
			<div class="col-sm-9">
			
			<select id="sous_doc" class="form-control" name="sous_doc">
			<option value="flyer" <?php if($values->sous_doc=="flyer"){ echo "selected"; }?>>flyer</option>
			<option value="depliant" <?php if($values->sous_doc=="depliant"){ echo "selected"; }?>>dépliant</option>
			<option value="catalogue" <?php if($values->sous_doc=="catalogue"){ echo "selected"; }?>>catalogue</option>
			<option value="magazine" <?php if($values->sous_doc=="magazine"){ echo "selected"; }?>>magazine</option>
			</select>
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Type Client</label>
			<div class="col-sm-9">
			<select id="sous_typeclient" class="form-control" name="sous_typeclient">
			<option value="mairie" <?php if($values->sous_typeclient=="mairie"){ echo "selected"; }?>>mairie</option>
			<option value="client_exigeant" <?php if($values->sous_typeclient=="client_exigeant"){ echo "selected"; }?>>client exigeant</option>
			<option value="prix_bas" <?php if($values->sous_typeclient=="prix_bas"){ echo "selected"; }?>>prix bas</option>			
			</select>
			
			
			</div>
			</div>
			</div>			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Date limite</label>
			<div class="col-sm-9">
			<input id="sous_date" class="form-control  form-date-field" name="sous_date" value="<?php echo $values->sous_date;?>" placeholder="Date limite" type="date" autocomplete="off">
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Semaine prevue</label>
			<div class="col-sm-9">
			<input id="sous_prevue" class="form-control" name="sous_prevue" value="<?php echo $values->sous_prevue;?>" placeholder="Semaine prevue" type="text">
			</div>
			</div>
			</div>
			
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Sous-Traitant Demande</label>
			<div class="col-sm-9">		
			<select id="sous_demande" class="form-control" name="sous_demande" onchange="ajaxemployee();">
			<?php print_r($edit_demande); ?>		
			</select>
			</div>
			</div>
			</div>
			<div id="type_data"><!---type_data---->
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Tel Sous-Traitant</label>
			<div class="col-sm-9">
			<input id="sous_tel" class="form-control" name="sous_tel" value="<?php echo $values->sous_tel;?>" placeholder="Tel Sous-Traitant" type="text">
			</div>
			</div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" >Mail</label>
			<div class="col-sm-9">
			<input id="sous_mail" class="form-control" name="sous_mail" value="<?php echo $values->sous_mail;?>" placeholder="Mail" type="text">
			</div>
			</div>
			</div>
			</div><!---type_data end---->	
<?php	
}
?>   
 </div><!---tab end-->
        <br />
        <p class="text-center"><button type="submit" class="btn btn-primary"><?php echo $confirmation?></button>  <a href="<?php echo  base_url()."index.php/".$controleur;?>" class="btn btn-default">Retour</a></p>
        <?php echo form_close()?>
    </div>
</div>
