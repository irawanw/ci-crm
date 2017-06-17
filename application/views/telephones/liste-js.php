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
    });

    setTimeout(function() { 
        //extending print button
        var table = $('#datatable').DataTable();        

        //extending pdf button  
        table.button(3).remove();   //we will customize pdf button so remove it first then readding again
        table.button().add( 3, {    //re adding the pdf button
            extend: 'pdfHtml5',
            text: 'Save PDF',
            exportOptions: {
                modifier: {      
                    columns: ':visible',
                },
            },   
            orientation: 'landscape',
            pageSize: 'LEGAL',      
            customize : function(doc){
                doc.defaultStyle.fontSize = 8;
                doc.styles.tableHeader.fontSize = 8;
                doc.pageMargins = [10, 10, 10,10 ];
            } 
        });
    }, 1000);
</script>
<?php $this->load->view('templates/remove_confirmation_js.php'); ?>