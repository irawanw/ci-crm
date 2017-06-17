<!-- Initialisation de la grille -->
<script>
    var crudServiceBaseUrl = "<?php echo site_url("modeles_documents/liste")?>";
    var datasource = new kendo.data.DataSource({
        transport: {
            read:  {
                url: crudServiceBaseUrl + '/get',
                dataType: "json"
            }
        },
        pageSize: 10,
        schema: {
            model: {
                fields: {
                    type: {},
                    client: {},
                    piece: {},
                    date: {type: "date"},
                    heure: {},
                    utilisateur: {},
                    fichier: {}
                },
                id: ""
            }
        },
        sort:[
            { field: "date", dir: "desc" },
            { field: "heure", dir: "desc" }
        ]
    });
</script>

<script>
    $(document).ready(function(){
        $("#grid").kendoGrid({
            columns: [
                {
                    field: "type",
                    title: "Type",
                    attributes: {style: "text-align: left;"},
                    filterable: { cell: { operator: "contains" } }
                },
                {
                    field: "client",
                    title: "Client",
                    attributes: {style: "text-align: left;"},
                    filterable: { cell: { operator: "contains" } }
                },
                {
                    field: "piece",
                    title: "Piece",
                    attributes: {style: "text-align: left;"},
                    filterable: { cell: { operator: "contains" } }
                },
                {
                    field: "date",
                    title: "Date",
                    attributes: {style: "text-align: left;"},
                    format: "{0: dd/MM/yyyy}",
                    filterable: { cell: { operator: "eq" } }
                },
                {
                    field: "heure",
                    title: "Heure",
                    attributes: {style: "text-align: left;"},
                    filterable: { cell: { operator: "contains" } }
                },
                {
                    field: "fichier",
                    title: "Fichier",
                    encoded: false,
                    attributes: {style: "text-align: left;"},
                    filterable: { cell: { operator: "contains" } }
                }
            ],
            dataSource: datasource,
            scrollable: false,
            pageable: true,
            editable: false,
            selectable: false,
            reorderable: true,
            resizable: true,
            filterable: {
                mode: "row"
            }
        });
    });

</script>
