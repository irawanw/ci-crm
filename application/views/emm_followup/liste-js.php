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

            //default view hide deliverance and technical column
            
            setTimeout(function() {
                deliveranceColumn('hide');
                technicalColumn('hide');
            },1000);

            //action toggle button to show/hide deliverance column
            $('#emm_followup_show_deliverance a').click(function(e) {
                //console.log("show deliverance");
                e.preventDefault();
                var textHtml = $(this).html();
                var hide = $(this).attr('data-hide') ? $(this).attr('data-hide') : false;

                if(hide) {
                    textHtml = textHtml.replace("Hide", "Show");
                    $(this).html(textHtml);
                    $(this).removeAttr('data-hide');

                    deliveranceColumn('hide');
                } else {
                    textHtml = textHtml.replace("Show", "Hide");
                    $(this).html(textHtml);
                    $(this).attr('data-hide', true);
                    deliveranceColumn('show');
                }
            });

            //action toggle button to show/hide techincal column
            $('#emm_followup_show_technical a').click(function(e) {
                //console.log("show technical");
                e.preventDefault();
                var textHtml = $(this).html();
                var hide = $(this).attr('data-hide') ? $(this).attr('data-hide') : false;

                if(hide) {
                    textHtml = textHtml.replace("Hide", "Show");
                    $(this).html(textHtml);
                    $(this).removeAttr('data-hide');

                    technicalColumn('hide');
                } else {
                    textHtml = textHtml.replace("Show", "Hide");
                    $(this).html(textHtml);
                    $(this).attr('data-hide', true);
                    technicalColumn('show');
                }
            });

            //mass action
            $("#btn_action_all").click(function(e) {
                var action = $("#sel_action_all" ).val();
                theid = {};
                $("tbody input:checkbox:checked").each(function(i){
                    theid[i] = $(this).val();
                    $(this).parent().parent().fadeOut();
                });
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('emm_followup/mass_');?>" + action,
                    data: {ids: JSON.stringify(theid)},
                });
            })

            //check/uncheck all boxes
        $("#datatable thead").on('click', '#check-all', function(e) {
            $("tbody input:checkbox").not(this).prop('checked', this.checked);
        });
        
        //custom header checkbox
        setTimeout(function() {
            customHeaderCheckbox();
        }, 500);
    });

    /**
     * Generate input type checkbox in header column checkbox ids
     * @return {[type]} [description]
     */
    function customHeaderCheckbox() {
        var table = $('#datatable').DataTable();
        var headerCheckbox = table.column(0).header();
        $(headerCheckbox).html("<input type='checkbox' id='check-all' />");

    }

    function deliveranceColumn(action) {
        var table = $('#datatable').DataTable();
        var start = 15;
        var end = 23;

          if(action == 'hide') {

            for(var i = start; i <= end; i++) {
                table.column( i ).visible( false, false );
            }

            table.columns.adjust().draw( false ); // adjust column sizing and redraw
          } else {
            for(var i = start; i <= end; i++) {
                table.column( i ).visible( true);
            }

            table.columns.adjust().draw( false ); // adjust column sizing and redraw
          }

    }

    function technicalColumn(action) {
        var table = $('#datatable').DataTable();

        var start = 24;
        var end = 26;

          if(action == 'hide') {

            for(var i = start; i <= end; i++) {
                table.column( i ).visible( false, false );
            }

            table.columns.adjust().draw( false ); // adjust column sizing and redraw
          } else {
            for(var i = start; i <= end; i++) {
                table.column( i ).visible( true);
            }

            table.columns.adjust().draw( false ); // adjust column sizing and redraw
          }
    }
</script>
