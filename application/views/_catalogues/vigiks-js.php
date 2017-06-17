<?php $code_cat = 'V'?>
<!-- Initialisation de la grille -->
<script>
    var datasource_<?php echo $code_cat?> = new kendo.data.DataSource({
        transport: {
            read:  {
                url: "<?php echo site_url("devis/lecture_catalogue/$code_cat")?>",
                dataType: "json"
            }
        },
        pageSize: 10,
        schema: {
            model: {
                fields: {
                    art_id: {},
                    art_code: {},
                    art_description: {},
                    art_libelle: {}
                },
                id: "art_id"
            }
        }
    });
</script>

<script>
    $(document).ready(function(){
        $("#grid_<?php echo $code_cat?>").kendoGrid({
            columns: [
                {
                    field: "art_code",
                    title: "Code",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "art_description",
                    title: "Description",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                }
            ],
            dataSource: datasource_<?php echo $code_cat?>,
            filterable: {
                mode: "row"
            },
            selectable: false,
            scrollable: false,
            pageable: true
        });

        // custom made multiple select
        // manage disabled state
        $("#grid_V").delegate('tbody>tr', 'click', function(){
            $(this).toggleClass('k-state-selected');
            $("#form_V").prop('disabled', true) ;
            $("#grid_V  tbody>tr").each( function(idx, row) {
                if ($(row).hasClass('k-state-selected')) {
                    $("#form_V").prop('disabled', false) ;
                }
            })
        });


        $("#form_V").click(function(e){
            e.preventDefault ;
            var grid = $("#grid_V").data("kendoGrid");
            $("#grid_V  tbody>tr").each( function(idx, row) {
                if ($(row).hasClass('k-state-selected')) {
                    var data = grid.dataItem(row);
                    var article = {
                        id: data.art_id,
                        code: data.art_code,
                        description: data.art_libelle,
                        info: "",
                        prix: data.art_prix,
                        quantite: 1
                    }
                    // superfragilistic
                    nouvel_article(article);
                    $(row).removeClass('k-state-selected') ;
                }
            })
            var win = $("#popup-V").data("kendoWindow");
            win.close() ;
        })

        $("#form_V_close").click(function(e){
            var win = $("#popup-V").data("kendoWindow");
            win.close() ;
        })

    });

</script>
