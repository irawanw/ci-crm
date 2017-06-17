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
    padding: 3px
  }
  .context-title{
    text-align: center;
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
  #barre-action-group {
    margin-right: 15px;
  }
  #barre-action-group li {
    background-color: #fff;
    border: 1px solid #000;
    border-radius: 5px;
  }
</style>

<?php
$view_mode     = $this->uri->segment(4);
$controle_mode = $this->uri->segment(2);
$group_name    = $this->uri->segment(3);


if($group_name == "") {
  $is_disabled = "disabled";
} else {
  $is_disabled = "";
}

$profil = $this->session->profil;
?>

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
      <option <?php echo $selected; ?> value="<?php echo site_url('feuille_controle'); ?>">En cours</option>
      <?php     
        $selected = '';
        if($this->uri->segment(2) == 'archiver')
          $selected = ' selected ';   
      ?>
      <option <?php echo $selected; ?> value="<?php echo site_url('feuille_controle/archiver');?>">Archivées</option>
      <?php     
        $selected = '';
        if($this->uri->segment(2) == 'deleted')
          $selected = ' selected ';   
      ?>
      <option <?php echo $selected; ?> value="<?php echo site_url('feuille_controle/deleted');?>">Supprimées</option>

      <?php
        $selected = '';
        if($this->uri->segment(2) == 'all')
          $selected = ' selected ';   
      ?>
      <option <?php echo $selected; ?> value="<?php echo site_url('feuille_controle/all');?>">Tout</option>
    </select>
  </div>
  <div style="clear: both"></div>
</div>
<?php endif; ?>

<?php if($group_name != "" && $controle_mode == "group"): ?>

<div class="toolbar-container">  
	<?php if($controle_mode != null): ?> 
		<div class="context-title">
		<?php if($group_valid == 0):?>
			<b>VOUS MODIFIEZ LE CONTROLE DISTRIBUTION</b><br>
		<?php else:?>
			<b>VOUS CONSULTEZ LE CONTROLE DISTRIBUTION</b><br>
		<?php endif; ?>
		<i><?php echo strtoupper($group_name);?></i>
		</div>
	<?php endif; ?>

  <div style="clear: both"></div>
</div>
<div style="clear: both"></div>
<div class="toolbar-container">
  <div>
    <span><b>GESTION DES ADRESSES</b></span>
  </div>
  <?php if($group_valid == 0 && $profil != 'Client'):?>
  <div class="form-group">
    <label for="action">Actions de masse</label>
    <select class="form-control input-sm" id="sel_action_all">
      <option value="remove">Supprimer une adresse</option>
      <option value="archiver">Archiver une adresse</option>
      <option value="unremove">Ré-intégrer une adresse</option>
    </select>
    <button type="button" class="btn btn-default btn-xs" id="btn_action_all">Ok</button>
    &nbsp;&nbsp;&nbsp;&nbsp;
  </div>
  
  <div class="form-group">
    <label for="action">Vue</label>
    <select class="form-control input-sm" id="sel_view" <?php echo $is_disabled; ?>>
      <option value="">[Select]</option>
      <?php
      $selected = '';
      if ($view_mode == '') {
          $selected = ' selected ';
      }

      $adresse_en_cours = site_url('feuille_controle') . '/' . $controle_mode . '/' . $group_name;
      ?>
      <option <?php echo $selected; ?>
        value="<?php echo $adresse_en_cours;?>">
        Adresses en cours
      </option>
      <?php
      $selected = '';
      if ($view_mode == 'deleted') {
          $selected = ' selected ';
      }

      $adresse_archiver = site_url('feuille_controle/'.$controle_mode.'/'.$group_name.'/archived');

      ?>
      <option <?php echo $selected; ?>
        value="<?php echo $adresse_archiver;?>">
        Adresses archivées
      </option>
      <?php
      $selected = '';
      if ($view_mode == 'deleted') {
          $selected = ' selected ';
      }

      $adresse_supprimees = site_url('feuille_controle/'.$controle_mode.'/'.$group_name.'/deleted');

      ?>
      <option <?php echo $selected; ?>
        value="<?php echo $adresse_supprimees;?>">
        Adresses supprimées
      </option>

      <?php
      $selected = '';
      if ($view_mode == 'all') {
          $selected = ' selected ';
      }

      $tout = site_url('feuille_controle/'.$controle_mode.'/'.$group_name.'/all');

      ?>
      <option <?php echo $selected; ?>
        value="<?php echo $tout; ?>">
        Tout
      </option>
    </select>
  </div>
  <?php endif;?>

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

  <!-- <ul class="nav nav-pills nav-pills-custom btn-single-action">
    <?php if($group_valid == 0 && $profil != 'Client'):?>
    <li class="text-center">
      <a href="<?php echo site_url('feuille_controle') . '/nouveau/'. $group_name; ?>" class="btn btn-default btn-xs">
        <span>Ajouter<br>une adresse</span>
      </a>
    </li>
    <li class="text-center disabled" id="feuille_controle_modification">
      <a href="#" data-cible="<?php echo site_url('feuille_controle') . '/modification'; ?>" class="btn btn-default btn-xs">
        <span>Consulter/Modifier<br>une adresse</span>
      </a>
    </li>
    <li class="text-center disabled" id="feuille_controle_supprimer">
      <a href="#" data-cible="<?php echo site_url('feuille_controle') . '/remove'; ?>" class="btn btn-default btn-xs">
        <span>Supprimer<br>une adresse</span>
      </a>
    </li>
    <?php endif;?>

    <li class="text-center" id="feuille_controle_voir_liste">
      <a href="" data-cible="" class="btn btn-default btn-xs">
        <span>Voir la liste<br>complete des adresse</span>
      </a>
    </li>
  </ul> -->

  <div style="clear: both"></div>
</div>
<?php endif; ?>

<!-- Modal Form Create Controle Distribution -->
<div id="modal-form-controle-distribution" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Creer un Controle Distribution</h4>
      </div>
       <form class="form-horizontal" method="post" action="<?php echo site_url('feuille_controle').'/set_controle_distribution'; ?>">
      <div class="modal-body">
            <div class="form-group">
              <label class="col-md-2">Date du Controle</label>
              <div class="col-md-10">
                <input type="text" name="controle_distribution_date" id="controle_distribution_date" class="form-control" required />
              </div>  
            </div>
            <div class="form-group">
              <label class="col-md-2">Heure de debut</label>
              <div class="col-md-10">
                <input type="text" name="controle_distribution_heure_de_debut" id="controle_distribution_heure_de_debut" class="form-control" required />
              </div>  
            </div>
            <div class="form-group">
              <label class="col-md-2">Heure de fin</label>
              <div class="col-md-10">
                <input type="text" name="controle_distribution_heure_de_fin" id="controle_distribution_heure_de_fin" class="form-control" required />
              </div>  
            </div>
            <div class="form-group">
              <label class="col-md-2">Controleur</label>
              <div class="col-md-10">
                <select class="form-control" name="controle_distribution_controleur" id="controle_distribution_controleur" required>
                  <option value='' selected='selected'>(choisissez)</option>
                </select>
              </div>
            </div>	
            <div class="form-group">
              <label class="col-md-2">Client</label>
              <div class="col-md-10">
                <select class="form-control" name="controle_distribution_client" id="controle_distribution_client" required>
                  <option value='' selected='selected'>(choisissez)</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2">Devis</label>
              <div class="col-md-10">
                <select class="form-control" name="controle_distribution_devis" id="controle_distribution_devis" required>
                  <option value='' selected='selected'>(choisissez)</option>
                  <option value=-1 >aucun</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2">Facture</label>
              <div class="col-md-10">
                <select class="form-control" name="controle_distribution_facture" id="controle_distribution_facture" required>
                  <option value='' selected='selected'>(choisissez)</option>
                  <option value=-1 >aucune</option>
                </select>
              </div>
            </div>		
            <div class="form-group">
              <label class="col-md-2">Controle Distribution Name</label>      
              <div class="col-md-10">        
                <input type="text" name="controle_distribution_name" id="controle_distribution_name" class="form-control" readonly />   
              </div>         
            </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <button type="submit" id="btn-submit" class="btn btn-primary">Submit</button>
      </div>
      </form>
    </div>
  
  </div>
</div>
<!-- Eof Modal Form Create Controle Distribution -->

<!-- Modal Form Valider Controle Distribution -->
<div id="modal-form-valider-controle-distribution" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation Valider Contrôle de Distribution</h4>
      </div>      
      <div class="modal-body">
            <div class="form-group">
                 Voulez-vous valider Contrôle de Distribution?
            </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
        <a href="<?php echo site_url('feuille_controle').'/set_valider/'.$group_name; ?>" class="btn btn-success">Oui</a>
      </div>
    </div>
  
  </div>
</div>
<!-- Eof Modal Form Valider Controle Distribution -->

<!-- Modal Form Devalider Controle Distribution -->
<div id="modal-form-devalider-controle-distribution" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation Devalider Contrôle de Distribution</h4>
      </div>    
      <div class="modal-body">
            <div class="form-group">
                 Voulez-vous devalider Contrôle de Distribution?
            </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
        <a href="<?php echo site_url('feuille_controle').'/unset_valider/'.$group_name; ?>" class="btn btn-success">Oui</a>
      </div>    
    </div>
  
  </div>
</div>
<!-- Eof Modal Form DeValider Controle Distribution -->

<!-- Modal Form Upload File -->
<div id="modal-form-upload" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Form Upload File</h4>
      </div>
      <form enctype="multipart/form-data" method="post" action="<?php echo site_url('factures_compta/upload_facture');?>">
      <div class="modal-body">

          <input type="file" name="facture" id="facture" required="">
          <input type="hidden" name="id" id="upload_id" value="0">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input class="btn btn-primary" type="submit" value="Submit">
      </div>
      </form>
    </div>
  </div>
</div>
<!-- Eof Modal Form Upload File -->

<!-- Modal Form Confirmation Delete Group -->
<div id="modal-form-remove-group" class="modal fade" role="dialog">
  <div class="modal-dialog">  
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

<?php 
//load confirmation mass remove box
$this->load->view('templates/remove_confirmation.php'); 
?>