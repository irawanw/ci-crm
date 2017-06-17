
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
        <button type="button" class="btn btn-warning btn-xs" id="annulerLignes"><span class="glyphicon glyphicon-refresh"></span> Annuler les changements</button>    </form>
</script>

<!-- Initialisation de la grille -->
<script>
    var arrondi = function(v) {return v.toFixed(2)};
    var tva = <?php echo $tva?>;
    var activite_contact = 0;
    var crudServiceBaseUrl = "<?php echo site_url("devis/manipulation/$id")?>";
    var redirect_action = 'save' ; // 'pdf' or 'save'
    var id_devis = <?php echo $id?> ;
    var etat_devis = <?php echo isset($values) ? $values->dvi_etat : -9 ?> ;


    var datasource = new kendo.data.DataSource({
        transport: {
            read:  {
                url: crudServiceBaseUrl + '/get',
                dataType: "json"
            },
            update:  {
                url: crudServiceBaseUrl + '/update',
                type: "post",
                dataType: "json"
            },
            destroy:  {
                url: crudServiceBaseUrl + '/destroy',
                type: "post",
                dataType: "json"
            },
            create:  {
                url: crudServiceBaseUrl + '/create',
                type: "post",
                dataType: "json"
            }
        },
        sync: function(e) {
            var broadcast = actionMenuBar.broadcastOnSuccess();
            var data = {
                success: true,
                data: {
                    event: {
                        controleur: "devis",
                        type: "recordchange",
                        id: <?php echo $id ?>,
                        timeStamp: Date.now()
                    }
                }
            };
            broadcast(data);
            window.location.replace("<?php echo site_url("devis/lignes/$id")?>");
        },
        batch:true,
        pageSize: 10,
        schema: {
            model: {
                fields: {
                    ard_id: {type: "number"},
                    ard_article: {type: "number"},
                    ard_code: {editable: false},
                    ard_description: {},
                    ard_info: {},
                    ard_prix: {type: "number",editable: false},
                    ard_quantite: {type: "number"},
                    ard_remise_taux: {type: "number"},
                    ard_remise_ht: {type: "number"},
                    ard_remise_ttc: {type: "number"}
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
    var nouvel_article = function(data) {
        datasource.add( {
            ard_id: 0,
            ard_article: data.id,
            ard_code: data.code,
            ard_description: data.description,
            ard_info: data.info,
            ard_prix: data.prix,
            ard_quantite: data.quantite,
            ard_remise_taux: 0,
            ard_remise_ht: 0,
            ard_remise_ttc: 0
        });
        actionMenuBar.disable('#devis_dupliquer');
        actionMenuBar.disable('#devis_passer_commande');
        actionMenuBar.disable('#devis_marquer_refus');
        actionMenuBar.disable('#devis_envoyer_email');
        actionMenuBar.disable('#devis_exporter_pdf');
        actionMenuBar.disable('#devis_imprimer_pdf');
        actionMenuBar.enable('#enregistrerLignes');
        $("#annulerLignes").show();
    };

    $(document).ready(function(){
        $("#grid").kendoGrid({
            toolbar: [
                { template: kendo.template($("#template").html()) }
            ],
            columns: [
                {
                    field: "ard_code",
                    title: "Code",
                    attributes: {style: "text-align: left;"}
                },
                {
                    field: "ard_description",
                    title: "Description",
                    width: "300px",
                    attributes: {style: "text-align: left;"},
                    footerTemplate: '<div style="text-align: left;">Total avant remise<br />Remise</br>Total général</div>',
                    editor: function(container, options) {
                        // default color is white probably due to css directives controlling row selection
                        // so it is imperative to reset color black
                        $('<textarea data-bind="value: ard_description" style="color:black;width: ' + container.width() + 'px;height:' + container.height() + 'px"></textarea>')
                            .appendTo(container) ;
                    },
                    editable: "inline"
                },
                {
                    field: "ard_info",
                    title: "Info",
                    width: "300px",
                    attributes: {style: "text-align: left;"}
                },
                {
                    field: "ard_prix",
                    title: "PUHT",
                    format:"{0:0.000}",
                    attributes: {style: "text-align: right;"},
                    footerTemplate: function() {
                        var lignes = datasource.data();
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].ard_code == 'R') {
                                remise += lignes[i].ard_prix;
                            }
                        }
                        return (remise > 0) ? '<div style="text-align: right;">&nbsp;<br />' + arrondi(remise * 100) + ' %<br />&nbsp;</div>' : '';
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
                    format:"{0:0.00} %",
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
                        var total_brut = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].ard_code != 'R') {
                                total_brut += lignes[i].ard_quantite * lignes[i].ard_prix;
                                total += lignes[i].ard_quantite * lignes[i].ard_prix - lignes[i].ard_remise_ht;
                            }
                        }
                        var remise_globale = 0;
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].ard_code == 'R') {
                                remise_globale += lignes[i].ard_prix;
                            } else {
                                remise += lignes[i].ard_remise_ht;
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
                            if (lignes[i].ard_code != 'R') {
                                total_brut += (lignes[i].ard_quantite * lignes[i].ard_prix) * tva;
                                total += (lignes[i].ard_quantite * lignes[i].ard_prix - lignes[i].ard_remise_ht) * tva;
                            }
                        }
                        var remise_globale = 0;
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].ard_code == 'R') {
                                remise_globale += lignes[i].ard_prix;
                            } else {
                                remise += lignes[i].ard_remise_ht * tva;
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
                            if (lignes[i].ard_code != 'R') {
                                total_brut += (lignes[i].ard_quantite * lignes[i].ard_prix) * (1 + tva);
                                total += (lignes[i].ard_quantite * lignes[i].ard_prix - lignes[i].ard_remise_ht) * (1 + tva);
                            }
                        }
                        var remise_globale = 0;
                        var remise = 0;
                        for (var i = 0; i < lignes.length; i++) {
                            if (lignes[i].ard_code == 'R') {
                                remise_globale += lignes[i].ard_prix;
                            } else {
                                remise += lignes[i].ard_remise_ht * (1 + tva);
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
                var ptht = e.model.ard_prix * e.model.ard_quantite;
                switch (champ[0]) {
                    case "ard_remise_taux":
                        e.model.ard_remise_ht = ptht * e.values.ard_remise_taux / 100;
                        e.model.ard_remise_ttc = e.model.ard_remise_ht * (1 + tva);
                        break;
                    case "ard_remise_ht":
                        e.model.ard_remise_taux = e.values.ard_remise_ht / ptht;
                        e.model.ard_remise_ttc = e.values.ard_remise_ht * (1 + tva);
                        break;
                    case "ard_remise_ttc":
                        e.model.ard_remise_ht = e.values.ard_remise_ttc / (1 + tva);
                        e.model.ard_remise_taux = e.model.ard_remise_ht / ptht;
                        break;
                    case "ard_quantite":
                        var ptht = e.model.ard_prix * e.values.ard_quantite;
                        e.model.ard_remise_ht = ptht * e.model.ard_remise_taux / 100;
                        e.model.ard_remise_ttc = e.model.ard_remise_ht * (1 + tva);
                    default:
                }
                this.refresh() ;
            }
        });

        $("#enregistrerLignes").click(function(e) {
            var grid = $("#grid").data("kendoGrid");
            grid.saveChanges();
            actionMenuBar.enable('#devis_dupliquer', <?php echo $id; ?>);
            actionMenuBar.enable('#devis_passer_commande', <?php echo $id; ?>);
            actionMenuBar.enable('#devis_marquer_refus', <?php echo $id; ?>);
            actionMenuBar.enable('#devis_envoyer_email', <?php echo $id; ?>);
            actionMenuBar.enable('#devis_exporter_pdf', <?php echo $id; ?>);
            actionMenuBar.enable('#devis_imprimer_pdf', <?php echo $id; ?>);
            $("#annulerLignes").hide();
        });

        $("#annulerLignes").click(function(e) {
            var grid = $("#grid").data("kendoGrid");
            grid.cancelChanges();
            actionMenuBar.enable('#devis_dupliquer', <?php echo $id; ?>);
            actionMenuBar.enable('#devis_passer_commande', <?php echo $id; ?>);
            actionMenuBar.enable('#devis_marquer_refus', <?php echo $id; ?>);
            actionMenuBar.enable('#devis_envoyer_email', <?php echo $id; ?>);
            actionMenuBar.enable('#devis_exporter_pdf', <?php echo $id; ?>);
            actionMenuBar.enable('#devis_imprimer_pdf', <?php echo $id; ?>);
            actionMenuBar.disable('#enregistrerLignes');
            $(this).hide();
        }).hide();

        $("#grid").on('click', "a.k-grid-delete", function() {
            actionMenuBar.disable('#devis_dupliquer');
            actionMenuBar.disable('#devis_passer_commande');
            actionMenuBar.disable('#devis_marquer_refus');
            actionMenuBar.disable('#devis_envoyer_email');
            actionMenuBar.disable('#devis_exporter_pdf');
            actionMenuBar.disable('#devis_imprimer_pdf');
            actionMenuBar.enable('#enregistrerLignes');
            $("#annulerLignes").show();
        })

        $("#grid").on('change', "input.k-input", function() {
            actionMenuBar.disable('#devis_dupliquer');
            actionMenuBar.disable('#devis_passer_commande');
            actionMenuBar.disable('#devis_marquer_refus');
            actionMenuBar.disable('#devis_envoyer_email');
            actionMenuBar.disable('#devis_exporter_pdf');
            actionMenuBar.disable('#devis_imprimer_pdf');
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
                    win.bind("open", function(){
                        if (['D','E','I','M','P','V'].indexOf(action) != -1) {
                            $("#form_"+action).prop('disabled', true) ;
                        }
                    });
                    win.open();
                    break;
                default:
            }
            $(this).val('x') ;
        });

        // For the detail links on the page
        $(document).on('click', 'a.view-detail', function(ev) {
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

        $('#form_devis :input').not(':input[name="dvi_client"]').change(function() {
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

        $('#form_devis :input[name="dvi_client"]').change(function() {
            var form = this.form;
            var input = this;
            $.ajax({
                type: "POST",
                url: form.action,
                dataType: "json",
                data: $(form).serialize()
            }).done(
                actionMenuBar.displayNotification,
                actionMenuBar.broadcastOnSuccess('localStorage'),
                function (data) {
                    if (data.success && data.data && data.data.freshData) {
                        var fresh = data.data.freshData;
                        $("#dvi_adresse_client").html('<a class="view-detail" target="_blank" href="<?php echo site_url('contacts/detail'); ?>/'+fresh.ctc_id+'">'+fresh.ctc_nom+"</a><br>"+fresh.ctc_adresse+"<br>"+fresh.ctc_cp+" "+fresh.ctc_ville);
                        if (fresh.emp_nom) {
                            $("#ctc_commercial").html('<a class="view-detail" target="_blank" href="<?php echo site_url('employes/detail'); ?>/'+fresh.ctc_commercial+'">'+fresh.emp_nom+"</a>");
                        } else {
                            $("#ctc_commercial").html('');
                        }
                        var options = '<option value="">(choisissez)</option>';
                        var correspondants = fresh.dvi_correspondant_options;
                        for (var i = 0; i < correspondants.length; ++i) {
                            options += '<option value="'+correspondants[i].cor_id+'">'+correspondants[i].cor_nom+'</option>';
                        }
                        $("#dvi_correspondant").html(options);
                    }
                }
            );
        });

        $("#dvi_client").kendoDropDownList({
            index: 0,
            minLength: 2,
            filter:"contains",
            optionLabel: "Sélectionnez un client ..."
        });
        var contacts = $("#dvi_client").data('kendoDropDownList');
        var width = $("#dvi_correspondant").width();
        contacts.list.width(width);
        $("#dvi_client").parent().width(width);

    });

</script>