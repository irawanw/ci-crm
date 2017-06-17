<!-- Modal Form View Long Text -->
<div id="modal-form-view-text" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Form View Long Text</h4>
      </div>
            <form method="post" action="<?php echo site_url('message_list/update_value');?>">
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

<!-- Custom modal confirm remove -->
<div id="modal-custom-remove" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation d'opération</h4>
      </div>        
      <div class="modal-body">
            Veuillez confirmer la suppression du message list
      </div>
     
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <a href="" class="btn btn-danger btn-custom-confirm-remove" role="button">Supprimer</a>
    </div>
     
    </div>
  </div>
</div>
<!-- ./Custom modal confirm remove -->

<!-- Custom modal confirm archive -->
<div id="modal-custom-archive" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation d'opération</h4>
      </div>        
      <div class="modal-body">
            Veuillez confirmer la archive du message list
      </div>
     
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <a href="" class="btn btn-warning btn-custom-confirm-archive" role="button">Archiver</a>
    </div>
     
    </div>
  </div>
</div>
<!-- ./Custom modal confirm archive -->
<script>
    $(document).ready(function() {
		//view all button to list all result
        $('#voir_liste a').click(function(e) {
            e.preventDefault();
            console.log("show all");
            var isList = $(this).attr('data-list');
            var table = $('#datatable');
            var setting = table.DataTable().init();
            var textHtml = $(this).html();

            if(isList) {
                setting.iDisplayLength = 100;
                setting.sScrollY = 575;
                setting.scroller = {loadingIndicator: true};
                setting.bPaginate = true;
                table.DataTable().destroy();
                table.DataTable(setting);
                textHtml = textHtml.replace("Défaut la liste", "Voir la liste");
                $(this).html(textHtml);
                $(this).removeAttr("data-list");
            } else {
                delete setting.scrollY;
                delete setting.scroller;
                setting.sScrollY = false;
                setting.iDisplayLength = -1;
                setting.bPaginate = false;
                table.DataTable().destroy();
                table.DataTable(setting);
                textHtml = textHtml.replace("Voir la liste", "Défaut la liste");
                $(this).html(textHtml);
                $(this).attr("data-list", true);
            }
        });
	
        $('#sel_view').change(function(e) {
            var view = $(this).val();
            window.location = view;
        });

        //action to open modal form view long text
        $('#datatable tbody').on('click', '.view-text', function(e) {
          e.preventDefault();
          var id = $(this).attr('data-id');
          var message = $(this).attr('data-message');
          $('#id').val(id);
          $('#message').val(message);
          $('#modal-form-view-text').modal('show');
        });

        //action to copy text on textarea form
        $('.btn-copy-text').click(function(){
    			textarea = $('#message');
    			textarea.select();
    			document.execCommand('copy');
    		});

        /** action remove data */
        /*$('#datatable tbody').on('click', 'tr', function(e) {
            var table = $('#datatable').DataTable();
            var data = table.row(this).data();

            $('#btn-remove').prop("href", "<?php echo site_url('message_list/remove');?>/" + data.message_list_id);

            $('#message_list_supprimer').removeClass("disabled");
        });

        $('#message_list_supprimer').click(function(e) {
            e.preventDefault();
            $('#modal-form-remove').modal('show');
        });*/

		var old_init = dt_init_resizable;
		dt_init_resizable = function() {
		  old_init.apply(this, arguments);
		  //groupedHeader();
		  customHeaderCheckbox();
		};		
    });

    //grouped header to be 3 groups
    function groupedHeader(){
        //grouped header
        var myElem = document.getElementById('parentHeader');
        var table = $('#datatable').DataTable();
        var header = table.table().header();


        if (myElem == null){
            $(header).find("#data_table_columns_header").before("<tr style='height: 20px !important' id='parentHeader' class='labels' role='row'><td style='text-align:center' colspan='6' >Mail</td> <td style='text-align:center' colspan='5' >Mail Body</strong></td>  <td style='text-align:center' colspan='8' >Mail Database</strong></td></tr>");
        }
    }   
</script>
<?php $this->load->view('templates/remove_confirmation_js.php'); ?>