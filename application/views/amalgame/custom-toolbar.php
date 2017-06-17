<?php 
//url structure definition
$view_mode		= $this->uri->segment(4);
$amalgame_name	= $this->uri->segment(3);
	
//set valider name as current date
$valider_name = date('d-F-Y');

//when we are on nonvalides list then 
//set valides name to its own name without NV
if(preg_match('/-NV/', current_url())){
	$valider_name = substr($amalgame_name, 0, -3);
}	

$viewurl = site_url('amalgame').'/group/'.$amalgame_name;

/*
if($this->uri->segment(2) == 'valider'){
	$viewurl = site_url('amalgame').'/valider/'.$amalgame_name;
} else {
	$viewurl = site_url('amalgame');
}
*/
?>
<style>
	.toolbar-container{
		border: 1px solid grey;
		padding: 5px;
		margin-bottom: 5px;
	}
	.context-title{
		float: right;
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
	.nav-pills-custom{
		float: right;		
		margin-top: 5px;
	}
	.nav-pills-custom span{
		line-height: 18px
	}
	.nav-pills-custom li a{		
		padding: 3px 8px;
	}
	#sel_action_all, 
	#sel_view{
		width: 100px;
	}
	#datatable tfoot th{
	    padding: 0px;
		font-size: 11px;
		background-color: #9a9a9a;
		color: white;
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
			<option <?php echo $selected; ?> value="<?php echo site_url('amalgame'); ?>">En cours</option>
			<?php			
				$selected = '';
				if($this->uri->segment(2) == 'archiver')
					$selected = ' selected ';		
			?>
			<option <?php echo $selected; ?> value="<?php echo site_url('amalgame/archiver');?>">Archivées</option>
			<?php			
				$selected = '';
				if($this->uri->segment(2) == 'deleted')
					$selected = ' selected ';		
			?>
			<option <?php echo $selected; ?> value="<?php echo site_url('amalgame/deleted');?>">Supprimées</option>

			<?php
				$selected = '';
				if($this->uri->segment(2) == 'all')
					$selected = ' selected ';		
			?>
			<option <?php echo $selected; ?> value="<?php echo site_url('amalgame/all');?>">Tout</option>
		</select>
	</div>
	<div style="clear: both"></div>
</div>
<?php endif; ?>

<?php if($this->uri->segment(2) == 'group') { ?>
<div class="toolbar-container">
	<div id="group-title">
	<?php if($group_valid == 0):?>
		<b>VOUS MODIFIEZ L'AMALGAME</b><br>
	<?php else: ?>
		<b>VOUS CONSULTEZ L'AMALGAME</b><br>
	<?php endif; ?>
		<i>
			<?php 				
				echo $amalgame_name; 
				if($group_valid)
					echo ' VALIDE';
				else
					echo ' NON VALIDE';
			?>
		</i>
	</div>
</div>
<div class="toolbar-container">
	<?php if($group_valid == 0):?>
	<div class="form-group">
		<label for="action">Actions de masse</label>
		<select class="form-control input-sm" id="sel_action_all">			
			<option value="remove">Supprimer</option>
			<option value="archiver">Archiver</option>			
			<option value="unremove">re-integrer</option>
		</select>
		<button type="button" class="btn btn-default btn-xs" id="btn_action_all">Ok</button>
	</div>	
	<div class="form-group">
		<label for="action">Vue</label>
		<select class="form-control input-sm" id="sel_view">
			<option value="">[Select]</option>
			<?php		
				$selected = '';
				if($view_mode == '')
					$selected = ' selected ';	
			?>		
			<option <?php echo $selected; ?> value="<?php echo $viewurl; ?>">En cours</option>
			<?php			
				$selected = '';
				if($view_mode == 'archiver')
					$selected = ' selected ';		
			?>
			<option <?php echo $selected; ?> value="<?php echo $viewurl;?>/archiver">Archivées</option>
			<?php			
				$selected = '';
				if($view_mode == 'deleted')
					$selected = ' selected ';		
			?>
			<option <?php echo $selected; ?> value="<?php echo $viewurl; ?>/deleted">Supprimées</option>
			<?php
				$selected = '';
				if($view_mode == 'all')
					$selected = ' selected ';		
			?>
			<option <?php echo $selected; ?> value="<?php echo $viewurl; ?>/all">Tout</option>
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
<?php } ?>

<!--
	<div class="form-group" style="position: absolute; top: 90px; left: 650px;">
		<label for="action" style="width: 220px">Voir la liste des amalgames validés</label>
		<select class="form-control input-sm" id="valider_view">
			<option value="">(choisissez)</option>
			<?php				
			if(is_array($list_valides)){
				foreach($list_valides as $row){				
					$selected = ''; 
					if( preg_match('/'.$row.'/', urldecode(current_url())) )
						$selected = ' selected ';
					echo '<option '.$selected.' value="'.site_url('amalgame').'/group/'.$row.'">'.$row.'</option>';
				}
			}
			?>					
		</select>
	</div>
	<div class="form-group" style="position: absolute; top: 125px; left: 650px;">
		<label for="action" style="width: 220px">Voir la liste des amalgames non validés</label>
		<select class="form-control input-sm" id="non_valider_view">
			<option value="">(choisissez)</option>';
			<?php
			if(is_array($list_non_valides)){
				foreach($list_non_valides as $row){				
					$selected = ''; 
					if( preg_match('/'.$row.'/', urldecode(current_url())) )
						$selected = ' selected ';
					echo '<option '.$selected.' value="'.site_url('amalgame').'/group/'.$row.'">'.$row.'</option>';
				}
			}; 
			?>					
		</select>
	</div>
-->
	

<div class="valider" style="display: none">
	<form enctype="multipart/form-data" method="post" action="<?php echo site_url('amalgame').'/set_valider'; ?>">
		<input name="old_valider_name" id="old_valider_name" value="<?php echo $amalgame_name; ?>" type="hidden">
		<input name="valider_name" id="valider_name" value="<?php echo $valider_name; ?>">
		<br><br>
		<input class="btn btn-primary" type="submit" value="OK">
	</form>
</div>
<div class="modal fade" id="popup_amalgame_valider" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Confirmation Valider l'Amalgame</h4>
            </div>
            <div class="modal-body">
                Voulez-vous valider l'amalgame?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-success" id="popup_amalgame_valider_do">Oui</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="popup_amalgame_devalider" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Confirmation Devalider l'Amalgame</h4>
            </div>
            <div class="modal-body">
                Êtes-vous sûr
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal" id="popup_amalgame_devalider_do">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="popup_amalgame_ajouter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Ajouter un Amalgame</h4>
            </div>
			<form class="form-horizontal" method="post" action="<?php echo site_url('amalgame').'/set_group'; ?>">
				<div class="modal-body">
					<div class="form-group col-md-12">
						<label>Date de livraison de l'amalgame</label><br>
						<input type="text" name="date_de_livraison_del_amalgame" id="date_de_livraison_del_amalgame" class="form-control" required />
					</div>
					<br>
					<div class="form-group col-md-12">
						<label>Date envoi BAT global</label>
						<input type="text" name="date_envoi_bat_global" id="date_envoi_bat_global" class="form-control" required />
					</div>
					<div class="form-group col-md-12">
						<label>date livraison réelle</label>
						<input type="text" name="date_livraison_reelle" id="date_livraison_reelle" class="form-control" required />
					</div>
					<div class="form-group col-md-12">
						<label>Amalgame Nom</label>
						<input type="text" name="amalgame_name" id="amalgame_name" class="form-control" required />
					</div>					
					<div style="clear: both"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
					<input type="submit" class="btn btn-warning" id="popup_amalgame_devalider_do" value="OK">
					<div style="clear: both"></div>
				</div>				
			</form>
        </div>
    </div>
</div>

<div class="modal fade" id="popup_liste_amalgame" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Ajouter un Amalgame</h4>
            </div>
			<form class="form-horizontal" method="post" action="">
				<div class="modal-body">
					<div class="form-group col-md-12">
						<label>Date de livraison réelle</label>
						<input type="text" name="date_livraison_reelle_filter" id="date_livraison_reelle_filter" class="form-control" required />
					</div>				
					<div style="clear: both"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
					<input type="submit" class="btn btn-warning" id="popup_liste_amalgame_do" value="OK">
					<div style="clear: both"></div>
				</div>				
			</form>
        </div>
    </div>
</div>

<!-- Modal Form Confirmation Delete -->
<div id="modal-form-remove" class="modal fade" role="dialog">
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
		<button onclick="massActionGroup()" id="btn-remove-group" class="btn btn-warning">Oui</button>
      </div>
    </div>
  </div>
</div>

<?php 
//load confirmation mass remove box
$this->load->view('templates/remove_confirmation.php'); 
$this->load->view('templates/modal_upload_files.php'); 
?>