<!-- toolbar de la grille -->
<script id="template" type="text/x-kendo-template">
    <form class="form-inline text-left">
        <div class="form-group">
            <label for="action">Ajouter un article</label>
            <select class="form-control input-sm" id="ajout_article">
                <option value="x">(choisir)</option>
                <?php foreach ($familles as $f) {?>
                    <option value="<?php echo $f->vfm_code?>"><?php echo $f->vfm_famille?></option>
                <?php }?>
            </select>
        </div>
        <button type="button" class="btn btn-warning btn-xs" id="annulerLignes">Effacer</button>
        &nbsp;&nbsp;&nbsp;
        <button type="button" class="btn btn-primary btn-xs" id="enregistrerLignes">Enregistrer</button>
    </form>
</script>

<!-- Initialisation de la grille -->
<script>
    var arrondi = function(v) {return v.toFixed(2)};
    var tva = <?php echo $tva?>;
    var crudServiceBaseUrl = "<?php echo site_url("lignes_factures/manipulation/$id")?>";
    var datasource = new kendo.data.DataSource({
        transport: {
            read:  {
                url: crudServiceBaseUrl + '/get',
                dataType: "json"
            },
            update:  {
                url: crudServiceBaseUrl + '/update',
                dataType: "json"
            },
            destroy:  {
                url: crudServiceBaseUrl + '/destroy',
                dataType: "json"
            },
            create:  {
                url: crudServiceBaseUrl + '/create',
                dataType: "json"
            }
        },
        sync: function(e) {
            window.location.replace("<?php echo site_url("factures/genere_pdf/$id")?>");
        },
        batch:true,
        pageSize: 10,
        schema: {
            model: {
                fields: {
                    lif_id: {type: "number"},
                    lif_code: {editable: false},
                    lif_description: {},
                    lif_prix: {type: "number",editable: false},
                    lif_quantite: {type: "number"},
                    lif_remise_taux: {type: "number"},
                    lif_remise_ht: {type: "number"},
                    lif_remise_ttc: {type: "number"},
                },
                prixHt: function() {
                    if (this.lif_code == 'R') return '';
                    var num = this.lif_prix * this.lif_quantite - this.lif_remise_ht; return arrondi(num);
                },
                tva: function() {
                    if (this.lif_code == 'R') return '';
                    var num = (this.lif_prix * this.lif_quantite * tva - this.lif_remise_ht); return arrondi(num);
                },
                prixTtc: function() {
                    if (this.lif_code == 'R') return '';
                    var num = (this.lif_prix * this.lif_quantite - this.lif_remise_ht) * (1 + tva); return arrondi(num);
                },
                id: "lif_id"
            }
        }
    });
    var nouvel_article = function(data) {
        datasource.add( {
            lif_id: 0,
            lif_code: data.code,
            lif_description: data.description,
            lif_prix: data.prix,
            lif_quantite: 1,
            lif_remise_taux: 0,
            lif_remise_ht: 0,
            lif_remise_ttc: 0
        });
    };
</script>

<script>
    $(document).ready(function(){
        $("#grid-constitution-lignes-factures").kendoGrid({
            toolbar: [
                { template: kendo.template($("#template").html()) }
            ],
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
                            if (lignes[i].lif_code == 'R') {
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
                    field: "lif_remise_taux",
                    title: "Taux de remise",
                    format:"{0:p}",
                    attributes: {style: "text-align: right;"}
                },
                {
                    field: "lif_remise_ht",
                    title: "Remise HT",
                    format:"{0:0.00}",
                    attributes: {style: "text-align: right;"}
                },
                {
                    field: "lif_remise_ttc",
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
                            if (lignes[i].lif_code != 'R') {
                                total += lignes[i].lif_quantite * lignes[i].lif_prix - lignes[i].lif_remise_ht;
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
                                total += (lignes[i].lif_quantite * lignes[i].lif_prix - lignes[i].lif_remise_ht) * tva;
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
                                total += (lignes[i].lif_quantite * lignes[i].lif_prix - lignes[i].lif_remise_ht) * (1 + tva);
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
                { command: { name: "destroy", text: "Suppr",title: "&nbsp;"}}
            ],
            dataSource: datasource,
            scrollable: false,
            pageable: true,
            editable: true,
            selectable: true,
            reorderable: true,
            resizable: true,
            save: function(e) {
                var champ = Object.getOwnPropertyNames(e.values);
                var ptht = e.model.lif_prix * e.model.lif_quantite;
                switch (champ[0]) {
                    case "lif_remise_taux":
                        e.model.lif_remise_ht = ptht * e.values.lif_remise_taux;
                        e.model.lif_remise_ttc = e.model.lif_remise_ht * (1 + tva);
                        break;
                    case "lif_remise_ht":
                        e.model.lif_remise_taux = e.values.lif_remise_ht / ptht;
                        e.model.lif_remise_ttc = e.values.lif_remise_ht * (1 + tva);
                        break;
                    case "lif_remise_ttc":
                        e.model.lif_remise_ht = e.values.lif_remise_ttc / (1 + tva);
                        e.model.lif_remise_taux = e.model.lif_remise_ht / ptht;
                        break;
                    case "lif_quantite":
                        var ptht = e.model.lif_prix * e.values.lif_quantite;
                        e.model.lif_remise_ht = ptht * e.model.lif_remise_taux;
                        e.model.lif_remise_ttc = e.model.lif_remise_ht * (1 + tva);
                    default:
                }
                this.refresh() }
        });
        $("#enregistrerLignes").click(function(e) {
            var grid = $("#grid-constitution-lignes-factures").data("kendoGrid");
            grid.saveChanges();
            // bug Firefox was due to url redirecting at this point
            // this is now done in datasource sync event
            // window.location.replace("<?php echo site_url("factures/genere_pdf/$id")?>");
        });

        $("#annulerLignes").click(function(e) {
            var grid = $("#grid-constitution-lignes-factures").data("kendoGrid");
            grid.cancelChanges();
        });

        // fenetres pop_up
        <?php foreach ($familles as $f) {?>
        $("#popup-<?php echo $f->vfm_code?>").kendoWindow({
            modal: true,
            visible: false
        });
        <?php }?>

        // ajout d'article
        $(document).on("change", '#ajout_article', function (e) {
            var action = $(this).val();
            switch(action) {
            <?php foreach ($familles as $f) {?>
                case "<?php echo $f->vfm_code?>":
                <?php }?>
                    var win = $("#popup-"+action).data("kendoWindow");
                    win.center();
                    win.open();
                    break;
                default:
            }
            // revenir a choisir
            $(this).val('x') ;
        });

    });

</script>