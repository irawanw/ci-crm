<div class="col-md-offset-2">
	<span style="font-size: 40px;line-height: 30px;vertical-align: middle;">◀</span>
	<a href="">Retour a la liste</a>
</div>
<form class="form-horizontal" action="<?php echo site_url('feuilles_de_tri/save_fdr');?>" method="POST">    		
<div class="row" style="margin-top: 20px">
    <div class="col-md-12">
    	<div class="row">    		
    		<?php foreach($lists as $id => $distributions):?>
    		<div class="col-md-12">
				<div class="panel panel-default">
				  <input type="hidden" name="feuilles_de_tri_id[]" value="<?php echo $group_id;?>">
				  <div class="panel-heading"><input type="checkbox" name="secteur_id[]" value="<?php echo $distributions[0]->secteur_id;?>" checked /> <?php echo $distributions[0]->secteur_name;?></div>
				  <div class="panel-body">
				  		<div class="form-group">
				  			<label class="col-md-4"> </label>
				  			<div class="col-md-6">
				  				<?php foreach($checkbox_persons as $i => $checkbox): ?>
				  				<?php $checked = $i == 0 ? "checked" : "";?>
				  				<input type="radio" name="person_type" class="person-type" data-id="<?php echo $id;?>" value="<?php echo $checkbox->id;?>" <?php echo $checked;?>> <?php echo $checkbox->name;?>
				  				<?php endforeach;?>
				  			</div>
				  		</div>
				  		<div class="form-group">
				  			<label class="col-md-4">Person</label>
				  			<div class="col-md-6">
				  				<select class="form-control" name="person[]" id="person-<?php echo $id;?>">
				  					<option value="0">(Choose)</option>
				  					<?php foreach($person_liste as $row):?>
				  					<option value="<?php echo $row->id;?>"><?php echo $row->name;?></option>
				  					<?php endforeach;?>
				  				</select>
				  			</div>
				  		</div>
				    	<div class="form-group">
				    		<label class="col-md-4">Date limite de distribution </label>
				    		<div class="col-md-6">
				    			<input type="text" class="form-control" name="date_distribution[]" value="<?php echo $distributions[0]->date_distribution;?>" readonly />
				    		</div>
				    	</div>
				    	<div class="form-group">
				    		<label class="col-md-4">Date de la feuille de route</label>
				    		<div class="col-md-6">
				    			<input type="text" class="form-control date" name="date[]" value="<?php echo date("Y-m-d"); ?>" />
				    		</div>
				    	</div>
				    	<div class="form-group">
				    		<label class="col-md-4">Choix du distributeur</label>
				    		<div class="col-md-6">
				    			<select class="form-control" name="distributeur[]">
				    				<?php foreach($distributeur_liste as $row):?>
				    				<option value="<?php echo $row->emp_id;?>"><?php echo $row->emp_nom;?></option>
				    				<?php endforeach;?>
				    			</select>
				    		</div>
				    	</div>
				    	<div class="form-group">
				    		<label class="col-md-4">Prendre le poids en considération</label>
				    		<div class="col-md-6">
				    			<label class="radio-inline">
								  <input type="radio" name="poids_consideration-<?php echo $distributions[0]->secteur_id;?>" value="oui" checked /> Oui
								</label>
								<label class="radio-inline">
								  <input type="radio" name="poids_consideration-<?php echo $distributions[0]->secteur_id;?>" value="non" /> Non
								</label>
				    		</div>
				    	</div>
				    	<div class="form-group">
				    		<label class="col-md-4">Prendre en considération le tri à la main</label>
				    		<div class="col-md-6">
				    			<label class="radio-inline">
								  <input type="radio" name="tri_a_la_main-<?php echo $distributions[0]->secteur_id;?>" value="oui" /> Oui
								</label>
								<label class="radio-inline">
								  <input type="radio" name="tri_a_la_main-<?php echo $distributions[0]->secteur_id;?>" value="non" checked /> Non
								</label>
				    		</div>
				    	</div>			
				  </div>
				  <div class="panel-heading">Liste des Distributions</div>
				  <div class="panel-body">
				  	<table class="table table-bordered">
				  		<thead>
				  			<tr>
				  				<th>Clients</th>
				  				<th>Poids</th>
				  				<th>HLM</th>
				  				<th>RES</th>
				  				<th>PAV</th>
				  			</tr>
				  		</thead>
				  		<tbody>
				  			<?php 
				  			$total_boites_hlm = 0;
				  			$total_boites_res = 0;
				  			$total_boites_pav = 0;
				  			foreach($distributions as $distribution):
				  				$total_boites_hlm += $distribution->hlm;
				  				$total_boites_res += $distribution->res;
				  				$total_boites_pav += $distribution->pav;
				  			?>
				  			<tr>
				  				<td><?php echo $distribution->client_name;?></td>
				  				<td></td>
				  				<td><?php echo $distribution->hlm;?></td>
				  				<td><?php echo $distribution->res;?></td>
				  				<td><?php echo $distribution->pav;?></td>
				  			</tr>
				  			<?php endforeach;?>
				  		</tbody>
				  		<tfoot>
				  			<tr>
				  				<th>TOTAL POIDS / BOITES (HLM,RES,PAV)</th>
				  				<th></th>
				  				<th><input type="text" class="form-control" name="total_boites_hlm[]" value="<?php echo $total_boites_hlm;?>" style="width: 80px;"></th>
				  				<th><input type="text" class="form-control" name="total_boites_res[]" value="<?php echo $total_boites_res;?>" style="width: 80px;"></th>
				  				<th><input type="text" class="form-control" name="total_boites_pav[]" value="<?php echo $total_boites_pav;?>" style="width: 80px;"></th>
				  			</tr>
				  			<tr>
				  				<th colspan="2">Ajout boites supplémentaires</th>				  			
				  				<th><input type="text" class="form-control" name="boites_supplementaires_hlm[]" value="0" style="width: 80px;"></th>
				  				<th><input type="text" class="form-control" name="boites_supplementaires_res[]" value="0" style="width: 80px;"></th>
				  				<th><input type="text" class="form-control" name="boites_supplementaires_pav[]" value="0" style="width: 80px;"></th>
				  			</tr>
				  			<tr>
				  				<th colspan="2">Temps de distribution (Nbr boîtes/heure)</th>				  				
				  				<th><input type="text" class="form-control" name="temps_de_distribution_hlm[]" value="0" style="width: 80px;"></th>
				  				<th><input type="text" class="form-control" name="temps_de_distribution_res[]" value="0" style="width: 80px;"></th>
				  				<th><input type="text" class="form-control" name="temps_de_distribution_pav[]" value="0" style="width: 80px;"></th>
				  			</tr>
				  		</tfoot>
				  	</table>
				  </div>
				</div>  			
    		</div>
    		<?php endforeach; ?>    		
    	</div>
    	<div class="row">
    		<dic class="col-md-12">
    			<p class="text-center"><button type="submit" class="btn btn-primary">Enregistrer</button></p>
    		</dic>
    	</div>
    </div>
</div>
</form>