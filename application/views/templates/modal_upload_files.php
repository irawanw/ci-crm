<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/dropzone.min.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/basic.min.css') ?>">

<style type="text/css">
  .dropzone {
  margin-top: 10px;
  border: 2px dashed #0087F7;
  height: 400px;
}
</style>

<!-- Modal Form Upload File -->
<div id="modal-form-upload" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Form Upload File</h4>
      </div>
			<div class="modal-body">
					<input type="hidden" name="id" id="upload_id" value="0">
					<div class="dropzone">
					  <div class="dz-message">
					   <h3> Click and Drop file here</h3>
					  </div>
					</div>
      </div>
      <div class="modal-footer">
       <!--  <button type="button" class="btn btn-default clear-dropzone" data-dismiss="modal">Close</button> -->
		    <button class="btn btn-primary" type="button" id="btn-upload-ok">OK</button>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal Form Upload File -->
<!-- Modal Form Remove File -->
<div id="modal-form-remove-upload-file" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation remove file</h4>
      </div>
      <div class="modal-body">
          <p>Etes-vous certain de vouloir supprimer le fichier?</p>
          <input type="hidden" name="id" id="file_id" value="0">  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
        <button class="btn btn-warning" type="button" id="btn-remove-ok">Oui</button>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal Form Remove File -->
<!-- Modal Form Remove File -->

<!-- /.Modal Form Remove File -->