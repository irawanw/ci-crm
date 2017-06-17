<!-- Initialisation de la grille -->
<script>
    var arrondi = function(v) {return Math.round(v * 100) / 100};
    var tva = <?php echo tva()?>;
    var crudServiceBaseUrl = "<?php echo site_url("lignes_factures/manipulation/$id")?>";
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
                    lif_id: {type: "number"},
                    lif_code: {editable: false},
                    lif_description: {},
                    lif_prix: {type: "number",editable: false},
                    lif_quantite: {type: "number"}
                },
                prixHt: function() {
                    if (this.lif_code == 'R') return '';
                    var num = this.lif_prix * this.lif_quantite; return arrondi(num);
                },
                tva: function() {
                    if (this.lif_code == 'R') return '';
                    var num = this.lif_prix * this.lif_quantite * tva; return arrondi(num);
                },
                prixTtc: function() {
                    if (this.lif_code == 'R') return '';
                    var num = this.lif_prix * this.lif_quantite * (1 + tva); return arrondi(num);
                },
                id: "ard_id"
            }
        }
    });
</script>

<script>
    $(document).ready(function(){
        $("#grid-lignes-factures").kendoGrid({
            columns: [
                {
                    field: "lif_code",
                    title: "Code",
                    attributes: {style: "text-align: left;"}
                },
                {
                    field: "lif_description",
                    title: "Description",
                    attributes: {style: "text-align: left;"},
                    footerTemplate: '<div style="text-align: left;">Total avant remise<br />Remise</br>Total général</div>'
                },
                {
                    field: "lif_prix",
                    title: "PUHT",
                    format:"{0:0.00}",
                    attributes: {style: "text-align: right;"},
                    footerTemplate: function() {
                        var lignes = datasource.data();
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].art_code == 'R') {
                                remise += lignes[i].lif_prix;
                            }
                        }
                        return '<div style="text-align: right;">&nbsp;<br />' + arrondi(remise*100) + ' %<br />&nbsp;</div>';
                    }
                },
                {
                    field: "lif_quantite",
                    title: "Quantité",
                    format:"{0:d}",
                    attributes: {style: "text-align: right;"}
                },
                {
                    field: "prixHt()",
                    title: "PTHT",
                    format:"{0:0.00}",
                    attributes: {style: "text-align: right;"},
                    footerTemplate: function(){
                        var lignes = datasource.data();
                        var total = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lif_code != 'R') {
                                total += lignes[i].lif_quantite * lignes[i].lif_prix;
                            }
                        }
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lif_code == 'R') {
                                remise += lignes[i].lif_prix;
                            }
                        }
                        var totalR = -total * remise;
                        var totalG = total * (1 - remise);
                        return '<div style="text-align: right;">' + arrondi(total) + '<br />' + arrondi(totalR) + '<br />' + arrondi(totalG) + '</div>';
                    }
                },
                {
                    field: "tva()",
                    title: "TVA",
                    format:"{0:0.00}",
                    attributes: {style: "text-align: right;"},
                    footerTemplate: function(){
                        var lignes = datasource.data();
                        var total = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lif_code != 'R') {
                                total += lignes[i].lif_quantite * lignes[i].lif_prix * tva;
                            }
                        }
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lif_code == 'R') {

                                remise += lignes[i].lif_prix;
                            }
                        }
                        var totalR = -total * remise;
                        var totalG = total * (1 - remise);
                        return '<div style="text-align: right;">' + arrondi(total) + '<br />' + arrondi(totalR) + '<br />' + arrondi(totalG) + '</div>';
                    }
                },
                {
                    field: "prixTtc()",
                    title: "PTTC",
                    format:"{0:0.00}",
                    attributes: {style: "text-align: right;"},
                    footerTemplate: function(){
                        var lignes = datasource.data();
                        var total = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lif_code != 'R') {
                                total += lignes[i].lif_quantite * lignes[i].lif_prix * (1 + tva);
                            }
                        }
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lif_code == 'R') {
                                remise += lignes[i].lif_prix;
                            }
                        }
                        var totalR = -total * remise;
                        var totalG = total * (1 - remise);
                        return '<div style="text-align: right;">' + arrondi(total) + '<br />' + arrondi(totalR) + '<br />' + arrondi(totalG) + '</div>';
                    }
                }
            ],
            dataSource: datasource,
            scrollable: false,
            pageable: true,
            editable: false,
            selectable: false,
            reorderable: true,
            resizable: true
        });
    });

</script>