<div class="col-md-offset-2">
	<span style="font-size: 40px;line-height: 30px;vertical-align: middle;">◀</span>
	<a href="<?php echo site_url('feuille_de_route/ville');?>">Retour a la liste</a>
</div>
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12 fiche">   	
		<h4>Créer et éditer une feuille de route : Choix des secteurs et des tris</h4>
		<table class="table table-bordered">
			<thead>
				<tr class="info">
					<th>Choix du secteur (par distribution) :</th>
					<th>Commande(s) liée(s) à ce tri :</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($values as $row):?>
				<tr>
					<td>
						<input type="checkbox" name=""> <input type="checkbox" name=""> <?php echo $row->sec_nom;?> <br />
						<small><?php echo $row->fdr_ok." FDR OK";?> / <?php echo $row->fdr_total." FDR Total";?></small> <br />

						<small>Date limite : <?php echo $row->date_limite;?></small> <br />
						<small>Date de création de la feuille de tri : <?php echo $row->date_feuille_de_tri;?></small>
					</td>
					<td>
						
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>