<?php
	$view_mode 		= $this->uri->segment(5);
	$controle_name	= $this->uri->segment(4);
	$client_id		= $this->session->id;	
	$controle_mode	= $this->uri->segment(2);
?>
<style>
	.nav-pills-custom{
		float: right;		
		margin-top: -40px;
	}
	.nav-pills-custom span{
		line-height: 18px
	}
	.nav-pills-custom li a{		
		padding: 3px 8px;
	}
	.toolbar-container{
		border: 1px solid grey; 
		padding: 10px;
		margin-bottom: 5px;

	}
	.context-title{
		color: \#337ab7;
		font-size: 15px;
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
<div class="toolbar-container">
	<div class="group-title">
		<b>GESTION DES CONTROLES</b>
		
		<?php if($controle_mode != null): ?> 
			<span class="context-title">
				<b>VOUS MODIFIEZ LE CONTROLE <?php echo strtoupper($controle_mode);?></b><br>
				<i><?php echo strtoupper($controle_name);?></i>
			</span>
		<?php endif; ?>
	</div>
	<br>	
</div>

<div class="toolbar-container">
	<div>
		<span><b>GESTION DES ADRESSES</b></span>		
	</div>
	<br>
</div>
<div class="toolbar-container">
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
</div>
