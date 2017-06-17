<div class="col-md-offset-2">
	<span style="font-size: 40px;line-height: 30px;vertical-align: middle;">◀</span>
	<a href="<?php echo site_url('feuille_de_route');?>">Retour a la liste</a>
</div>
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12 fiche">   	
	<p><?php echo $values->distributeur_firstname." ".$values->distributeur_lastname;?></p>
	<p>TEL : <?php echo $values->distributeur_phone;?></p>
	<p><?php echo $values->distributeur_address;?></p>
	<p><?php echo $values->distributeur_code.", ".$values->distributeur_ville;?></p>
	<p>Ceci est la base de calcul du temps de travail conforme à la convention collective de distribution. Le calcul du colporteur est soit supérieur, soit équivalent.</p>
	 </div>
	 <div class="col-md-12 fiche">   
		<p><strong>Feuille de route générée le <?php echo $values->date;?></strong></p>
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td>Date limite de distribution</td><td><?php echo $values->date_distribution;?></td>			
				</tr>
				<tr>
					<td>Secteur</td><td><?php echo $values->secteur_name;?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-12 fiche"> 
		<p><strong>Addresses particulleres :</strong></p>
		<table class="table table-bordered">
			<tbody>
				<tr>
					<?php foreach($values->addresses as $row):?>
					<td><?php echo $row->ville." - Code : ".$row->code;?></td>				
					<?php endforeach;?>
				</tr>
				<tr>
					<?php foreach($values->addresses as $row):?>
					<td><?php echo $row->address?></td>				
					<?php endforeach;?>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-12 fiche"> 
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td>Distributeur</td>
					<td><?php echo $values->distributeur_firstname." ".$values->distributeur_lastname;?></td>
				</tr>				
				<tr>
					<td>Nombre d'heures par jour (contrat) :</td>
					<td><?php echo $values->nombre_par_jour;?></td>				
				</tr>
				<tr>
					<td>Poids total en charge du vehicule :</td>
					<td><?php echo $values->poids_total_vehicule;?></td>
				</tr>
				<tr>
					<td>Immatriculation du vehicule</td>
					<td><?php echo $values->immatriculation;?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-12 fiche"> 
		<table class="table table-bordered">
			<thead>
				<tr>
					<th></th>
					<th>HLM</th>
					<th>Res</th>
					<th>Pav</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Poids de la poignée : <br />
						Rappel de la convention collective : Deuxième tournée obligatoire à partir de 500g par poignée.
					</td>
					<td>
						
					</td>
					<td>
						
					</td>
					<td>
						
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td>Poids de chargement :</td>
					<td colspan="3"></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="col-md-12 fiche"> 
		<p><strong>Gestion des boites et des clients :</strong></p>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Nombre de documents : 1 <br/>
						Assemblés et triés par Le Colporteur</th>
					<th>HLM</th>
					<th>Res</th>
					<th>Pav</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Quantité Totale réelle de boites du secteur:</td>
					<td><?php echo $values->total_boites_hlm;?></td>
					<td><?php echo $values->total_boites_res;?></td>
					<td><?php echo $values->total_boites_pav;?></td>
				</tr>
				<tr>
					<td>Quantité de boites aux lettres triées:</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Quantité prise par le distributeur:</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Type de secteur convention : </td>
					<td colspan="3"><?php echo $values->secteur_type;?></td>
				</tr>
				<tr>
					<td>Rapidité minimale prévue par convention :</td>
					<td colspan="3"></td>
				</tr>
				<tr>
					<td>Rapidité maximale prévue par convention : </td>
					<td colspan="3"></td>
				</tr>
				<tr>
					<td>Temps minimal alloué par la convention collective :</td>
					<td colspan="3"></td>
				</tr>
				<tr>
					<td>Temps maximal alloué par la convention collective :</td>
					<td colspan="3"></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-12 fiche"> 
	<table class="table table-bordered">
		<tbody>
			<tr>
				<td>
					Distance trajet (Aller) : <br>
					Base convention collective
				</td>
				<td colspan="2">
					28 km
				</td>
			</tr>
			<tr>
				<td>
					Temps de trajet : <br />
					Temps de trajet (aller), Base convention collective.
				</td>
				<td colspan="2">
					
				</td>
			</tr>
			<tr>
				<td>Temps total comprennant 15 mn de chargement :</td>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td>
					Indemnités frais kilométriques (Base convention) :
				</td>
				<td colspan="2">
					
				</td>
			</tr>
			<tr style="height: 200px;">
				<td>Remarques :</td>
				<td colspan="2">
					<p>
						<?php echo $values->distributeur_firstname." ".$values->distributeur_lastname;?> <br />
						Signature :
					</p>
				</td>
			</tr>
			<tr>
				<td>Informations saisies par les distributeurs</td>
				<td>HLM -- RES -- PAV</td>
				<td>Total / Total manuel</td>
			</tr>
		</tbody>
	</table>
	</div>
</div>