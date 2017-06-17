<!-- Initialisation de la grille -->
<script>
    var arrondi = function(v) {return v.toFixed(2)};
    var tva = <?php echo tva()?>;
    var crudServiceBaseUrl = "<?php echo site_url("devis/manipulation/$id")?>";
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
                    actions: {},
                    ard_id: {type: "number"},
                    ard_article: {type: "number"},
                    ard_code: {editable: false},
                    ard_description: {},
                    ard_prix: {type: "number",editable: false},
                    ard_quantite: {type: "number"},
                    ard_remise_taux: {type: "number"},
                    ard_remise_ht: {type: "number"},
                    ard_remise_ttc: {type: "number"},
                },
                prixHt: function() {
                    if (this.ard_code == 'R') return '';
                    var num = this.ard_prix * this.ard_quantite - this.ard_remise_ht; return arrondi(num);
                },
                tva: function() {
                    if (this.ard_code == 'R') return '';
                    var num = (this.ard_prix * this.ard_quantite - this.ard_remise_ht) * tva; return arrondi(num);
                },
                prixTtc: function() {
                    if (this.ard_code == 'R') return '';
                    var num = (this.ard_prix * this.ard_quantite - this.ard_remise_ht) * (1 + tva); return arrondi(num);
                },
                id: "ard_id"
            }
        }
    });
</script>

<script>
    $(document).ready(function(){
        $("#grid").kendoGrid({
            columns: [
                {
                    field: "actions",
                    title: "Actions",
                    encoded: false
                },
                {
                    field: "ard_code",
                    title: "Code",
                    attributes: {style: "text-align: left;"}
                },
                {
                    field: "ard_description",
                    title: "Description",
                    attributes: {style: "text-align: left;"},
                    footerTemplate: '<div style="text-align: left;">Total avant remise<br />Remise</br>Total général</div>'
                },
                {
                    field: "ard_prix",
                    title: "PUHT",
                    format:"{0:0.00}",
                    attributes: {style: "text-align: right;"},
                    footerTemplate: function() {
                        var lignes = datasource.data();
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].ard_code == 'R') {
                                remise += lignes[i].ard_prix;
                            }
                        }
                        return '<div style="text-align: right;">&nbsp;<br />' + arrondi(remise*100) + ' %<br />&nbsp;</div>';
                    }
                },
                {
                    field: "ard_quantite",
                    title: "Quantité",
                    format:"{0:d}",
                    attributes: {style: "text-align: right;"}
                },
                {
                    field: "ard_remise_taux",
                    title: "Taux de remise",
                    format:"{0:p}",
                    attributes: {style: "text-align: right;"}
                },
                {
                    field: "ard_remise_ht",
                    title: "Remise HT",
                    format:"{0:0.00}",
                    attributes: {style: "text-align: right;"}
                },
                {
                    field: "ard_remise_ttc",
                    title: "Remise TTC",
                    format:"{0:0.00}",
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
                            if (lignes[i].ard_code != 'R') {
                                total += lignes[i].ard_quantite * lignes[i].ard_prix - lignes[i].ard_remise_ht;
                            }
                        }
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].ard_code == 'R') {
                                remise += lignes[i].ard_prix;
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
                            if (lignes[i].ard_code != 'R') {
                                total += (lignes[i].ard_quantite * lignes[i].ard_prix - lignes[i].ard_remise_ht) * tva;
                            }
                        }
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].ard_code == 'R') {

                                remise += lignes[i].ard_prix;
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
                            if (lignes[i].ard_code != 'R') {
                                total += (lignes[i].ard_quantite * lignes[i].ard_prix - lignes[i].ard_remise_ht) * (1 + tva);
                            }
                        }
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].ard_code == 'R') {
                                remise += lignes[i].ard_prix;
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
            selectable: false
        });

        // For the "Consulter" button to bring a popup modal
        $('#grid').on('click', 'a.view-detail', function(ev) {
            ev.preventDefault();
            actionMenuBar.loadInModal(this, '#template-modal-detail');
        });

        // For the detail links on the page
        $('a.view-detail').click(function(ev) {
            ev.preventDefault();
            actionMenuBar.loadInModal(this, '#template-modal-detail');
        });

    });

</script>
