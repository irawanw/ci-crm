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
       

        $('#btn-voir-access').click(function(e) {
            voirLesAccess($(this));
        });
        
        //custom header checkbox
        setTimeout(function() {
            hideColumnAccess();
        }, 500);
    });

    function getIndexColumnAccess() {
        var columnIndexes = [];
        var start = 12;
        var length = 16;

        for(var i = start; i < start + length; i++) {
            columnIndexes.push(i);
        }

        return columnIndexes;
    }

    function hideColumnAccess() {
        var columnIndexes = getIndexColumnAccess();
        var table = $('#datatable').DataTable();
        table.columns(columnIndexes).visible(false, false);
        table.columns.adjust().draw();
    }

    function voirLesAccess(btn) {
        var isHidden = btn.attr('data-hidden') ? btn.attr('data-hidden') : null;
        var columnIndexes = getIndexColumnAccess();
        var table = $('#datatable').DataTable();

        //console.log(columnIndexes)

        if(isHidden) {
            btn.text("voir les accès");
            btn.removeAttr('data-hidden');
            table.columns(columnIndexes).visible(false, false);
            table.columns.adjust().draw();
        } else {
            btn.text("masquer les accès");
            btn.attr("data-hidden", true);
            table.columns(columnIndexes).visible(true, false);
            table.columns.adjust().draw();
        }
    }
</script>
