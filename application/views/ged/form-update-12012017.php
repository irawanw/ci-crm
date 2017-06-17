<div id="controls">
</div>
<style>	
	.UpdateMetaData div.col-md-3{
		float: left;
		width: 33%;
		padding-bottom: 5px;
		border-bottom: 1px dashed #ccc;
		margin-bottom: 10px;
	}
	.UpdateMetaData div.col-md-6{
		float: left;
		width: 50%;
	}		
	.UpdateMetaData div.col-md-12{
		float: left;
		width: 100%;
	}	
	.UpdateMetaData input{
		padding: 7px 5px;
		border: 1px solid #ccc;
		border-radius: 3px;
	}
	.UpdateMetaData select {
		background: #ffffff !important;
	}
	.ui-dialog {
		z-index: 999999 !important;
	}
	.subform {
		display: none;
	}
	#loading-img {
	    background: url(http://svr.bonenvoi.com/crm/assets/images/loader.gif) center center no-repeat;
	    height: 100%;
	    z-index: 20;
	}

	.overlay {
	    background: #e9e9e9;
	    z-index: 99999;
	    position: absolute;
	    display: none;
	    top: 0;
	    right: 0;
	    bottom: 0;
	    left: 0;
	    opacity: 0.5;
	}
</style>
<div class="overlay">
    <div id="loading-img"></div>
</div>

<div class="col-md-2">
	<span style="font-size: 40px;line-height: 30px;vertical-align: middle;">◀</span>
	<a href="<?php echo site_url('ged');?>">Retour a la liste</a>
</div>

<div class="row" style="margin-top: 20px">
    <div class="col-md-12 fiche">
<!-- FORM UPDATE -->
<form class="UpdateMetaData" method="post" action="<?php echo site_url('ged/update');?>">	
	<input type="hidden" id="update-id" name="id" />
	<input type="hidden" id="update-id-file" name="file-id" />
	<input type="hidden" id="update-format-file" name="format-file" />
	<input type="hidden" id="update-size-file" name="size-file" />
	<input type="hidden" id="update-date-creation" name="date-creation" />
	<input type="hidden" id="update-id-user" name="user-id" />
	<input type="hidden" id="update-old-document-type" name="old-document-type" />

	<div class="col-md-2">
		<label>Filename</label><br>
		<input type="text" id="update-file-name" name="file-name" class="form-control" />
	</div>
	<dvi class="col-md-1">
		<label>&nbsp;</label>
		<input type="text" id="update-file-extension" name="file-extension" class="form-control" readonly="readonly" />		
	</dvi>

	<div class="col-md-3">
		<label>Created by</label><br>
		<select class="form-control" id="update-createdby" name="createdby">
			<option></option>
		</select>
	</div>
	<div class="col-md-3">
		<label>Uploaded by</label><br>
		<select class="form-control" id="update-uploadedby" name="uploadedby">
			<option></option>
		</select>
	</div>	
	<div style="clear: both"></div>
	<div class="col-md-6">
		<label>Description</label><br>
		<textarea class="form-control" id="update-description" rows="5" name="description"></textarea>
	</div>
	<div style="clear: both"></div>
	<div class="col-md-3">
		<label>Type Document</label><br>
		<select class="form-control" id="update-typedocument" name="document-type">
			<option>Aucun</option>
			<option>Infographie</option>
			<option>Plan</option>
			<option>Piece Comptable</option>
			<option>Facture Client</option>
			<option>Devis Client</option>
			<option>E-Mailing</option>
			<option>Site Internet</option>
			<option>Administratif Divers</option>
		</select>
	</div>

	<div style="clear: both"></div>
	<div class="update-subform-infographie update-subform-documenttype" style="display: none">
		<div class="col-md-3">
			<label>Client Name</label><br>
			<select class="form-control" id="update-clientName" name="client">
				<option></option>
			</select>
		</div>
		<div class="col-md-3">
			<label>Devis</label><br>
			<select class="form-control" id="update-devis" name="devis">
				<option></option>
			</select>
		</div>
		<div class="col-md-3">
			<label>Facture</label><br>
			<select class="form-control" id="update-facture" name="facture">
				<option></option>
			</select>
		</div>
		<div style="clear: both"></div>
		
		<div class="col-md-3 subform-soustype-infographie">
			<label>Sous-Type</label><br>
			<select class="form-control" id="update-soustype" name="sous-type">
				<option>Aucun</option>
				<option>Flyer</option>
				<option>Depliant</option>
				<option>Affiche</option>
				<option>Bache</option>
				<option>Carte de Visite</option>
				<option>Akilux</option>
				<option>Panneau</option>
				<option>Logo</option>
				<option>Brochure</option>
				<option>Enveloppes</option>
				<option>Drapeau</option>
				<option>Blocs</option>
				<option>Auto-Collant</option>
				<option>Chemise</option>
				<option>E-Mailing</option>
				<option>Site Internet</option>
				<option>Fax</option>
				<option>Photo</option>
			</select>
		</div>
		<div style="clear: both"></div>
		
		<div class="update-subform-format update-subform-soustype" style="display: none">
			<div class="col-md-3">
				<label>Format Ouvert</label><br>
				<input class="form-control" id="update-format-ouvert" name="format-ouvert">
			</div>
			<div class="col-md-3">
				<label>Format Ferme</label><br>
				<input class="form-control" id="update-format-ferme" name="format-ferme">
			</div>
			<div style="clear: both"></div>
		</div>
		
		<div class="update-subform-largeur-longueur update-subform-soustype" style="display: none">
			<div class="col-md-3">
				<label>Largeur</label><br>
				<input class="form-control" id="update-largeur" name="largeur">
			</div>
			<div class="col-md-3">
				<label>Longueur</label><br>
				<input class="form-control" id="update-longueur" name="longueur">
			</div>
			<div class="col-md-3 update-nombre-pages" style="display: none">
				<label>Nombre de Pages</label><br>
				<input class="form-control" id="update-nombre-pages" name="nombre-pages" />
			</div>
			<div style="clear: both"></div>
		</div>		

		<div class="update-subform-emailing update-subform-soustype" style="display: none">
			<div class="col-md-3">
				<label>CAMPAGNE</label><br>
				<input class="form-control" id="update-campagne" name="campagne" />
			</div>
			<div class="col-md-3">
				<label>OBJET</label><br>
				<input class="form-control" id="update-objet" name="objet" />
			</div>
			<div style="clear: both"></div>
		</div>

		<div class="update-subform-siteinternet update-subform-soustype" style="display: none">
			<div class="col-md-3">
				<label>NOM DE DOMAINE</label><br>
				<input class="form-control" id="update-nomde-domaine" name="nomde-domaine">
			</div>			
			<div style="clear: both"></div>
		</div>

		<div class="update-subform-photo update-subform-soustype" style="display: none">
			<div class="col-md-3">
				<label>ORIGINE</label><br>
				<input class="form-control" id="update-origine" name="origine" />
			</div>
			<div class="col-md-3">
				<label>SUJET</label><br>
				<input class="form-control" id="update-sujet" name="sujet" />
			</div>
			<div style="clear: both"></div>
		</div>
	</div>

	<!-- SUB FORM PLAN -->
	<div style="clear: both"></div>
	
	<div class="col-md-3 subform-soustype-plan subform">
		<label>Sous-Type</label><br>
		<select class="form-control" id="update-soustype-plan" name="soustype-plan">
			<option>Aucun</option>
			<option>Plan General</option>
			<option>Secteur</option>
			<option>Plan General Plaintes Ponctuelles</option>				
			<option>Plan General Plaintes Permanentes</option>
			<option>Secteur Plaintes Permanentes</option>
		</select>
	</div>					
	<!-- /.SUB FORM PLAN -->

	<!-- SUB FORM PIECE COMPTABLE -->
	<div style="clear: both"></div>
	
	<div class="col-md-3 subform-soustype-piece-comptable subform">
		<label>Sous-Type</label><br>
		<select class="form-control" id="update-soustype-piece-comptable" name="soustype-piece-comptable">
			<option>Aucun</option>
			<option>Facture Fournisseur</option>
			<option>Releve De Compte</option>
		</select>
	</div>					
	<!-- /.SUB FORM PIECE COMPTABLE -->

	<!-- SUB FORM SITE INTERNET -->
	<div style="clear: both"></div>
	
	<div class="col-md-3 subform-soustype-site-internet subform">
		<label>Sous-Type</label><br>
		<select class="form-control" id="update-soustype-site-internet" name="soustype-site-internet">
			<option>Aucun</option>
			<option>Template</option>
			<option>Code</option>
			<option>Photo</option>
			<option>Autres</option>
		</select>
	</div>					
	<!-- /.SUB FORM SITE INTERNET -->

	<!-- SUB FORM ADMINISTRATIFS DIVERS -->
	<div style="clear: both"></div>
	
	<div class="col-md-3 subform-soustype-administratifs-divers subform">
		<label>Sous-Type</label><br>
		<select class="form-control" id="update-soustype-administratifs-divers" name="soustype-administratifs-divers">
			<option>Aucun</option>
			<option>Salarie</option>
			<option>Societe</option>
			<option>Divers</option>
		</select>
	</div>					
	<!-- /.SUB FORM ADMINISTRATIFS DIVERS -->

	<!-- SUB FORM GENERAL -->
	<div style="clear: both"></div>

	<div class="col-md-3 subform-ville subform">
		<label>Ville</label><br>
		<input class="form-control" id="update-ville" name="ville">
	</div>
	<div class="col-md-3 subform-numero subform">
		<label>Numero</label><br>
		<input class="form-control" id="update-numero" name="numero">
	</div>
	<div class="col-md-3 subform-mois subform">
		<label>Mois</label><br>
		<input class="form-control" id="update-mois" name="mois">
	</div>
	<div class="col-md-3 subform-societe subform">
		<label>Societe</label><br>
		<input class="form-control" id="update-societe" name="societe">
	</div>
	<div class="col-md-3 subform-banque subform">
		<label>Banque</label><br>
		<input class="form-control" id="update-banque" name="banque">
	</div>
	<div class="col-md-3 subform-origine subform">
		<label>Origine</label><br>
		<input class="form-control" id="update-origine-general" name="origine-general">
	</div>
	<div class="col-md-3 subform-sujet subform">
		<label>Sujet</label><br>
		<input class="form-control" id="update-sujet-general" name="sujet-general">
	</div>
	<div class="col-md-3 subform-nom subform">
		<label>Nom</label><br>
		<input class="form-control" id="update-nom" name="nom">
	</div>
	<div class="col-md-3 subform-statuts subform">
		<label>Statuts</label><br>
		<input class="form-control" id="update-statuts" name="statuts">
	</div>
	<div class="col-md-3 subform-assemblees subform">
		<label>Assemblees</label><br>
		<input class="form-control" id="update-assemblees" name="assemblees">
	</div>
	<div class="col-md-3 subform-descriptif subform">
		<label>Descriptif</label><br>
		<textarea class="form-control" id="update-descriptif" rows="3" name="descriptif"></textarea>
	</div>
	<div class="col-md-3 subform-piece subform">
		<label>Piece</label><br>
		<select class="form-control" id="update-piece" name="piece">
			<option>contrat</option>
			<option>fiche de paie</option>
			<option>divers</option>
		</select>
	</div>
	<div class="col-md-3 subform-format-emailing subform">
		<label>Format</label><br>
		<select class="form-control" id="insert-format-emailing" name="format-emailing">
			<option>Aucun</option>
			<option>Text</option>
			<option>Html</option>
		</select>
	</div>
	<div class="col-md-3 subform-bass-de-donnee subform">
		<label>Bass de donnee</label><br>
		<select class="form-control" id="insert-bass-de-donnee" name="bass-de-donnee">
			<option></option>
		</select>
	</div>
	<!-- ./SUB FORM GENERAL -->
	
	<div style="clear: both"></div><br>
	<div>		
		<button type="button" class="btn btn-primary do-update">Save Data</button>
	</div>	
</form>
<!-- EOF FORM UPDATE -->
    </div>
</div>

