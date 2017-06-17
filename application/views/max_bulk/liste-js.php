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
            Veuillez confirmer la suppression du maxbulk
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
            Veuillez confirmer la archive du Max Bulk
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

        //action to open modal form upload file
        $('#datatable tbody').on('click', '.btn-view-detail', function(e) {
            e.preventDefault();
            var btnSelected = $(this);
            var isHide = btnSelected.attr('data-hide') == "1" ? true : false;
            var tr = $(this).closest('tr');
            var row = DT.row( tr );
            var tr_id = tr.attr('id');

            if(isHide == false) {                     
                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    
                    //remove child row
                    $('.child-'+tr_id).remove();
                }
                else {
                    // Open this row
                    //row.child( format(row.data()) ).show();
                    tr.addClass('shown');                               
                    
                    //reinit click selected function
                    $('#datatable tr').click(function(){
                        $('#datatable tr').removeClass('selected');
                        $(this).addClass('selected');
                    })
                    
                    //get child data
                    $.post('<?php echo site_url(); ?>/max_bulk/index_child_json/0', {'parentId':tr_id}, function(data){
                        //toggle button hide
                        btnSelected.attr('data-hide', 1);
                        btnSelected.text('hide detail');
                        
                        if($('.child-'+tr_id).length>=1)
                            $('.child-'+tr_id).remove();
                        
                        if(data.data.length > 0) {
                            $.each(data.data, function(index, value){
                                //we might change this later
                        new_html = '<tr id="child-id-'+value.max_bulk_child_id+'" data-parent="'+tr_id+'" class="child child-'+tr_id+'">'+
                                        '<td colspan="23">Sub Sending '+(index+1)+'</td>'+
                                        '<td>'+value.date_envoi+'</td>'+
                                        '<td>'+value.segment_part+'</td>'+          
                                        // '<td></td>'+
                                        // '<td></td>'+                                        
                                        '<td>'+value.stats+'</td>'+
                                        '<td>'+value.quantite_envoyee+'</td>'+
                                        '<td>'+value.verification_number+'</td>'+
                                        '<td>'+value.open+'</td>'+
                                        '<td>'+value.open_pourcentage+'</td>'+
                                        '<td>'+value.deliv_sur_test_orange+'</td>'+
                                        '<td>'+value.deliv_sur_test_free+'</td>'+
                                        '<td>'+value.deliv_sur_test_sfr+'</td>'+
                                        '<td>'+value.deliv_sur_test_gmail+'</td>'+
                                        '<td>'+value.deliv_sur_test_yahoo+'</td>'+
                                        '<td>'+value.deliv_sur_test_microsoft+'</td>'+
                                        '<td>'+value.deliv_sur_test_ovh+'</td>'+
                                        '<td>'+value.deliv_sur_test_oneandone+'</td>'+
                                        '<td>'+value.operateur_qui_envoie_name+'</td>'+
                                        '<td>'+value.deliv_reelle_orange+'</td>'+
                                        '<td>'+value.deliv_reelle_free+'</td>'+
                                        '<td>'+value.deliv_reelle_sfr+'</td>'+
                                        '<td>'+value.deliv_reelle_gmail+'</td>'+
                                        '<td>'+value.deliv_reelle_microsoft+'</td>'+
                                        '<td>'+value.deliv_reelle_yahoo+'</td>'+
                                        '<td>'+value.deliv_reelle_ovh+'</td>'+
                                        '<td>'+value.deliv_reelle_oneandone+'</td>'+
                                        '<td>'+value.operateur_qui_envoie_name+'</td>'+
                                        '<td>'+value.physical_server+'</td>'+
                                        '<td>'+value.smtp+'</td>'+
                                        '<td>'+value.computer+'</td>'+
                                    '</tr>';
                                
                                if($('.child-'+tr_id).length>=1)
                                    $('.child-'+tr_id).last().after(new_html);
                                else
                                    $(tr).after(new_html);
                                
                                if ($('.child-'+tr_id).length == (index+1)) {
                                    $('#datatable').unbind();
                                    $('#datatable tr').click(function(){
                                        parent_id = $(this).attr('id');                             
                                        $('#datatable tr').removeClass('selected');
                                        $(this).addClass('selected');

                                        $('#max_bulk_supprimer').addClass("action-confirm-danger");                            
                                        //modify button modification & remove
                                        if($(this).hasClass('child')){                                  
                                            child_id = $(this).attr('id').split('-');
                                            child_id = child_id[2];

                                            $("#max_bulk_modification a").attr('href', 
                                                '<?php echo site_url(); ?>/max_bulk/modification_child/'+child_id);
                                            $("#max_bulk_archiver a").attr('href', 
                                                '<?php echo site_url(); ?>/max_bulk/archive_child/'+child_id);
                                            $("#max_bulk_supprimer a").attr('href', 
                                                '<?php echo site_url(); ?>/max_bulk/remove_child/'+child_id);
                                            
                                        } else {
                                            $("#max_bulk_modification a").attr('href', 
                                                '<?php echo site_url(); ?>/max_bulk/modification/'+parent_id);

                                            $("#max_bulk_supprimer a").attr('href', 
                                                '<?php echo site_url(); ?>/max_bulk/remove/'+parent_id);

                                            $("#max_bulk_archiver a").attr('href', 
                                                '<?php echo site_url(); ?>/max_bulk/archive/'+parent_id);
                                        }
                                    })
                                }
                            })
                        } else {
                            new_html = '<tr data-parent="'+tr_id+'" class="child child-'+tr_id+'"><td colspan="49">None</td></tr>';
                            $(tr).after(new_html);
                        }
                    });             
                } 
            } else {
                //toggle button view
                btnSelected.removeAttr('data-hide');
                btnSelected.text('view detail');
                $('.child-'+tr_id).remove();
            }
        });

        /** Show Modal Remove Confirmation**/
        $("#max_bulk_supprimer a").click(function(ev){
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
        $("#max_bulk_supprimer").addClass("action-confirm-danger");

        /** Show Modal Archive Confirmation**/
        $("#max_bulk_archiver a").click(function(ev){
            ev.preventDefault();
            var href = $(this).attr('href');

            $('#modal-custom-archive').modal('show');
            $('.btn-custom-confirm-archive').attr('href', href);
        });

        /** Custom action archive **/
        $(".btn-custom-confirm-archive").click(function(ev) {
            ev.preventDefault();
            var url = $(this).attr('href') + '/ajax';
            var helper = actionMenuBar.datatable;

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
                        }
                    } 

                    notificationWidget.show(response.message, response.notif);
                    $('#modal-custom-archive').modal('hide');
                },
                error: function(err) {
                    notificationWidget.show("Request error", 'warning');
                }
            })
        });

    });
</script>
<?php $this->load->view('templates/remove_confirmation_js.php'); ?>