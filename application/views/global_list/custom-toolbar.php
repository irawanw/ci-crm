<style>
	.nav-pills-custom{
		margin-top: 5px;
	}
	.nav-pills-custom span{
		line-height: 18px
	}
	.nav-pills-custom li a{		
		padding: 3px 8px;
	}
	.form-inline{
		float: left;
		margin-right: 20px;
	}
</style>

	
<ul class="nav nav-pills nav-pills-custom">
	<li class="text-center" id="view_commerciale">
		<a href="#" class="btn btn-default btn-xs">
			<span>Vue Commerciale</span>
		</a>
	</li>
	<li class="text-center" id="view_delivery">
		<a href="#" class="btn btn-default btn-xs">
			<span>Vue délivrabilité</span>
		</a>
	</li>
	<li class="text-center" id="view_synthetique">
		<a href="#" class="btn btn-default btn-xs">
			<span>Vue synthétique</span>
		</a>
	</li>
</ul>

<!-- Modal Form Upload File -->
<div id="modal-form-upload" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Form Upload File</h4>
      </div>
			<form enctype="multipart/form-data" method="post" action="<?php echo site_url('pages_jaunes/upload_message_sent');?>">
			<div class="modal-body">

					<input type="file" name="message_sent" id="message_sent" required="">
					<input type="hidden" name="upload_id" id="upload_id" value="0">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input class="btn btn-primary" type="submit" value="OK">
      </div>
			</form>
    </div>
  </div>
</div>
<!-- Eof Modal Form Upload File -->

<!-- Modal Form View Long Text -->
<div id="modal-form-view-text" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Form View Long Text</h4>
      </div>
			<form method="post" action="<?php echo site_url('pages_jaunes/update_value');?>">
			<div class="modal-body">
					<div class="form-group">
						<a class="btn btn-primary btn-copy-text">Copy</a>
					</div>
				  <div class="form-group">
						<textarea class="form-control" name="message" id="message" rows="8" cols="40"></textarea>
					</div>
					<input type="hidden" name="id" id="id" value="0">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input class="btn btn-primary" type="submit" value="OK">
      </div>
			</form>
    </div>
  </div>
</div>
<!-- EOf Modal Form View Long Text -->

<!-- Modal Form View Long Text -->
<div id="modal-form-emailing" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Choose E-mailing</h4>
			</div>		
			<div class="modal-body">
				<div class="form-group">
					<label>Software</label>
					<select class="form-control btn-create-emailing">
						<option value="">-- Choose a software --</option>						
						<option value="manual_sending">Envoi Manuel</option>
						<option value="pages_jaunes">Envois Pages Jaunes</option>
						<option value="max_bulk">Envois Max Bulk</option>
						<option value="openemm">Envois Openemm</option>
						<option value="sendgrid">Envois Sendgrid</option>
						<option value="sendinblue">Envois Sendinblue</option>
						<option value="airmail">Envois Air mail</option>
						<option value="mailchimp">Envois Mail chimp</option>
						<option value="emailing">Autres</option>
					</select>
				</div>
			</div>
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
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
		<a href="#" id="btn-remove" class="btn btn-warning">Yes</a>
      </div>
			</form>
    </div>
  </div>
</div>

<!-- Modal Form Confirmation Delete -->
<div id="modal-mass-remove" class="modal fade" role="dialog">
	<div class="modal-dialog">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Supprimer la confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Etes-vous certain de vouloir supprimer le champ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
				<a href="#" onclick="massAction()" id="btn-mass-remove" class="btn btn-warning">Oui</a>
			</div>
		</div>
	</div>
</div>
<!-- Modal Form Confirmation Re-integrer -->
<div id="modal-mass-re-integrer" class="modal fade" role="dialog">
	<div class="modal-dialog">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Re-integrer la confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Etes-vous certain de vouloir re-integrer le champ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
				<a href="#" onclick="massAction()" id="btn-mass-re-integrer" class="btn btn-warning">Oui</a>
			</div>
		</div>
	</div>
</div>