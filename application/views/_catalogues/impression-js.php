<?php $code_cat = 'I'?>
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
                    art_libelle: {},
                    art_imprime: {},
                    art_format: {},
                    art_taille: {},
                    art_papier: {},
                    art_grammage: {type: "string"},
                    art_finitions: {},
                    art_couleurs: {type: "string"},
                    art_recto_verso: {},
                    art_quantite: {type: "string"}
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
                },
                {
                    field: "art_imprime",
                    title: "Imprimé",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "art_format",
                    title: "Format",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "art_taille",
                    title: "Taille",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "art_papier",
                    title: "Papier",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "art_grammage",
                    title: "Grammage",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "art_finitions",
                    title: "Finition",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "art_couleurs",
                    title: "Couleurs",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "art_recto_verso",
                    title: "Recto/verso",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "art_quantite",
                    title: "Quantité",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "art_prix",
                    title: "Prix",
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
            // selection is custom made - see below
            selectable: false,
            // disable save button
            dataBound: function(e) {
                 $("#form_I").prop('disabled', true) ;
            },
            scrollable: false,
            pageable: true
        });

        // custom made multiple select
        // manage disabled state
        $("#grid_I").delegate('tbody>tr', 'click', function(){
            $(this).toggleClass('k-state-selected');
            $("#form_I").prop('disabled', true) ;
            $("#grid_I  tbody>tr").each( function(idx, row) {
                if ($(row).hasClass('k-state-selected')) {
                    $("#form_I").prop('disabled', false) ;
                }
            })
        });


        $("#form_I").click(function(e){
            e.preventDefault ;
            var grid = $("#grid_I").data("kendoGrid");
            $("#grid_I  tbody>tr").each( function(idx, row) {
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
                    $(row).removeClass('k-state-selected') ;
                    nouvel_article(article);
                }
            })
            var win = $("#popup-I").data("kendoWindow");
            win.close() ;
        })

        $("#form_I_close").click(function(e){
            var win = $("#popup-I").data("kendoWindow");
            win.close() ;
        })

   });

</script>
