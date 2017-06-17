<script>
    $(document).ready(function() {

        $('#sel_view').change(function(e) {
            var view = $(this).val();
            window.location = view;
        });

        //action to open modal form upload file
        $('#datatable tbody').on('click', '.btn-upload-file', function(e) {
          e.preventDefault();
          var id = $(this).attr('data-id');
          $('#upload_id').val(id);
          $('#modal-form-upload').modal('show');
        });

        /** Action Voir la liste */
        $('#vehicules_voir_liste a').click(function(e) {
            e.preventDefault();
            //console.log("show all");
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
        /** eof Action Voir la liste */
    });

</script>
<?php $this->load->view('templates/remove_confirmation_js.php');
$this->load->view('templates/modal_upload_files.php');
$this->load->view('templates/modal_upload_files-js.php', array('url_upload' => $url_upload)); 
?>