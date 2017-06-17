<?php
	$view_mode 		= $this->uri->segment(5);
	$group_name		= $this->uri->segment(4);	
	$controle_mode	= $this->uri->segment(3);
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
		padding: 3px;
		margin-bottom:5px;
	}
	.toolbar-container .context-title{
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
		width: 160px;
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
<div style="clear: both"></div>
<div class="toolbar-container">
	<div id="group-title">
		<?php if($controle_mode != null): ?> 
			<?php $valid_mode = $group->valid == true ? "VALIDEE" : "NON VALIDEE"; ?>
			<span class="context-title">
				<?php if($group_valid == 0): ?>
				<b>VOUS MODIFIEZ LE CONTROLE <?php echo strtoupper($group->type);?></b><br>
				<?php else: ?>
				<b>VOUS CONSULTEZ LE CONTROLE <?php echo strtoupper($group->type);?></b><br>
				<?php endif; ?>
				<i><?php echo strtoupper($group_name)." ".$valid_mode;?></i>
			</span>
		<?php endif; ?>
	</div>

	<div style="clear: both"></div>
</div>
<div class="toolbar-container">
	<div>
		<span><b>GESTION DES ADRESSES</b></span>		
	</div>
	
	<?php if($group_valid == 0): ?>
	<div class="form-group">
		<label for="action">Actions de masse</label>
		<select class="form-control input-sm" id="sel_action_all">
			<option value="remove">Supprimer une adresse</option>
			<option value="unremove">Ré-intégrer une adresse</option>
			<option value="archiver">Archiver une adresse</option>
			<?php if($group->type == "ponctuel"):?>
			<option value="resultat_fait">résultat fait</option>
			<option value="resultat_pas_fait">résultat pas fait</option>
			<option value="resultat_non_controle">résultat non controlé</option>
			<?php endif; ?>
		</select>
		<button type="button" class="btn btn-default btn-xs" id="mass_action">Ok</button>
		&nbsp;&nbsp;&nbsp;&nbsp;
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
			<option <?php echo $selected; ?> 
				value="<?php echo site_url('controle_recurrents').'/group/'.$controle_mode.'/'.$group_name; ?>">
				Adresses en cours
			</option>
			<?php			
				$selected = '';
				if($view_mode == 'archiver')
					$selected = ' selected ';		
			?>
			<option <?php echo $selected; ?> 
				value="<?php echo site_url('controle_recurrents').'/group/'.$controle_mode.'/'.$group_name; ?>/archiver">
				Adresses archivées
			</option>
			<?php			
				$selected = '';
				if($view_mode == 'deleted')
					$selected = ' selected ';		
			?>
			<option <?php echo $selected; ?> 
				value="<?php echo site_url('controle_recurrents').'/group/'.$controle_mode.'/'.$group_name; ?>/deleted">
				Adresses supprimées
			</option>

			<?php
				$selected = '';
				if($view_mode == 'all')
					$selected = ' selected ';		
			?>
			<option <?php echo $selected; ?> 
				value="<?php echo site_url('controle_recurrents').'/group/'.$controle_mode.'/'.$group_name; ?>/all">
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
<?php 
//load confirmation mass remove box
$this->load->view('templates/remove_confirmation.php'); 
$this->load->view('controle_recurrents/custom-toolbar.php', array('group' => $group)); 
?>