<style type="text/css">
  .btn-ajouter {
    margin-top: 8px;
  }
  .btn-ajouter-link {
    margin-top: 8px;
  }
</style>

<!-- Modal Form Ajouter -->
<div id="modal-form-ajouter" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Form</h4>
      </div>
      <form method="POST" action="<?php echo site_url('champs/nouveau');?>" id="form-add-champs">
			<div class="modal-body">
            <div id="form-alert" class="alert alert-danger" style="display: none;">
              
            </div>
            <input type="hidden" name="champ_id" />
      			<input type="hidden" name="champ_name" />
            <div class="form-group">
              <label>Entrez le champ</label>
              <input type="text" name="champ_value" class="form-control" />
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-cancel">Cancel</button>
		    <button type="submit" class="btn btn-primary">Save</button>
      </div>
			</form>
    </div>
     <!-- Eof Modal content-->
  </div>
</div>
<!-- Eof Modal Form Ajouter -->

<script type="text/javascript">
  
  /**
   * generate button ajouter each from data liste_ajouter
   * @return {[type]} [description]
   */
  function generateBtnAjouter() {
    var btnAjouters = JSON.parse('<?php echo json_encode($liste_ajouter);?>');

    for(var i=0; i < btnAjouters.length; i++) {
      var classBtn = btnAjouters[i].ref == "#" ? "btn-ajouter" : "btn-ajouter-link"; 
      var targetBlank = btnAjouters[i].ref == "#" ? "" : 'target="_BLANK"';
      var button = '<a href="'+ btnAjouters[i].ref +'" '+ targetBlank +' class="btn btn-primary '+ classBtn +'" data-id="'+ btnAjouters[i].id +'"  data-name="'+ btnAjouters[i].champ +'">Ajouter</a>';

      $('#' + btnAjouters[i].id).parent().append(button);
    }
  }

  /**
   * method add/insert new champs to table
   */
  function addChamps() {
    //hide alert message
    $('#form-alert').hide();
    //get form data
    var urlAction = $('#form-add-champs').attr('action');
    var champName = $('input[name=champ_name]').val().trim();
    var champValue = $('input[name=champ_value]').val().trim();

    //if champName not null or empty
    if(champName != "" && champValue != "") {
      var data = {
        champ_name: champName,
        champ_value: champValue
      };
      //send data via ajax post
      $.post( urlAction, data, function( response ) {
        //handle response process ajax
        handleResponseFormChamps(response);
      }, "json");
    } else {
      $('#form-alert').html("Please enter champ").fadeIn();
    }
  }

  function handleResponseFormChamps(response) {
    if(response.status) {
      //append new value into dropdown
      var value = $('input[name=champ_value]').val();
      var id = $('input[name=champ_id]').val();
      var newOption = '<option>' + value + '</option>';
      $('#' + id).append(newOption);
      $('#' + id).val(value);
      //reset form and alert message
      resetFormChamps();
      //hide modal form
      $('#modal-form-ajouter').modal('hide');
    } else {
      $('#form-alert').html(response.error).fadeIn();
    }
  }

  function resetFormChamps() {
      $('input[name=champ_id]').val('');
      $('input[name=champ_name]').val('');
      $('input[name=champ_value]').val('');
      $('#form-alert').hide();
  }

  $(document).ready(function() {
    generateBtnAjouter();

    $('.form-group').on('click', '.btn-ajouter', function(e) {
      e.preventDefault();
      var champName = $(this).attr('data-name');
      var champId = $(this).attr('data-id');
      $('input[name=champ_name]').val(champName);
      $('input[name=champ_id]').val(champId);
      $('#modal-form-ajouter').modal('show');
    });

    $('#form-add-champs').submit(function(e) {
      e.preventDefault();
      addChamps();
    });

    $('.btn-cancel').click(function(e) {
      resetFormChamps();
      $('#modal-form-ajouter').modal('hide');
    });

  });
</script>


