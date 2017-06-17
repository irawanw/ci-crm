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
    });
</script>
<?php $this->load->view('templates/remove_confirmation_js.php'); ?>