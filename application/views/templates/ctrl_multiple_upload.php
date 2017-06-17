<?php
$label = html_escape($label);
$oblig = $label;
if ($obligatoire) {
    $oblig = '* '.$label;
}?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/dropzone.min.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/basic.min.css') ?>">

<style type="text/css">
  .dropzone {
  margin-top: 10px;
  border: 2px dashed #0087F7;
  height: 200px;
}
</style>

<div class="row" style="margin-bottom: 15px">
    <div class="form-group">
        <label class="col-sm-3 control-label" for="<?php echo $controle?>"><?php echo $oblig;?></label>
        <div class="col-sm-9">
            <input type="hidden" name="<?php echo $controle?>" id="<?php echo $controle?>" />
            <div class="dropzone">
      				<div class="dz-message">
      				  <h3>Faites glisser le fichier ici</h3>
      				</div>
      			</div>
        </div>
    </div>
</div>

<!-- Modal Form Remove File -->
<div id="modal-form-remove-file" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation</h4>
      </div>
      <div class="modal-body">
          <p>Êtes-vous certain de vouloir supprimer le fichier&nbsp;?</p>
          <input type="hidden" name="file_id" id="file_id" value="0">         
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
        <button class="btn btn-warning" type="button" id="btn-remove-ok">Oui</button>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal Form Remove File -->

<script type="text/javascript" src="<?php echo base_url('assets/js/dropzone.min.js') ?>"></script>