
<!-- P R E P A R E    U I   C O N T R O L S -->
<script type="text/javascript">
    /* global $ */
    var _masseActionsPrepared =  0 ; //false;
                                     // 0:false; -1:in progress, 1:done
    var _contentieux_row = null;
    function prepareMasseActions() {
        if ( _masseActionsPrepared != 0 /* != false*/) return;
        _masseActionsPrepared = -1;  //true; // -1 = in progress
        //console.log( "Lets populate masse actions.." );
        var select = $('.masse_actions_select');
        select.append('<option value="M">Masquer</option> <option value="R1M">Relance 1 (mail)</option> <option value="R1L">Relance 1 (courrier)</option> \
                        <option value="R1A">Relance 1 (mail+courrier)</option> <option value="R2M">Relance 2 (mail)</option> <option value="R2L">Relance 2 (courrier)</option> \
                        <option value="R2A">Relance 2 (mail+courrier)</option> <option value="R3M">Relance 3 (mail)</option> <option value="R3L">Relance 3 (courrier)</option> \
                        <option value="R3A">Relance 3 (mail+courrier)</option> <option value="LRL">Lettre recommandée</option> <option value="MRL">LR menace référé</option> \
                        <option value="SB">Effacer la surbrillance</option> <option value="SO">Mettre en surbrillance orange</option> <option value="SV">Mettre en surbrillance vert</option>');

        // actions de masse
        $("#btn_action_all").click(function(e) {
            var action = $(".masse_actions_select" ).val();
            var checkedRows = [];
            DT.column( CBSelect_column_id ).nodes().each( function ( value, index ) {
                var cb = $(value).find('input.dt_checkbox');
                if ( cb.prop('checked') ) {
                    var aRow = DT.row( cb.parent().parent() );
                    checkedRows.push( DT.row(aRow.index()) );
                    //console.log(DT.row(aRow.index()).index());
                    var row_data = row_data_map.get(DT.row(aRow.index()).id());
                    //console.log(row_data);
                    //console.log(row_data.get('dvi_id'));
                    cb.prop('checked', false);
                }
            } );
            if ( action!='' && action!='x' ) {
                //console.log('Calling ' + action + ' on ['+ checkedRows +']');
                call_server_action(action, checkedRows );
                $('#masse_actions_cb').prop('checked', false);
                $(".masse_actions_select" ).prop("selectedIndex", 0);
            }
        });

        // voir toutes les factures
        $('.masse_actions').append('<button type="button" class="btn btn-default btn-xs" id="btn_view_all">Toutes les factures</button>'+
                                    '&nbsp;&nbsp;&nbsp;&nbsp;');
        $("#btn_view_all").on('click', function(e) {
            quick_call_filters( [{ field: "due", operator: "gte", value: 0 }]);
        });

        // voir les factures impayées
        $('.masse_actions').append('<button type="button" class="btn btn-default btn-xs" id="btn_view_imp">Factures impayées</button>'+
                                    '&nbsp;&nbsp;&nbsp;&nbsp;');
        $("#btn_view_imp").on('click', function(e) {
            quick_call_filters( [{ field: "due", operator: "eq", value: 1 },
                                 { field: "fac_masque", operator: "eq", value: 0 }] );
        });

        // démasquage global
        $('.masse_actions').append('<button type="button" class="btn btn-default btn-xs" id="btn_demask_all">Démasquer (relances)</button>'+
                                    '&nbsp;&nbsp;&nbsp;&nbsp;');
        $("#btn_demask_all").on('click', function(e) {
            //console.log('#btn_demask_all');
            /* Data only available on server in server mode
            DT.rows().every(function() {
                var row = this;
                var record = row_data_map.get(row.id());
                if ( record.get('fac_masque')==1 ) {
                    console.log(record);
                    record.set('fac_masque',0);
                    sync(row, record);
                }
            });*/
            $.ajax({
                url: '<?php echo site_url("factures/impayees")?>/demasque_all',
                type: 'get',
                dataType: 'json'
            }
            )
            .done(  function(data){
                //console.log(data);
                DT.ajax.reload();
            } );

        });

        DT.columns().every(function(colIdx){
            $(DT.column( colIdx ).header()).find('.select_mask').each(function(){
                //console.log("SELECT: ",$(DT.column( colIdx ).header()).attr('data'), this);
                var cur_select = this;
                $(cur_select).find('option').remove();
                $(cur_select).append('<option value="">(sélectionner)</option>');
            <?php
                    foreach($descripteur['select_etats'] as $e) {
            ?>
                    $(cur_select).append($('<option>').text('<?php echo $e->vef_etat; ?>'));
            <?php   } ?>
            });
        });
        _masseActionsPrepared = 1; // 1 = completed
    };

    function prepareRowCustomControls(row) {
        //console.log('Populating custom contols in row '+row.index()+' [row id: '+row.id()+']');
        var select = $(row.node()).find('.dt_select');
        select.attr('rel_id', row.id());
        select.find('option').remove();
        select.append('<option value="x">(choisir)</option> <option value="M">Masquer</option> <option value="R1M">Relance 1 (mail)</option> \
                        <option value="R1L">Relance 1 (courrier)</option> <option value="R1A">Relance 1 (mail+courrier)</option> <option value="R2M">Relance 2 (mail)</option> \
                        <option value="R2L">Relance 2 (courrier)</option> <option value="R2A">Relance 2 (mail+courrier)</option> <option value="R3M">Relance 3 (mail)</option> \
                        <option value="R3L">Relance 3 (courrier)</option> <option value="R3A">Relance 3 (mail+courrier)</option> <option value="LRL">Lettre recommandée</option> \
                        <option value="MRL">LR menace référé</option> <option value="RF">Info contentieux</option> <option value="CR">Rappel</option>');
        select.unbind('change');
        select.on('change', function() {
            var func_name = $(this).val();
            $(this).prop("selectedIndex", 0);
            //console.log( 'Perform quick action '+ func_name +' on: '+ $(this).attr('rel_id') );
            if ( func_name!='' && func_name!='x' ) {
                //console.log('Calling ' + func_name);
                call_server_action(func_name, new Array(DT.row( select.parent().parent() )));
            }
        });

        $(row.node()).find('.dt_href').each(function(){
            var link = $(this);
            var url = link.attr('href');
            var url_suffix = row_data_map.get(row.id()).get(link.attr('ref'));
            //console.log(url_suffix);
            url = url.endsWith('/') ? url : url+'/' ;
            link.attr('href', url+url_suffix);
        });
        $(row.node()).find('.dt_hreflist').each(function(){
            var link = $(this);
            var url = link.attr('href');
            var url_suffix = row_data_map.get(row.id()).get(link.attr('ref'));
            var url_suffix_index = link.attr('ref_index');
            //console.log(url_suffix, url_suffix_index);
            url = url.endsWith('/') ? url : url+'/' ;
            var url_suffices_array = url_suffix.split(':');
            //link.attr('href', url+url_suffix+'INDEX:'+url_suffix_index);
            link.attr('href', url+url_suffices_array[url_suffix_index]);
        });

    }

    function prepareColumnsCustomControls() {
        DT.columns().every(function(colIdx){
            //console.log(colIdx);
            //console.log($(DT.column( colIdx ).header()).attr('data'));
            if ( $(DT.column( colIdx ).header()).attr('data')=='fac_notes' ) {
                DT.column( colIdx )
                    .nodes()
                    .to$()
                    .addClass( 'note_action' );
            } else if ( $(DT.column( colIdx ).header()).attr('data')=='fac_rappel' ) {
                DT.column( colIdx )
                    .nodes()
                    .to$()
                    .addClass( 'rappel_action' );
            }
        });

    }

    function prepareRowCustomStyling(row, row_data) {
        apply_styling_etat(   row, row_data.get('fac_etat'));
        apply_styling_couleur(row, row_data.get('fac_couleur'))
    }

    function apply_default_filters() {
        quick_call_filters( [{ field: "fac_masque", operator: "eq", value: 0 }]);
    }

    function apply_styling_etat(row, etat) {
        $(row.node()).removeClass('non-valide');
        $(row.node()).removeClass('valide');
        if ( etat==1 ) {
            $(row.node()).addClass('non-valide');
        }
        else {
            $(row.node()).addClass('valide');
        }
    }
    function apply_styling_couleur(row, couleur) {
        for ( var i=0; i<3; i++)
            $(row.node()).removeClass('surbrillance-'+i);
        $(row.node()).addClass('surbrillance-'+couleur);
    }
</script>

<!-- D A T A   F U N C T I O N S -->
<script type="text/javascript" >
    /** Handles a drop-down memu selected action on one or more rows
     *  @param action: The key of the action that has been selected
     *  @param rows: An Array of DT rows on which the action is to be performed
     */
    function call_server_action(action, rows) {
        var data = [];
        rows.forEach(function(row, index) {
            var record = row_data_map.get(row.id());
            console.log(action, record.get('RowID'));
            var sauver = false;
            var on_sucess_reload_table = false;
            switch(action) {
                case "M":
                    record.set("fac_masque",1);
                    sauver = true;
                    //quick_call_filters( [{ field: "fac_masque", operator: "eq", value: 0 }]);
                    on_sucess_reload_table = true;
                    break;
                case "R1M":
                case "R1L":
                case "R1A":
                case "R2M":
                case "R2L":
                case "R2A":
                case "R3M":
                case "R3L":
                case "R3A":
                case "LRL":
                case "MRL":
                    relance(action, row, record);
                    break;
                case "SB":
                    record.set("fac_couleur",0);
                    apply_styling_couleur(row, 0);
                    sauver = true;
                    break;
                case "SO":
                    record.set("fac_couleur",1);
                    apply_styling_couleur(row, 1);
                    sauver = true;
                    break;
                case "SV":
                    record.set("fac_couleur",2);
                    apply_styling_couleur(row, 2);
                    sauver = true;
                    break;
                case "CR":
                    afficher_rappel(row,record);
                    break;
                case "RF":
                    _contentieux_row = row;
                    $('#popup_2_fac_id').html(record.get('fac_id'));
                    $('#popup_2').modal('show');
                    break;
            }
            if (sauver) {
                data.push({
                    row: row,
                    record: record
                });
            }
        });
        if (data.length > 0) {
            syncMulti(data);
        }
    }

    function syncMulti(data) {
        // Action to perform only when every row was processed
        var onComplete = function(length) {
            var counter = 0;
            return function(data) {
                ++counter;
                if (counter == length) {
                    var broadcast = actionMenuBar.broadcastOnSuccess('localStorage');
                    var event = {
                        success: true,
                        notif: 'success',
                        message: 'La facture a été modifiée',
                        data: {
                            event: {
                                controleur: 'factures',
                                type: 'recordchange',
                                timeStamp: Date.now()
                            }
                        }
                    };
                    broadcast(event);
                }
            }
        };
        var callback = onComplete(data.length);
        data.forEach(function(elem) {
            sync(elem.row, elem.record, callback);
        });
    }

    /** Syncs with server a record after a drop-down menu selected action
     *  @param row: The DT table row (is DT object not simple html object)
     *  @param record: The updated record; these values are submitted to the server.
     *  @returns true|false; on true prior to return the table html is updated to reflect changes
     */
    function sync(row, record, on_sucess_reload_table) {
        //console.log('Sync record of row '+row.id()+':', record);
        //console.log(record);

        //console.log(row.node());
        var synced_cb = $(row.node()).find('input');
        //console.log(synced_cb);
        synced_cb.attr("disabled", true);
        synced_cb.hide();
        synced_cb.parent().append('<img class="loading_anim" src="<?php echo base_url('assets/images/loading-circles-30.gif')?>"/>');

        var record_as_array = {"epoch": (Math.round(new Date().getTime()/1000))};
        for (var [key, value] of record) {
            record_as_array[key] = value;
        }
        //console.log('record_as_array: ', record_as_array);
        $.ajax({
                url: '<?php echo site_url("factures/impayees")?>/update',
                type: 'get',
                dataType: 'json',
                data: record_as_array
            }
        )
        .done(  function(data){
            // Get fresh copy of data
            //console.log('Refresh: ', record_as_array);
            $.ajax({
                    url: '<?php echo site_url("factures/impayees")?>/get',
                    type: 'get',
                    dataType: 'json',
                    data: record_as_array
                }
            )
            .done( function(data) {     //console.log('SUCCESS getting updated record: ', data);
                if ( typeof data[0] === 'undefined' || typeof data[0] !== 'object') {
                    notificationWidget.show(":-( Failed to reload updated record ("+ record.get('dvi_id') +"); UI is out of sync with database. Need to reload manually.","error");
                    return;
                }
                var new_data_array = data[0];
                //console.log('In sync new_data_array: ', new_data_array);
                var cells_updated = 0;
                DT.columns().every( function() {
                    //console.log( 'Update Row: '+row.id()+' Column: '+this.index());
                    var cell = DT.cell(row.index(), this.index());
                    //console.log(cell.node());
                    var header = DT.column(this.index()).header()
                    var data_key = $(header).attr('data');
                    if ( $.inArray(data_key, ['RowID', 'CBSelect'] ) <0 ) {
                        if ( typeof data_key !== 'undefined') {
                            var old_data = cell.data();
                            var new_data = new_data_array[data_key];
                            if ( new_data == null || typeof new_data === 'undefined' )
                                new_data = '';
                            if ( old_data != new_data ) {
                                //console.log( 'Update Row: '+row.id()+' Column: '+this.index()+'; Data key is '+data_key+'; Old data= '+old_data +'\nNew data='+ new_data );
                                cell.data( new_data );
                                cells_updated++;
                            }
                        }
                    }
                    //console.log(cell.data());
                } );
                if ( cells_updated > 0) {
                    if ( on_sucess_reload_table ) {
                        DT.ajax.reload();
                    }
                    else {
                        //DT.draw();
                        //DT.ajax.reload();
                        //console.log( row_data_map.get(row.id()));
                        //console.log(typeof new_data_array);
                        var new_map = objectToMap(new_data_array);
                        row_data_map.set(row.id(), new_map);
                        //new_map.set('RowID', new_map.get('dvi_id'));
                        //console.log( row_data_map.get(row.id()));
                        //apply_styling_etat(row, new_map.get('dvi_etat'));
                        prepareRowCustomStyling(row, new_map);
                        prepareRowCustomControls(row);

                        $("#datatable").colResizable({ disable:true });
                        dt_init_resizable();
                        $('div.dataTables_scrollHeadInner').find('table').css('margin-right', getScrollBarWidth());
                        $('div.dataTables_scrollHeadInner').find('table').width($('#datatable.JColResizer').width());
                        dt_persist_state();
                        dt_reapply_column_widths(); // to sync headers
                        dt_persist_state();
                        $('.JCLRgrip').height($('div.dataTables_scrollBody').find('div').last().height());
                    }
                }
                synced_cb.parent().find('img.loading_anim').remove();
                synced_cb.show();
                synced_cb.removeAttr("disabled");
                notificationWidget.show(":-) Enregistrement "+ record.get('RowID') +" mis à jour.","success");
            })
            .fail( function(data) {     console.log('FAILed to get updated record '+(record_as_array.RowID)); console.log(data);
                notificationWidget.show(":-( Failed to reload updated record ("+ record.get('RowID') +"); UI is out of sync with database. Need to reload manually.","error");
            });
            }
        )
        .fail(  function(data){
            console.log(data.responseText);
            notificationWidget.show("Record "+ record.get('RowID') +" failed to update!","error");
            }
        )
        .always(function(){
            }
        );
    }

    // impression de masse des factures
    $("#impression").click(function(e){
        var pdfs = [];
        var baseUrl = "<?php echo base_url(); ?>";
        var location = "fichiers/factures/";
        var basePath = "<?php echo addslashes(FCPATH); ?>";

        grid.tbody.find("input:checked").closest("tr").each(function (index) {
            var href = $($.parseHTML(grid.dataItem($(this)).fac_fichier)).attr('href');
            if(href != undefined) pdfs[pdfs.length] = href;
        });
        if(pdfs.length){
            pdfs = $.map(pdfs, function(val){
                return val.replace(baseUrl+location, '');
            });

            $.post("<?php echo site_url("factures/merge_pdfs")?>", {path: basePath+location, pdfs: pdfs}, function(response) {
                if (response) {
                    window.open(baseUrl + location + response, '_blank');
                }
            });
        }
    });

</script>
<!-- U I   F U N C T I O N S -->
<script type="text/javascript">

    // sélection / déselection
    $("#check-all").click(function(e) {
        var checkbox = $(this);
        var valeur = checkbox.is(":checked");
        DT.rows().every( function() {
            //console.log( 'Update Row: '+row.id()+' Column: '+this.index());
            var cell = DT.cell(this.index(), 1);
            //console.log(cell.node());
            $(cell.node()).find('input:checkbox').prop("checked", valeur);
        });

    });

    _note_row = null; //global var used by the popup_1 modal

    // edition des remarques
    $(document).on('click', '.note_action', function (e) {
        var htmlRow = $(this).closest("tr");
        var thisRow = DT.row( this );
        _note_row = thisRow;
        var id = thisRow.id();
        var record = row_data_map.get(id);
        $('#popup_1_fac_id').html(record.get('fac_id'));
        $('#popup_1_notes').val(record.get('fac_notes'));
        $('#popup_1').modal('show');
    });

    // fonction de renvoi
    var renvoi = function(type, row, record) {
        $.ajax({
            method:"POST",
            url:"<?php echo site_url("devis/renvoi")?>",
            data: {"type":type,"devis":record.get('dvi_id')},
            success: function(reponse) {
                // NB: NOT TESTED for type=RI due to SMTP  error
                if (reponse != false) {

                    // mise à jour du champ relance
                    var texte = record.get('dvi_relance');
                    if (texte == '') {
                        texte = '<table>' + reponse + '</table>';
                    }
                    else {
                        texte = texte.slice(0,-8) + reponse + '</table>';
                    }
                    record.set("dvi_relance", texte);
                    sync(row, record); // sauve
                    notificationWidget.show("Le devis a été renvoyé", "success");
                }
                else {
                    notificationWidget.show("Pas d'adresse email pour l'envoi du devis", "error");
                }
            },
            error: function() {
                notificationWidget.show("Erreur lors du renvoi du devis","error");
            }
        });
    };

    _rappel_row = null; //global var used by the popup_*_rappel modals
    // edition de rappel sur clic long
    $(document).on("mousedown", '#datatable_wrapper .rappel_action', function (e) {
        //e.preventDefault();
        //var htmlRow = $(this).closest("tr");
        var row = DT.row( this );
        var id = row.id();
        var record = row_data_map.get(id);
        $(this).data('longclick', setTimeout(function() {
            afficher_rappel(row, record);
        }, 300));
    }).on('mouseup', '#datatable_wrapper .rappel_action', function() {
        clearTimeout($(this).data('longclick'));
    }).on('mouseout', '#datatable_wrapper .rappel_action', function() {
        clearTimeout($(this).data('longclick'));
    });

    // affichage de la fenêtre de rappel
    function afficher_rappel(row, record) {
        $('#popup_fac_id').html(record.get('fac_id'));
        _rappel_row = row;
        $('#popup_societe').html(record.get('ctc_nom'));
        var tTTC = ((parseFloat(record.get('total_TTC'))+0.0).format(2, 3, '.', ','))
        $('#popup_ttc').html(tTTC);
        $('#popup_numero').html(record.get('fac_reference'));
        var datefac = (new Date( record.get('fac_date') ) ).ddmmyyyy();
        $('#popup_datefac').html(datefac);
        $('#popup_info').val('F|'+record.get('fac_id')+'|'+record.get('ctc_nom')+' - '+record.get('fac_reference')+' - '+datefac+' - '+record.get('solde_du')+' €');
        if (record.get('fac_id_rappel') > 0) {
            var date_rappel = new Date( record.get('fac_rappel') );
            var now = new Date();
            if (date_rappel > now) {
                $("#popup_date").val(("0" + date_rappel.getDate()).slice(-2) + "/" + ("0"+(date_rappel.getMonth()+1)).slice(-2) + "/" + date_rappel.getFullYear());
                $("#popup_heure").val(('0'+date_rappel.getHours()).slice(-2)+('0'+date_rappel.getMinutes()).slice(-2));
                $("#popup_comment").val(record.get('texte_rappel'));
                $("#popup_creer_rappel").hide();
                $("#popup_modifier_rappel").show();
                $("#popup_supprimer_rappel").show();
            }
            else {
                date_rappel = now;
                $("#popup_date").val(("0" + date_rappel.getDate()).slice(-2) + "/" + ("0"+(date_rappel.getMonth()+1)).slice(-2) + "/" + date_rappel.getFullYear());
                $("#popup_heure").val('');
                $("#popup_comment").val('');
                $("#popup_creer_rappel").show();
                $("#popup_modifier_rappel").hide();
                $("#popup_supprimer_rappel").hide();
            }
        }
        else {
            $("#popup_creer_rappel").show();
            $("#popup_modifier_rappel").hide();
            $("#popup_supprimer_rappel").hide();
        }
        $('#popup_0').modal('show');
    };

    // fonction de relance
    function relance(type, row, record) {
        var id = record.get('fac_id');
        //console.log(type, record.get('fac_id'));
        $.ajax({
            method:"POST",
            url:"<?php echo site_url("factures/relance")?>",
            data: {"type":type,"facture":id},
            success: function(reponse) {
                //console.log(reponse);
                if (reponse != false) {

                    // mise à jour du champ relance
                    var texte = record.get('fac_relance');
                    if (texte == '') {
                        texte = '<table>' + reponse + '</table>';
                    }
                    else {
                        texte = texte.slice(0,-8) + reponse + '</table>';
                    }
                    record.set("fac_relance",texte);
                    sync(row, record); // sauve
                    notificationWidget.show("La relance a été envoyée","success");

                    var broadcast = actionMenuBar.broadcastOnSuccess('localStorage');
                    var event = {
                        success: true,
                        notif: 'success',
                        message: 'La relance a été envoyée',
                        data: {
                            event: {
                                    controleur: 'factures',
                                    type: 'recordchange',
                                    id: id,
                                    timeStamp: Date.now()
                            }
                        }
                    };
                    broadcast(event);
                }
                else {
                    notificationWidget.show("Pas d'envoi de la relance","warning");
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                notificationWidget.show("Erreur lors de l'envoi de la relance","error");
                console.log(xhr, ajaxOptions, thrownError);
            }
        });
    };

</script>
<!-- D O C U M E N T   R E A D Y   F U N C T I O N S -->
<script type="text/javascript">
    $(document).ready(function(){

        // Quand une ligne est ajoutée à la datatable, il faut construire les éléments dynamiques
        // Note : Pour l'instant, ce n'est pas un vrai objet Event
        actionMenuBar.datatable.on('loaded', function(event) {
            var map = objectToMap(event.data);
            row_data_map.set(event.id, map);
            var row = $('#datatable').DataTable().row(actionMenuBar.datatable.$row(event.id));
            prepareRowCustomStyling(row, map);
            prepareRowCustomControls(row);
        });

        // Redéfinition de quand les boutons sont activés dans la barre action
        actionMenuBar.datatable.buttonStatus = function(id) {
            var data = actionMenuBar.datatable.data(id);
            var etat = parseInt(data.get('fac_etat'), 10);

            // Fonction de status de bouton
            return function(button) {
                switch (button.id) {
                    case 'facture_valider':
                    case 'facture_modification':
                    case 'facture_lignes':
                    case 'facture_supprimer':
                        return (etat == 1);

                    case 'facture_transferer_avoir':
                        return (etat == 2);

                    case 'facture_envoyer_email':
                    case 'facture_marquer_transmise':
                        return (etat != 1);

                    case 'facture_saisir_reglement':
                        return (etat != 1);
                }
                // Tous les autres boutons sont activés par défaut
                return true;
            }
        };

        // Redéfinition de la fonction qui génère les paramètres des boutons dans la barre action
        // C'est nécessaire pour les factures pour l'action "Saisir règlement"
        actionMenuBar.datatable.buttonParams = function(id) {
            var data = actionMenuBar.datatable.data(id);
            var contact_id = data.get('dvi_client');

            // Fonction de paramètres de bouton
            return function(button) {
                switch (button.id) {
                    case 'facture_saisir_reglement':
                        // Pour la présélection de la facture dans la liste des factures à choisir
                        // dans la popup pour un nouveau règlement.
                        reglements_form.factureId = id;

                        return contact_id+'/'+id;
                }
                // Tous les autres boutons prennent simplement l'id dans l'URL
                return id;
            }
        };

        // création de rappel
        $("#popup_creer_rappel").on('click', function(){
            var row = _rappel_row;
            var id = $("#popup_fac_id").text();
            var heure = $("#popup_heure").val() + '0000';
            heure = heure.substring(0, 2) + ':' + heure.substring(2,4);
            var rappel = $("#popup_date").val() + ' ' + heure;
            var info = $("#popup_info").val();
            var comment = $("#popup_comment").val();
            $.ajax({
                method:"POST",
                url:"<?php echo site_url("factures/rappel/0")?>",
                data: {"id":id,"rappel":rappel,"info":info,"comment":comment},
                success: function(reponse) {
                    //console.log(reponse);
                    if (reponse != false) {
                        // mise à jour du champ rappel
                        var record = row_data_map.get(id); //grid.dataSource.get(id);
                        var rappel_iso_8601 = rappel.substr(6,4)+'-'+rappel.substr(3,2)+'-'+rappel.substr(0,2)+rappel.substr(10);
                        //console.log(rappel_iso_8601);
                        record.set("fac_rappel",rappel_iso_8601);
                        record.set("fac_id_rappel",reponse);
                        record.set("texte_rappel",comment);
                        sync(row, record); // sauve
                        notificationWidget.show("Le rappel a été créé","success");

                        var broadcast = actionMenuBar.broadcastOnSuccess('localStorage');
                        var event = {
                            success: true,
                            notif: 'success',
                            message: 'Le rappel a été créé',
                            data: {
                                event: [
                                    {
                                        controleur: 'taches',
                                        type: 'recordadd',
                                        id: reponse,
                                        timeStamp: Date.now()
                                    },
                                    {
                                        controleur: 'factures',
                                        type: 'recordchange',
                                        id: id,
                                        timeStamp: Date.now()
                                    }
                                ]
                            }
                        };
                        broadcast(event);
                    }
                    else {
                        notificationWidget.show("Le rappel n'a pu être créé","error");
                    }
                },
                error: function() {
                    notificationWidget.show("Erreur lors de la création du rappel","error");
                }
            });
        });

        // modification de rappel
        $("#popup_modifier_rappel").click(function(e) {
            var row = _rappel_row;
            var id = $("#popup_fac_id").text();
            var record = row_data_map.get(id); //grid.dataSource.get(id);
            //console.log(record);
            var heure = $("#popup_heure").val() + '0000';
            heure = heure.substring(0, 2) + ':' + heure.substring(2,4);
            var rappel = $("#popup_date").val() + ' ' + heure;
            var info = $("#popup_info").val();
            var comment = $("#popup_comment").val();
            $.ajax({
                method:"POST",
                url:"<?php echo site_url("factures/rappel/2")?>",
                data: {"id":record.get('fac_id_rappel'),"rappel":rappel,"info":info,"comment":comment},
                success: function(reponse) {
                    if (reponse != false) {
                        //console.log(reponse);
                        // mise à jour du champ rappel
                        var rappel_iso_8601 = rappel.substr(6,4)+'-'+rappel.substr(3,2)+'-'+rappel.substr(0,2)+rappel.substr(10);
                        record.set("fac_rappel",rappel_iso_8601);
                        record.set("fac_id_rappel",reponse);
                        record.set("texte_rappel",comment);
                        //console.log(record);
                        sync(row, record); // sauve
                        notificationWidget.show("Le rappel a été modifié","success");

                        var broadcast = actionMenuBar.broadcastOnSuccess('localStorage');
                        var event = {
                            success: true,
                            notif: 'success',
                            message: 'Le rappel a été modifié',
                            data: {
                                event: [
                                    {
                                        controleur: 'taches',
                                        type: 'recordchange',
                                        id: reponse,
                                        timeStamp: Date.now()
                                    },
                                    {
                                        controleur: 'factures',
                                        type: 'recordchange',
                                        id: id,
                                        timeStamp: Date.now()
                                    }
                                ]
                            }
                        };
                        broadcast(event);
                    }
                    else {
                        notificationWidget.show("Le rappel n'a pu être modifié","error");
                    }
                },
                error: function() {
                    notificationWidget.show("Erreur lors de la modification du rappel","error");
                }
            });
        });


        // suppression de rappel
        $("#popup_supprimer_rappel").click(function(e) {
            var row = _rappel_row;
            var id = $("#popup_fac_id").text();
            var record = row_data_map.get(id); //grid.dataSource.get(id);
            $.ajax({
                method:"POST",
                url:"<?php echo site_url("factures/rappel/1")?>",
                data: {"id":record.get('fac_id_rappel')},
                success: function(reponse) {
                    if (reponse != false) {
                        $("#popup_heure").val('');

                        // mise à jour du champ rappel
                        record.set("fac_couleur",0);
                        record.set("fac_rappel",'');
                        record.set("fac_id_rappel",0);
                        record.set("texte_rappel",'');
                        sync(row, record); // sauve
                        notificationWidget.show("Le rappel a été supprimé","success");

                        var broadcast = actionMenuBar.broadcastOnSuccess('localStorage');
                        var event = {
                            success: true,
                            notif: 'success',
                            message: 'Le rappel a été supprimé',
                            data: {
                                event: [
                                    {
                                        controleur: 'taches',
                                        type: 'recordchange',
                                        id: reponse,
                                        timeStamp: Date.now()
                                    },
                                    {
                                        controleur: 'factures',
                                        type: 'recordchange',
                                        id: id,
                                        timeStamp: Date.now()
                                    }
                                ]
                            }
                        };
                        broadcast(event);
                    }
                    else {
                        notificationWidget.show("Le rappel n'a pu être supprimé","error");
                    }
                },
                error: function() {
                    notificationWidget.show("Erreur lors de la suppression du rappel","error");
                }
            });
        });

        // impression de masse des factures
        $("#impression").click(function(e){
            var pdfs = [];
            var baseUrl = "<?php echo base_url(); ?>";
            var location = "fichiers/factures/";
            var basePath = "<?php echo addslashes(FCPATH); ?>";

            DT.column( CBSelect_column_id ).nodes().each( function ( value, index ) {
                var cb = $(value).find('input.dt_checkbox');
                if ( cb.prop('checked') ) {
                    var aRow = DT.row( cb.parent().parent() );
                    //console.log(DT.row(aRow.index()).index());
                    var row_data = row_data_map.get(DT.row(aRow.index()).id());
                    var href = $($.parseHTML(row_data.get('fac_fichier'))).attr('href');
                    if(href != undefined) pdfs[pdfs.length] = href;
                }
            } );

            if(pdfs.length){
                pdfs = $.map(pdfs, function(val){
                    return val.replace(baseUrl+location, '');
                });
                $.post("<?php echo site_url("factures/merge_pdfs")?>", {path: basePath+location, pdfs: pdfs}, function(response){
                    if(response) {
                        window.open(baseUrl+location+response,'_blank');
                    }
                    else {
                        notificationWidget.show("Erreur technique lors de la préparation du PDF","error");
                    }
                }).error(function() {
                    notificationWidget.show("Erreur technique lors de la préparation du PDF","error");
                });
            }
            return false;
        });

        // enregistrement des remarques
        $("#popup_1_sauver").on('click', function(e) {
            var id = $("#popup_1_fac_id").text();
            var record = row_data_map.get(id);
            record.set("fac_notes",$("#popup_1_notes").val());
            sync(_note_row, record);
        });

        // enregistrement des referes
        //_contentieux_row = null; //global
        $("#popup_2_sauver").click(function(e) {
            var id = $("#popup_2_fac_id").text();
            var date_texte = $("#popup_date_refere").val();
            var texte = date_texte + '&nbsp;' + $("#popup_refere").val();
            var record = row_data_map.get(id);
            var texte_orig = record.get('fac_contentieux');
            if (texte_orig != '') {
                texte = texte_orig + '<br />' + texte;
            }
            record.set("fac_contentieux",texte);
            sync(_contentieux_row, record);
        });

        // mise en évidence d'une facture faisant l'objet d'un rappel
        function mise_evidence_facture(id) {
            var record = row_data_map.get(id);
            var row = null;
            DT.rows().every(function() {
                if ( id == this.id() )
                    row = this;
            });
            if ( row == null ) {
                notificationWidget.show("Enregistrement "+id+" non trouvé dans la table","error");
            }
            else {
                record.set("fac_couleur",3);
                sync(row, record);
                var sort_id = -1;
                DT.columns().every(function(colIdx){
                    if ( $(DT.column( colIdx ).header()).attr('data')=='dvi_couleur' ) {
                        sort_id = colIdx;
                    }
                });
                if ( sort_id!=-1 ) {
                    DT.order([sort_id, "desc"]).draw();
                }
                else {
                    notificationWidget.show("Impossible de trier par couleur","error");
                }
            }
        };

        // TODO: Test!!!!!!
        $("#popup_heure").keydown(function(e) {
            var heure = $(this).val();
            if (e.which == 8) return;
            var char = e.which - 48;
            if (char >= 48 && char < 58) {
                char -= 48;
            }
            if (char < 0 || char > 9) {
                return false;
            }
            switch(heure.length) {
                case 0:
                    if (char > 2) return false;
                    break;
                case 1:
                    if (heure == 2 && char > 3) return false;
                    break;
                case 2:
                    if (char > 5) return false;
                    break;
                case 3:
                    break;
                default:
                    return false;
            }
        });

    });

</script>