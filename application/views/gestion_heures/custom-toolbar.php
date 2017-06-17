<?php
	$gest_mode			= $this->uri->segment(2);
	$gest_employe_id	= $this->uri->segment(3);
	$gest_year			= $this->uri->segment(4);
	$gest_month			= $this->uri->segment(5);
	$view_mode 			= $this->uri->segment(6);
	
	$annee	= 	array( 
					"toutes","2016","2017","2018","2019","2020",
				);
	$mois	= 	array(
					"tous"   => "tous", 
					1 	=> "01",
					2	=> "02",
					3 	=> "03",
					4 	=> "04",
					5 	=> "05",
					6 	=> "06",
					7	=> "07",
					8 	=> "08",
					9 	=> "09",
					10 	=> "10",
					11 	=> "11",
					12 	=> "12",
				);
	
	$selected = '';
?>
<style>
	.nav-pills-custom{
		float: right;		
		margin-top: 0px;
	}
	.nav-pills-custom span{
		line-height: 18px
	}
	.nav-pills-custom li a{		
		padding: 3px 8px;
	}
	.toolbar-container{
		border: 1px solid grey;
		padding: 5px;
		margin-bottom: 5px;
	}
	.context-title{
		color: \#337ab7;
		font-size: 15px;
	}
	.toolbar-container .form-group{
		margin-top: 0px;
		margin-bottom: 5px;
		font-size: 11px;
		float: left;
	}
	.toolbar-container .form-group label{
		float: left;
		margin-top: 7px;
		margin-right: 5px;
	}
	.toolbar-container .form-group .btn{
		float: left;
		margin-left: -5px;
		height: 30px;
	}
	.toolbar-container .form-group select{
		width: 110px;
		float: left;
		margin-right: 10px;
	}
	.toolbar-container .form-group input{
		width: 70%;
		float: left;
	}
	#sel_action_all, 
	#sel_view{
		width: 175px;
	}
	#datatable tfoot th{
	    padding: 0px;
		font-size: 11px;
		background-color: #9a9a9a;
		color: white;
	}
	.form-custom{
		margin-top: 5px;
		margin-bottom: 5px;
		font-size: 11px;
		float: left;
		padding: 0 5px;
	}
	.form-custom label{
		float: left;
		margin-top: 7px;
		margin-right: 5px;
	}
	.form-custom .btn{
		float: left;
		margin-left: 5px;
		height: 30px;
	}
	.form-custom select{
		width: 160px;
		float: left;
	}
	.form-custom input{
		width: 70%;
		float: left;
	}
	#group-title{
		font-size: 15px;
		text-align: center;
	}	
	#barre-action-group {
		margin-right: 15px;
	}
	#barre-action-group li {
	    background-color: #fff;
	    border: 1px solid #000;
	    border-radius: 5px;
	}
	#input_urbain_div {
	    position: absolute;
	    width: 70px;
	    left: 500px;
	    margin-top: 55px;
	    height: 22px;
	    z-index: 1;
	}
	#input_rural_div {
		position: absolute;
	    width: 70px;
	    left: 740px;
	    margin-top: 55px;
	    height: 22px;
	    z-index: 1;
	}
</style>

<?php if(isset($is_liste_group)): ?>
<div class="toolbar-container">
  <div class="form-group">
    <label for="action">Actions de masse</label>
    <select class="form-control input-sm" id="sel_action_all_group">      
      <option value="remove_group">Supprimer</option>
      <option value="archiver_group">Archiver</option>     
      <option value="unremove_group">re-integrer</option>
    </select>
    <button type="button" class="btn btn-default btn-xs" id="btn_action_all_group">Ok</button>
  </div>  
  <div class="form-group">
    <label for="action">Vue</label>
    <select class="form-control input-sm" id="sel_view">
      <option value="">[Select]</option>
      <?php   
        $selected = '';
        if($this->uri->segment(2) == '')
          $selected = ' selected '; 
      ?>    
      <option <?php echo $selected; ?> value="<?php echo site_url('gestion_heures'); ?>">En cours</option>
      <?php     
        $selected = '';
        if($this->uri->segment(2) == 'archiver')
          $selected = ' selected ';   
      ?>
      <option <?php echo $selected; ?> value="<?php echo site_url('gestion_heures/archiver');?>">Archivées</option>
      <?php     
        $selected = '';
        if($this->uri->segment(2) == 'deleted')
          $selected = ' selected ';   
      ?>
      <option <?php echo $selected; ?> value="<?php echo site_url('gestion_heures/deleted');?>">Supprimées</option>

      <?php
        $selected = '';
        if($this->uri->segment(2) == 'all')
          $selected = ' selected ';   
      ?>
      <option <?php echo $selected; ?> value="<?php echo site_url('gestion_heures/all');?>">Tout</option>
    </select>
  </div>
  <div style="clear: both"></div>
</div>
<?php endif; ?>

<?php if($gest_mode == 'salarie'){ ?>
<div class="toolbar-container">
	<div id="group-title">
		<?php 
			if($gest_mode != null){
				$gest_month_label = $gest_month == "tous" ? "TOUS LES MOIS" : strtoupper(date("F",mktime(0,0,0,$gest_month,1,2011)));
				$gest_year_label = $gest_year == "toutes" ? "TOUTES LES ANNÉES" : $gest_year; 

				$group_valid = isset($group_valid)?$group_valid:'';
				if($group_valid) {
					echo '<span class="context-title"><b>VOUS CONSULTEZ LA FEUILLE D\'HEURES</b><br><i>'
						.strtoupper($emp_nom).' '.$gest_month_label.' '
						.$gest_year_label.' VALIDEE</i></span>';
				}
				else {
					echo '<span class="context-title"><b>VOUS MODIFIEZ LA FEUILLE D\'HEURES</b><br><i>'
						.strtoupper($emp_nom).' '.$gest_month_label.' '
						.$gest_year_label.' NON VALIDEE</i></span>';
				}
				
		} ?>
	</div>
	
	&nbsp;&nbsp;
	<div class="form-group" style="float: right">
		<label>indem kilo</label>
		<input type="search" class="form-control" name="indemnite_kilometrique" value="<?php echo $indemnite_kilometrique;?>" readonly style="width: 100px;" />
	</div>	
	<div style="clear: both"></div>
</div>
<div class="toolbar-container" id="gestion_des_lignes">
	<div>
		<span><b>GESTION DES LIGNES</b></span>		
	</div>
	
	<?php if($group_valid == 0): ?>
	<div class="form-group">
		<label for="action">Actions de masse</label>
		<select class="form-control input-sm" id="sel_action_all">
			<option value="remove">Supprimer une lignes</option>
			<option value="archiver">Archiver une lignes</option>
			<option value="unremove">Ré-intégrer une lignes</option>
		</select>
		<button type="button" class="btn btn-default btn-xs" id="btn_action_all">Ok</button>
		&nbsp;&nbsp;&nbsp;&nbsp;
	</div>	
	&nbsp;&nbsp;&nbsp;&nbsp;
	<div class="form-group">
		<label for="action">Vue</label>
		<select class="form-control input-sm" id="sel_view">
			<option value="">[Select]</option>
			<?php		
				$selected = '';
				if($view_mode == '')
					$selected = ' selected ';	
			?>		
			<option <?php echo $selected; ?> 
				value="<?php echo site_url('gestion_heures').'/salarie/'.$gest_employe_id.'/'.$gest_year.'/'.$gest_month; ?>">
				Lignes en cours
			</option>
			<?php			
				$selected = '';
				if($view_mode == 'archived')
					$selected = ' selected ';		
			?>
			<option <?php echo $selected; ?> 
				value="<?php echo site_url('gestion_heures').'/salarie/'.$gest_employe_id.'/'.$gest_year.'/'.$gest_month; ?>/archived">
				Lignes archivees
			</option>

			<?php			
				$selected = '';
				if($view_mode == 'deleted')
					$selected = ' selected ';		
			?>
			<option <?php echo $selected; ?> 
				value="<?php echo site_url('gestion_heures').'/salarie/'.$gest_employe_id.'/'.$gest_year.'/'.$gest_month; ?>/deleted">
				Lignes supprimées
			</option>

			<?php
				$selected = '';
				if($view_mode == 'all')
					$selected = ' selected ';		
			?>
			<option <?php echo $selected; ?> 
				value="<?php echo site_url('gestion_heures').'/salarie/'.$gest_employe_id.'/'.$gest_year.'/'.$gest_month; ?>/all">
				Tout
			</option>
		</select>
	</div>
	<?php endif; ?>
	
	<?php
		if (!empty($barre_action)) {
		    echo '<div class="pull-right" id="barre-action-group">';
		    if (!isset($profil)) {
		        $profil = $this->session->profil;
		        if (!isset($profil)) {
		            $profil = 'public';
		        }
		    }
	    
	    	$barre_action = filtre_barre_action_par_droits($barre_action, $droits, $profil);
	    	$this->load->view('templates/barre_action',array('barre'=>$barre_action));
	    	echo '</div>';
		}
	?>

	<div style="clear: both"></div>
</div>	
<div>
    <input type="search" class="form-control input-sm" id="input_urbain_div" value="<?php echo $urbain_div;?>" placeholder="" />
    <input type="search" class="form-control input-sm" id="input_rural_div" value="<?php echo $rural_div;?>" placeholder="" />
</div>
<?php } ?>

<!--
<div class="form-custom form-group" style="position: absolute; top: 90px; left: 960px;">
	<label for="action" style="width: 200px">Voir la liste des Gestion Heures validés</label>
	<select class="form-control input-sm" id="valider_view">
		<option value="">(choisissez)</option>
		<?php				
		if(is_array($list_valides)){
			foreach($list_valides as $row){						
				$label = $row->emp_nom."-".$row->annee."-".$row->mois;
				$value = site_url('gestion_heures/salarie/'.$row->employes.'/'.$row->annee.'/'.$row->mois);
				$selected = current_url() == $value ? "selected" : "";
				echo '<option '.$selected.' value="'.$value.'">'.$label.'</option>';
			}
		}
		?>				
	</select>
</div>
<div class="form-custom form-group" style="position: absolute; top: 125px; left: 960px;">
	<label for="action" style="width: 200px">Voir la liste des Gestion Heures non validés</label>
	<select class="form-control input-sm" id="non_valider_view">
		<option value="">(choisissez)</option>';				
		<?php				
		if(is_array($list_non_valides)){
			foreach($list_non_valides as $row){						
				$label = $row->emp_nom."-".$row->annee."-".$row->mois;
				$value = site_url('gestion_heures/salarie/'.$row->employes.'/'.$row->annee.'/'.$row->mois);
				$selected = current_url() == $value ? "selected" : "";
				echo '<option '.$selected.' value="'.$value.'">'.$label.'</option>';
			}
		}
		?>				
	</select>
</div>	
-->

<!-- Modal Tableau IK URSAFF -->
<div id="modal-tableau-ik-urssaf" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Voiture</h4>
      </div>
      <div class="modal-body">
      	<span><i>Montants au 1<sup>er</sup> janvier 2016</i></span>
        <table id="tableau-ik-urssaf" class="table table-striped table-bordered">
        	<thead>
        		<tr>
        			<th class="info"></th>
        			<th colspan="3" class="text-center info">Kilométrage parcouru à titre professionnel</th>
        		</tr>
        		<tr>
        			<th class="info">Puissance fiscale</th>
        			<th class="info">Jusqu'à 5 000 km</th>
        			<th class="info">De 5 001 à 20 000 km</th>
        			<th class="info">Au-delà de 20 000 km</th>
        		</tr>
        	</thead>
        	<tbody></tbody>
        </table>

        <span><i>d = distance parcourue à titre professionnel en km</i></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- EOfModal Tableau IK URSAFF -->

<!-- Modal Form Confirmation Delete Group -->
<div id="modal-form-remove-group" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation Delete</h4>
      </div>
      <div class="modal-body">
            <p>Etes-vous certain de vouloir supprimer le champ?</p>
          </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
    <button onclick="massActionGroup()" class="btn btn-warning">Oui</button>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal Form Confirmation Delete Group -->

<?php 
//load confirmation mass remove box
$this->load->view('templates/remove_confirmation.php'); 
?>