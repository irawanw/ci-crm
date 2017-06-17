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
      <option <?php echo $selected; ?> value="<?php echo site_url('controle_recurrents'); ?>">En cours</option>
      <?php     
        $selected = '';
        if($this->uri->segment(2) == 'archiver')
          $selected = ' selected ';   
      ?>
      <option <?php echo $selected; ?> value="<?php echo site_url('controle_recurrents/archiver');?>">Archivées</option>
      <?php     
        $selected = '';
        if($this->uri->segment(2) == 'deleted')
          $selected = ' selected ';   
      ?>
      <option <?php echo $selected; ?> value="<?php echo site_url('controle_recurrents/deleted');?>">Supprimées</option>

      <?php
        $selected = '';
        if($this->uri->segment(2) == 'all')
          $selected = ' selected ';   
      ?>
      <option <?php echo $selected; ?> value="<?php echo site_url('controle_recurrents/all');?>">Tout</option>
    </select>
  </div>
  <div style="clear: both"></div>
</div>
<?php endif; ?>

<!-- Modal Form Create Controle Permanent -->
<div id="modal-create-controle-permanent" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <form enctype="multipart/form-data" method="post" action="<?php echo site_url('controle_recurrents').'/set_controle_permanent'; ?>">
      <div class="modal-body">
            <div class="form-group">
				<label>Client</label>
				<select class="form-control" id="client" name="client" required>
					<option value="">(choisissez)</option>
					<?php
					if($list_client):
					foreach($list_client as $client):
					?>
					<option value="<?php echo $client->ctc_id;?>"><?php echo $client->ctc_nom;?></option>
					<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
			<div class="form-group">
				<label>Commande</label>
				<select class="form-control" id="commande" name="commande" required>
					<option value="">(choisissez)</option>
				</select>
			</div>
			<div class="form-group">
				<label>Controler Permanent Name</label>
				<input class="form-control" type="text" name="name" id="name" data-date="<?php echo date('d-F-Y');?>" required />
			</div>
			<input type="hidden" name="date" value="<?php echo date('Y-m-d');?>" id="date" />
			<input type="hidden" name="type" id="type" value="permanent" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <button type="submit" id="btn-submit" class="btn btn-primary">OK</button>
      </div>
      </form>
    </div>
  
  </div>
</div>
<!-- Eof Modal Form Create Controle Ponctuels -->

<!-- Modal Form Create Controle Ponctuels -->
<div id="modal-create-controle-ponctuels" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <form enctype="multipart/form-data" method="post" action="<?php echo site_url('controle_recurrents').'/set_controle_ponctuel'; ?>">
      <div class="modal-body">
            <div class="form-group">
				<label>Client</label>
				<select class="form-control" id="client" name="client" required>
					<option value="">(choisissez)</option>
					<?php
					if($list_client):
					foreach($list_client as $client):
					?>
					<option value="<?php echo $client->ctc_id;?>"><?php echo $client->ctc_nom;?></option>
					<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
			<div class="form-group">
				<label>Controle Permanent</label>
				<select class="form-control" id="controle_permanent" name="controle_permanent" required>
					<option value="">(choisissez)</option>
					<?php
					/*
					if($liste_permanent):
					foreach($liste_permanent as $permanent):
					?>
					<option value="<?php echo $permanent->group_id;?>"><?php echo $permanent->name;?></option>
					<?php endforeach; ?>
					<?php endif; 
					*/
					?>
				</select>
			</div>
			<div class="form-group">
				<label>Date Controle</label> <br />
				<input type="text" name="date_controle_ponctuel" id="date_controle_ponctuel" class="form-control" required />
			</div>
			<div class="form-group">
				<label>Type</label>
				<select class="form-control" name="type_ponctuel" id="type_ponctuel" required >
					<option value="">(choisissez)</option>
					<option value="appel">appel</option>
					<option value="terrain">terrain</option>
					<option value="mail">mail</option>
				</select>
			</div>			
			<div class="form-group">
				<label>Nom du Controle Ponctuels </label>
				<input class="form-control" type="text" name="name" id="name" data-date="<?php echo date('d-F-Y');?>" required />
			</div>
			<input type="hidden" name="date" value="<?php echo date('Y-m-d');?>" id="date" />
			<input type="hidden" name="type" id="type" value="ponctuel" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <button type="submit" id="btn-submit" class="btn btn-primary">OK</button>
      </div>
      </form>
    </div>
  
  </div>
</div>
<!-- Eof Modal Form Create Controle Ponctuels -->

<!-- Modal Form Valider Controle Distribution -->
<div id="modal-form-valider-controle" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation Valider Contrôle <?php echo $group->type;?></h4>
      </div>      
      <div class="modal-body">
            <div class="form-group">
                 Voulez-vous valider Contrôle <?php echo $group->type;?>?
            </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
        <a href="<?php echo site_url('controle_recurrents').'/set_valider/'.$group->type.'/'.$group->name; ?>" class="btn btn-success">Oui</a>
      </div>
    </div>
  
  </div>
</div>
<!-- Eof Modal Form Valider Controle Distribution -->

<!-- Modal Form DeValider Controle Distribution -->
<div id="modal-form-devalider-controle" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation Devalider Contrôle <?php echo $group->type;?></h4>
      </div>      
      <div class="modal-body">
            <div class="form-group">
                 Voulez-vous devalider Contrôle <?php echo $group->type;?>?
            </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
        <a href="<?php echo site_url('controle_recurrents').'/unset_valider/'.$group->type.'/'.$group->name; ?>" class="btn btn-success">Oui</a>
      </div>
    </div>
  
  </div>
</div>
<!-- Eof Modal Form DeValider Controle Distribution -->

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
    <button onclick="massActionGroup()" id="btn-remove" class="btn btn-warning">Oui</button>
      </div>
    </div>
  </div>
</div>