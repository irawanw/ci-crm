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
        <button type="button" class="btn btn-warning btn-xs" id="annulerLignes"><span class="glyphicon glyphicon-refresh"></span> Annuler les changements</button>
    </form>
</script>

<!-- Initialisation de la grille -->
<script>
    var arrondi = function(v) {return v.toFixed(2)};
    var tva = <?php echo $tva?>;
    var crudServiceBaseUrl = "<?php echo site_url("lignes_avoirs/manipulation/$id")?>";
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
            var broadcast = actionMenuBar.broadcastOnSuccess();
            var data = {
                success: true,
                data: {
                    event: {
                        controleur: "avoirs",
                        type: "recordchange",
                        id: <?php echo $id ?>,
                        timeStamp: Date.now()
                    }
                }
            };
            broadcast(data);
            window.location.replace("<?php echo site_url("avoirs/imprimer_pdf/$id")?>");
        },
        batch:true,
        pageSize: 10,
        schema: {
            model: {
                fields: {
                    lia_id: {type: "number"},
                    lia_code: {editable: false},
                    lia_description: {},
                    lia_prix: {type: "number",editable: false},
                    lia_quantite: {type: "number"},
                    lia_remise_taux: {type: "number"},
                    lia_remise_ht: {type: "number"},
                    lia_remise_ttc: {type: "number"}
                },
                prixHt: function() {
                    if (this.lia_code == 'R') return '';
                    var num = this.lia_prix * this.lia_quantite - this.lia_remise_ht; return arrondi(num);
                },
                tva: function() {
                    if (this.lia_code == 'R') return '';
                    var num = (this.lia_prix * this.lia_quantite - this.lia_remise_ht) * tva; return arrondi(num);
                },
                prixTtc: function() {
                    if (this.lia_code == 'R') return '';
                    var num = (this.lia_prix * this.lia_quantite - this.lia_remise_ht) * (1 + tva); return arrondi(num);
                },
                id: "lia_id"
            }
        }
    });
    var nouvel_article = function(data) {
        datasource.add( {
            lia_id: 0,
            lia_code: data.code,
            lia_description: data.description,
            lia_prix: data.prix,
            lia_quantite: 1,
            lia_remise_taux: 0,
            lia_remise_ht: 0,
            lia_remise_ttc: 0
        });
        actionMenuBar.disable('#avoir_dupliquer');
        actionMenuBar.disable('#avoir_valider');
        actionMenuBar.disable('#avoir_exporter_pdf');
        actionMenuBar.disable('#avoir_imprimer_pdf');
        actionMenuBar.enable('#enregistrerLignes');
        $("#annulerLignes").show();
    };
</script>

<script>
    $(document).ready(function() {
        $("#grid-constitution-lignes-avoirs").kendoGrid({
            toolbar: [
                { template: kendo.template($("#template").html()) }
            ],
            columns: [
                {
                    field: "lia_code",
                    title: "Code",
                    attributes: {style: "text-align: left;"}
                },
                {
                    field: "lia_description",
                    title: "Description",
                    attributes: {style: "text-align: left;"},
                    footerTemplate: '<div style="text-align: left;">Total avant remise<br />Remise</br>Total général</div>'
                },
                {
                    field: "lia_prix",
                    title: "PUHT",
                    format:"{0:0.00}",
                    attributes: {style: "text-align: right;"},
                    footerTemplate: function() {
                        var lignes = datasource.data();
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lia_code == 'R') {
                                remise += lignes[i].lia_prix;
                            }
                        }
                        return (remise > 0) ? '<div style="text-align: right;">&nbsp;<br />' + arrondi(remise * 100) + ' %<br />&nbsp;</div>' : '';
                    }
                },
                {
                    field: "lia_quantite",
                    title: "Quantité",
                    format:"{0:d}",
                    attributes: {style: "text-align: right;"}
                },
                {
                    field: "lia_remise_taux",
                    title: "Taux de remise",
                    format:"{0:0.00} %",
                    attributes: {style: "text-align: right;"}
                },
                {
                    field: "lia_remise_ht",
                    title: "Remise HT",
                    format:"{0:0.00}",
                    attributes: {style: "text-align: right;"}
                },
                {
                    field: "lia_remise_ttc",
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
                        var total_brut = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lia_code != 'R') {
                                total_brut += lignes[i].lia_quantite * lignes[i].lia_prix;
                                total += lignes[i].lia_quantite * lignes[i].lia_prix - lignes[i].lia_remise_ht;
                            }
                        }
                        var remise_globale = 0;
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lia_code == 'R') {
                                remise_globale += lignes[i].lia_prix;
                            } else {
                                remise += lignes[i].lia_remise_ht;
                            }
                        }
                        var totalR = -(total * remise_globale + remise);
                        var totalG = total * (1 - remise_globale);
                        return '<div style="text-align: right;">' + arrondi(total_brut) + '<br />' + arrondi(totalR) + '<br />' + arrondi(totalG) + '</div>';
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
                        var total_brut = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lia_code != 'R') {
                                total_brut += (lignes[i].lia_quantite * lignes[i].lia_prix) * tva;
                                total += (lignes[i].lia_quantite * lignes[i].lia_prix - lignes[i].lia_remise_ht) * tva;
                            }
                        }
                        var remise_globale = 0;
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lia_code == 'R') {
                                remise_globale += lignes[i].lia_prix;
                            } else {
                                remise += lignes[i].lia_remise_ht * tva;
                            }
                        }
                        var totalR = -(total * remise_globale + remise);
                        var totalG = total * (1 - remise_globale);
                        return '<div style="text-align: right;">' + arrondi(total_brut) + '<br />' + arrondi(totalR) + '<br />' + arrondi(totalG) + '</div>';
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
                        var total_brut = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lia_code != 'R') {
                                total_brut += (lignes[i].lia_quantite * lignes[i].lia_prix) * (1 + tva);
                                total += (lignes[i].lia_quantite * lignes[i].lia_prix - lignes[i].lia_remise_ht) * (1 + tva);
                            }
                        }
                        var remise_globale = 0;
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].lia_code == 'R') {
                                remise_globale += lignes[i].lia_prix;
                            } else {
                                remise += lignes[i].lia_remise_ht * (1 + tva);
                            }
                        }
                        var totalR = -(total * remise_globale + remise);
                        var totalG = total * (1 - remise_globale);
                        return '<div style="text-align: right;">' + arrondi(total_brut) + '<br />' + arrondi(totalR) + '<br />' + arrondi(totalG) + '</div>';
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
                var ptht = e.model.lia_prix * e.model.lia_quantite;
                switch (champ[0]) {
                    case "lia_remise_taux":
                        e.model.lia_remise_ht = ptht * e.values.lia_remise_taux / 100;
                        e.model.lia_remise_ttc = e.model.lia_remise_ht * (1 + tva);
                        break;
                    case "lia_remise_ht":
                        e.model.lia_remise_taux = e.values.lia_remise_ht / ptht;
                        e.model.lia_remise_ttc = e.values.lia_remise_ht * (1 + tva);
                        break;
                    case "lia_remise_ttc":
                        e.model.lia_remise_ht = e.values.lia_remise_ttc / (1 + tva);
                        e.model.lia_remise_taux = e.model.lia_remise_ht / ptht;
                        break;
                    case "lia_quantite":
                        var ptht = e.model.lia_prix * e.values.lia_quantite;
                        e.model.lia_remise_ht = ptht * e.model.lia_remise_taux / 100;
                        e.model.lia_remise_ttc = e.model.lia_remise_ht * (1 + tva);
                    default:
                }
                this.refresh() }
        });
        $("#enregistrerLignes").click(function(e) {
            var grid = $("#grid-constitution-lignes-avoirs").data("kendoGrid");
            grid.saveChanges();
            actionMenuBar.enable('#avoir_dupliquer', <?php echo $id; ?>);
            actionMenuBar.enable('#avoir_valider', <?php echo $id; ?>);
            actionMenuBar.enable('#avoir_exporter_pdf', <?php echo $id; ?>);
            actionMenuBar.enable('#avoir_imprimer_pdf', <?php echo $id; ?>);
            $("#annulerLignes").hide();
        });

        $("#annulerLignes").click(function(e) {
            var grid = $("#grid-constitution-lignes-avoirs").data("kendoGrid");
            grid.cancelChanges();
            actionMenuBar.enable('#avoir_dupliquer', <?php echo $id; ?>);
            actionMenuBar.enable('#avoir_valider', <?php echo $id; ?>);
            actionMenuBar.enable('#avoir_exporter_pdf', <?php echo $id; ?>);
            actionMenuBar.enable('#avoir_imprimer_pdf', <?php echo $id; ?>);
            actionMenuBar.disable('#enregistrerLignes');
            $(this).hide();
        }).hide();

        $("#grid-constitution-lignes-avoirs").on('click', "a.k-grid-delete", function() {
            actionMenuBar.disable('#avoir_dupliquer');
            actionMenuBar.disable('#avoir_valider');
            actionMenuBar.disable('#avoir_exporter_pdf');
            actionMenuBar.disable('#avoir_imprimer_pdf');
            actionMenuBar.enable('#enregistrerLignes');
            $("#annulerLignes").show();
        })

        $("#grid-constitution-lignes-avoirs").on('change', "input.k-input", function() {
            actionMenuBar.disable('#avoir_dupliquer');
            actionMenuBar.disable('#avoir_valider');
            actionMenuBar.disable('#avoir_exporter_pdf');
            actionMenuBar.disable('#avoir_imprimer_pdf');
            actionMenuBar.enable('#enregistrerLignes');
            $("#annulerLignes").show();
        })

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

        // For the detail links on the page
        $('a.view-detail').click(function(ev) {
            ev.preventDefault();
            actionMenuBar.loadInModal(this, '#template-modal-detail');
        });

        // Initialisation des champs date de formulaire dans la fenêtre modale
        $("input.form-date-field").focus(function() {
            $(this).datetimepicker({
                format:'d/m/Y',
                formatDate:'d/m/Y',
                timepicker: false,
                todayButton: true,
                allowBlank: !$(this).attr('required')
            });
        });

        $('#form-avoir-modification :input').change(function() {
            var form = this.form;
            $.ajax({
                type: "POST",
                url: form.action,
                dataType: "json",
                data: $(form).serialize()
            }).done(
                actionMenuBar.displayNotification,
                actionMenuBar.broadcastOnSuccess('localStorage')
            );
        });

    });

</script>
