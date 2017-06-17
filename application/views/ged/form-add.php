<style type="text/css">
	.InsertMetaData div.col-md-3{
		float: left;
		width: 33%;
		padding-bottom: 5px;
		border-bottom: 1px dashed #ccc;
		margin-bottom: 10px;
	}
	.InsertMetaData div.col-md-6{
		float: left;
		width: 50%;
	}		
	.InsertMetaData div.col-md-12{
		float: left;
		width: 100%;
	}	
	.InsertMetaData textarea{
		height: 70px;
	}
	.InsertMetaData input{
		padding: 7px 5px;
		border: 1px solid #ccc;
		border-radius: 3px;
	}
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
	.UpdateMetaData textarea{
		height: 70px;
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
    	<!-- FORM INSERT -->
<form class="InsertMetaData" action="<?php echo site_url('ged/upload');?>" method="post" enctype="multipart/form-data" accept-charset="utf-8">
	<div class="col-md-3">
		<label>Created by</label><br>
		<select class="form-control" id="insert-createdby" name="createdby">
			<option></option>
		</select>
	</div>
	<div class="col-md-3">
		<label>Uploaded by</label><br>
		<select class="form-control" id="insert-uploadedby" name="uploadedby">
			<option></option>
		</select>
	</div>	
	<div style="clear: both"></div>
	<div class="col-md-6">
		<label>Description</label><br>
		<textarea class="form-control" id="insert-description" name="description">
		</textarea>
	</div>
	<div style="clear: both"></div>
	<div class="col-md-3">
		<label>Type Document</label><br>
		<select class="form-control" id="insert-typedocument" name="document-type">
			<option>Aucun</option>
			<?php foreach($values['document_types'] as $row): ?>
			<option value="<?php echo $row->value;?>"><?php echo $row->value;?></option>
			<?php endforeach; ?>
		</select>
	</div>

	<!-- SUB FORM SOUS TYPE COMMERCIAL -->
	<div style="clear: both"></div>
	
	<div class="col-md-3 subform-soustype-commercial subform">
		<label>Sous-Type</label><br>
		<select class="form-control" id="insert-soustype-commercial" name="soustype-commercial">
			<option>Aucun</option>
			<option>Argumentaires</option>
			<option>Prix</option>
			<option>Quantites Boitauxlettres</option>
			<option>Pieces Diverses Client</option>
		</select>
	</div>					
	<!-- /.SUB FORM SOUS TYPE COMMERCIAL -->

	<!-- SUB FORM INFOGRAPHIE -->
	<div style="clear: both"></div>
	<div class="subform-infographie subform-documenttype" style="display: none">
		<div class="col-md-3 subform-client">
			<label>Client Name</label><br>
			<select class="form-control" id="insert-clientName" name="client">
				<option></option>
			</select>
		</div>
		<div class="col-md-3 submform-devis">
			<label>Devis</label><br>
			<select class="form-control" id="insert-devis" name="devis">
				<option></option>
			</select>
		</div>
		<div class="col-md-3 subform-facture">
			<label>Facture</label><br>
			<select class="form-control" id="insert-facture" name="facture">
				<option></option>
			</select>
		</div>
		<div style="clear: both"></div>
		
		<div class="col-md-3 subform-soustype-infographie">
			<label>Sous-Type</label><br>
			<select class="form-control" id="insert-soustype" name="sous-type">
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
		
		<div class="subform-format subform-soustype" style="display: none">
			<div class="col-md-3">
				<label>Format Ouvert</label><br>
				<input class="form-control" id="insert-format-ouvert" name="format-ouvert">
			</div>
			<div class="col-md-3">
				<label>Format Ferme</label><br>
				<input class="form-control" id="insert-format-ferme" name="format-ferme">
			</div>
			<div style="clear: both"></div>
		</div>
		
		<div class="subform-largeur-longueur subform-soustype" style="display: none">
			<div class="col-md-3">
				<label>Largeur</label><br>
				<input class="form-control" id="insert-largeur" name="largeur">
			</div>
			<div class="col-md-3">
				<label>Longueur</label><br>
				<input class="form-control" id="insert-longueur" name="longueur">
			</div>
			<div class="col-md-3 nombre-pages" style="display: none">
				<label>Nombre de Pages</label><br>
				<input class="form-control" id="insert-nombre-pages" name="nombre-pages">
			</div>
			<div style="clear: both"></div>
		</div>		

		<div class="subform-emailing subform-soustype" style="display: none">
			<div class="col-md-3">
				<label>CAMPAGNE</label><br>
				<input class="form-control" id="insert-campagne" name="campagne">
			</div>
			<div class="col-md-3">
				<label>OBJET</label><br>
				<input class="form-control" id="insert-objet" name="objet">
			</div>
			<div style="clear: both"></div>
		</div>

		<div class="subform-siteinternet subform-soustype" style="display: none">
			<div class="col-md-3">
				<label>NOM DE DOMAINE</label><br>
				<input class="form-control" id="insert-nomde-domaine" name="nomde-domaine">
			</div>			
			<div style="clear: both"></div>
		</div>

		<div class="subform-photo subform-soustype" style="display: none">
			<div class="col-md-3">
				<label>ORIGINE</label><br>
				<input class="form-control" id="insert-origine" name="origine">
			</div>
			<div class="col-md-3">
				<label>SUJET</label><br>
				<input class="form-control" id="insert-sujet" name="sujet">
			</div>
			<div style="clear: both"></div>
		</div>

	</div>
	<!-- /.SUB FORM INFOGRAPHIE -->

	<!-- SUB FORM PLAN -->
	<div style="clear: both"></div>
	
	<div class="col-md-3 subform-soustype-plan subform">
		<label>Sous-Type</label><br>
		<select class="form-control" id="insert-soustype-plan" name="soustype-plan">
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
		<select class="form-control" id="insert-soustype-piece-comptable" name="soustype-piece-comptable">
			<option>Aucun</option>
			<option>Facture Fournisseur</option>
			<option>Releve De Compte</option>
			<option>Autre</option>
		</select>
	</div>					
	<!-- /.SUB FORM PIECE COMPTABLE -->

	<!-- SUB FORM SITE INTERNET -->
	<div style="clear: both"></div>
	
	<div class="col-md-3 subform-soustype-site-internet subform">
		<label>Sous-Type</label><br>
		<select class="form-control" id="insert-soustype-site-internet" name="soustype-site-internet">
			<option>Aucun</option>
			<option>Fichiers</option>
			<option>Texte</option>
			<option>Photos</option>
			<option>Referencement</option>
			<option>Autres</option>
		</select>
	</div>					
	<!-- /.SUB FORM SITE INTERNET -->

	<!-- SUB FORM ADMINISTRATIFS DIVERS -->
	<div style="clear: both"></div>
	
	<div class="col-md-3 subform-soustype-administratifs-divers subform">
		<label>Sous-Type</label><br>
		<select class="form-control" id="insert-soustype-administratifs-divers" name="soustype-administratifs-divers">
			<option>Aucun</option>
			<option>Caisses,Cotisation</option>
			<option>Statuts Assemblees</option>
			<option>Sous-traitants</option>
			<option>Divers</option>
		</select>
	</div>					
	<!-- /.SUB FORM ADMINISTRATIFS DIVERS -->

	<!-- SUB FORM SOUS TYPE DEVELOPPMENT -->
	<div style="clear: both"></div>
	
	<div class="col-md-3 subform-soustype-development subform">
		<label>Sous-Type</label><br>
		<select class="form-control" id="insert-soustype-development" name="soustype-development">
			<option>Aucun</option>
			<option>Texte</option>
			<option>Visuels</option>
			<option>Code</option>
			<option>Divers</option>
		</select>
	</div>					
	<!-- /.SUB FORM SOUS TYPE DEVELOPPMENT -->	

	<!-- SUB FORM SOUS TYPE RESOURCE HUMAINES -->
	<div style="clear: both"></div>
	
	<div class="col-md-3 subform-soustype-ressources-humaines subform">
		<label>Sous-Type</label><br>
		<select class="form-control" id="insert-soustype-ressources-humaines" name="soustype-ressources-humaines">
			<option>Aucun</option>
			<option>Contrats De Travail</option>
			<option>Pieces Salarie</option>
			<option>Recrutement</option>
			<option>Objectifs Salaries</option>
			<option>Compte-rendu</option>
			<option>Licenciement</option>
			<option>Commissions</option>
			<option>Objectifs,Reunions</option>
			<option>Divers</option>
		</select>
	</div>					
	<!-- /.SUB FORM SOUS TYPE RESOURCE HUMAINES -->

	<!-- SUB FORM GENERAL -->
	<div style="clear: both"></div>

	<div class="col-md-3 subform-ville subform">
		<label>Ville</label><br>
		<input class="form-control" id="insert-ville" name="ville">
	</div>
	<div class="col-md-3 subform-numero subform">
		<label>Numero</label><br>
		<input class="form-control" id="insert-numero" name="numero">
	</div>
	<div class="col-md-3 subform-mois subform">
		<label>Mois</label><br>
		<input class="form-control" id="insert-mois" name="mois">
	</div>
	<div class="col-md-3 subform-societe subform">
		<label>Societe</label><br>
		<input class="form-control" id="insert-societe" name="societe">
	</div>
	<div class="col-md-3 subform-banque subform">
		<label>Banque</label><br>
		<input class="form-control" id="insert-banque" name="banque">
	</div>
	<div class="col-md-3 subform-origine subform">
		<label>Origine</label><br>
		<input class="form-control" id="insert-origine-general" name="origine-general">
	</div>
	<div class="col-md-3 subform-sujet subform">
		<label>Sujet</label><br>
		<input class="form-control" id="insert-sujet-general" name="sujet-general">
	</div>
	<div class="col-md-3 subform-nom subform">
		<label>Nom</label><br>
		<input class="form-control" id="insert-nom" name="nom">
	</div>
	<div class="col-md-3 subform-statuts subform">
		<label>Statuts</label><br>
		<input class="form-control" id="insert-statuts" name="statuts">
	</div>
	<div class="col-md-3 subform-assemblees subform">
		<label>Assemblees</label><br>
		<input class="form-control" id="insert-assemblees" name="assemblees">
	</div>
	<div class="col-md-3 subform-descriptif subform">
		<label>Descriptif</label><br>
		<textarea class="form-control" id="insert-descriptif" rows="3" name="descriptif"></textarea>
	</div>
	<div class="col-md-3 subform-piece subform">
		<label>Piece</label><br>
		<select class="form-control" id="insert-piece" name="piece">
			<option>Aucun</option>
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
	<div class="col-md-3 subform-message subform">
		<label>Message</label><br>
		<select class="form-control" id="insert-message" name="message">
			<option>Aucun</option>		
			<?php foreach($values['messages'] as $row):?>
			<option value="<?php echo $row->message_list_id;?>"><?php echo $row->name;?></option>	
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-md-3 subform-bass-de-donnee subform">
		<label>Bass de donnee</label><br>
		<select class="form-control" id="insert-bass-de-donnee" name="bass-de-donnee">
			<option></option>
		</select>
	</div>
	<div class="col-md-3 subform-salarie subform">
		<label>Salarie</label><br>
		<input class="form-control" id="insert-salarie" name="salarie">
	</div>
	<div class="col-md-3 subform-annee subform">
		<label>Annee</label><br>
		<input class="form-control" id="insert-annee" name="annee">
	</div>
	<div class="col-md-3 subform-recruitment-type subform">
		<label>Recruitment Type</label><br>
		<select class="form-control" id="insert-recruitment-type" name="recruitment-type">
			<option>Aucun</option>
			<option>CV</option>
			<option>Tests</option>
		</select>
	</div>
	<div class="col-md-3 subform-article subform">
		<label>Article</label><br>
		<input class="form-control" id="insert-article" name="article">
	</div>
	<div class="col-md-3 subform-departement subform">
		<label>Departement</label><br>
		<input class="form-control" id="insert-departement" name="departement">
	</div>
	<div class="col-md-3 subform-statuts-assemblees-type subform">
		<label>Statuts assemblees</label><br>
		<select class="form-control" id="insert-statuts-assemblees-type" name="statuts-assemblees-type">
			<option>Aucun</option>
			<option>Statuts</option>
			<option>Aprobation Des Comptes</option>
			<option>Constitution</option>
			<option>Rapport Gerance</option>
			<option>Etraordinaire</option>
		</select>
	</div>
	<div class="col-md-3 subform-sous-traitants-type subform">
		<label>Sous Traitants Type</label><br>
		<select class="form-control" id="insert-sous-traitants-type" name="sous-traitants-type">
			<option>Aucun</option>
			<option>Contrats</option>
			<option>Kbis</option>
			<option>Attestations Organisme</option>
		</select>
	</div>
	<div class="col-md-3 subform-nom-organisme subform">
		<label>Nom Organisme</label><br>
		<input class="form-control" id="insert-nom-organisme" name="nom-organisme">
	</div>
	<!-- ./SUB FORM GENERAL -->
	
	<div style="clear: both"></div><br>
	<input type='file' name='file[]' multiple='multiple' style="display: none;" id="files" />
	<div>
		<button type="button" class="btn btn-primary do-upload">Upload File</button>
	</div>	
</form>
<!-- EOF FORM INSERT -->
    </div>
</div>
