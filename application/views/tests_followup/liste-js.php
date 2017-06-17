<script>
    $(document).ready(function() 
	{
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
		
		//Export button for exporting the list into file
        $('#export_pdf').click(function(e){
			e.preventDefault();
			$('.buttons-pdf').click();
		});
		$('#print_list').click(function(e){
			e.preventDefault();
			$('.buttons-print').click();
		});		
		$('#export_xlsx').click(function(e){
            e.preventDefault();         
            $('.buttons-excel').click();
        }); 

        $('#datatable').on("click", "tr", function() {
            var thisRow = DT.row( this );
            var id = thisRow.id();
            //masquage_barre_action(id) ;
            $('#datatable').find('tr').removeClass('selected');
            $(this).addClass('selected');
			$('#template-modal-detail').attr("data-id", id);
        });
		
        $('#sel_view').change(function(e) {
            var view = $(this).val();
            window.location = view;
        });
});

</script>
<?php $this->load->view('templates/remove_confirmation_js.php'); ?>