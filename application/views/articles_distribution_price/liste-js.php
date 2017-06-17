
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
            Veuillez confirmer la suppression du Articles distribution price?
      </div>
     
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <a href="" class="btn btn-danger btn-custom-confirm-remove" role="button">Supprimer</a>
    </div>
     
    </div>
  </div>
</div>
<!-- ./Custom modal confirm remove -->

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
        
                
        /** Show Modal Remove Confirmation**/
        $("#articles_distribution_price_supprimer a").click(function(ev){
            ev.preventDefault();
            var href = $(this).attr('href');

            $('#modal-custom-remove').modal('show');
            $('.btn-custom-confirm-remove').attr('href', href);
        });

        /** Custom action remove **/
        $(".btn-custom-confirm-remove").click(function(ev) {
            ev.preventDefault();
            var url = $(this).attr('href') + '/ajax';
            var helper = actionMenuBar.datatable;
            var parentId = $('#datatable').find('tr.selected').attr('data-parent');

            $.ajax({
                type: 'POST',
                url: url,
                data: {},
                dataType: 'json',
                success: function(response) {                    
                    if(response.success == true) {
                        var event = response.data.event;
                        var isChild = event.hasOwnProperty("isChild") ? true : false;

                        if(!isChild) {
                            helper.unload(event.id);
                        } else {
                            $('#datatable').find('#child-id-' + event.id).fadeOut();
                            helper.reload(parentId);
                        }
                    } 

                    notificationWidget.show(response.message, response.notif);
                    $('#modal-custom-remove').modal('hide');
                },
                error: function(err) {
                    notificationWidget.show("Request error", 'warning');
                }
            })
        });

        //add class danger for button remove
        $("#articles_distribution_price_supprimer").addClass("action-confirm-danger");

        
    });
</script>
<?php $this->load->view('templates/remove_confirmation_js.php'); ?>