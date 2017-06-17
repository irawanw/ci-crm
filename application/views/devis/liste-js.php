
<!-- P R E P A R E    U I   C O N T R O L S -->
<script type="text/javascript">
    /* global $ */
    var _masseActionsPrepared =  0 ; //false;
                                     // 0:false; -1:in progress, 1:done
    function prepareMasseActions() {
        if ( _masseActionsPrepared != 0 /* != false*/) return;
        _masseActionsPrepared = -1;  //true; // -1 = in progress
        
        // Set up custom UI elements & register event functions!

        var select = $('.masse_actions_select');
        select.append('<option value="x">(choisir)</option> <option value="A">Archiver suite refus</option> <option value="C1">Chaleur 1</option> <option value="C2">Chaleur 2</option> \
                        <option value="C3">Chaleur 3</option> <option value="SB">Effacer la surbrillance</option> <option value="SR">Mettre en surbrillance</option> \
                        <option value="RM">Renvoyer devis mail</option> <option value="RI">Imprimer devis</option>');

        // actions de masse
        $("#btn_action_all").click(function(e) {
            var action = $(".masse_actions_select" ).val();
            var checkedRows = [];
            DT.column( CBSelect_column_id ).nodes().each( function ( value, index ) {
                var cb = $(value).find('input.dt_checkbox');
                if ( cb.prop('checked') ) {
                    var aRow = DT.row( cb.parent().parent() );
                    checkedRows.push( DT.row(aRow.index()) );
                    var row_data = row_data_map.get(DT.row(aRow.index()).id());
                    cb.prop('checked', false);
                }
            } );
            if ( action!='' && action!='x' ) {
                call_server_action(action, checkedRows );
                $('#masse_actions_cb').prop('checked', false);
                $(".masse_actions_select" ).prop("selectedIndex", 0);
            }
        });

        // voir tous les devis
        $('.masse_actions').append('<button type="button" class="btn btn-default btn-xs" id="btn_view_all">Tous les devis</button>'+
                                    '&nbsp;&nbsp;&nbsp;&nbsp;');
        $("#btn_view_all").click(function(e) {
            quick_call_filters( [{ field: "dvi_etat", operator: "gte", value: 0 }]);
        });

        // impression de masse des devis
        $("#impression").click(function(e) {
            var pdfs = [];
            var baseUrl = "<?php echo base_url(); ?>";
            var location = "fichiers/devis/";
            var basePath = "<?php echo addslashes(FCPATH); ?>";
        });

        // voir les devis à relancer
        $('.masse_actions').append('<button type="button" class="btn btn-default btn-xs" id="btn_view_rel">Devis à relancer</button>'+
                                    '&nbsp;&nbsp;&nbsp;&nbsp;');
        $("#btn_view_rel").click(function(e) {
            quick_call_filters( [{ field: "dvi_etat", operator: "eq", value: 3 }]);
        });

        DT.columns().every(function(colIdx){
            $(DT.column( colIdx ).header()).find('.select_mask').each(function(){
                //console.log("SELECT: ",$(DT.column( colIdx ).header()).attr('data'), this);
                var cur_select = this;
                $(cur_select).find('option').remove();
                $(cur_select).append('<option value="">(sélectionner)</option>');
            <?php   //echo json_encode($descripteur['select_etats']);
                    foreach($descripteur['select_etats'] as $e) {
            ?>
                    $(cur_select).append($('<option>').text('<?php echo $e->ved_etat; ?>'));
            <?php   } ?>
            });
        });
        _masseActionsPrepared = 1; // 1 = completed        
    };

    function prepareRowCustomControls(row) {
        //console.log('Populating custom controls in row '+row.index()+' [row id: '+row.id()+']');
        var select = $(row.node()).find('.dt_select');
        select.attr('rel_id', row.id());
        select.find('option').remove();
        select.append('<option value="x">(choisir)</option> <option value="A">Archiver suite refus</option> \
            <option value="S6">Projet abandonné</option> <option value="S7">Projet repoussé</option> <option value="S8">Passé par concurrence</option> \
            <option value="S9">En cours de signature</option> <option value="C1">Chaleur 1</option> <option value="C2">Chaleur 2</option> \
            <option value="C3">Chaleur 3</option> <option value="RM">Renvoyer devis mail</option> <option value="RI">Imprimer devis</option> \
            <option value="CR">Créer un rappel</option>');
        select.unbind('change');
        select.on('change', function() {
            var func_name = $(this).val();
            $(this).prop("selectedIndex", 0)
            //console.log( 'Perform quick action '+ func_name +' on: '+ $(this).attr('rel_id') );
            if ( func_name!='' && func_name!='x' ) {
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
        //console.log('prepareColumnsCustomControls');
        DT.columns().every(function(colIdx){
            //console.log('prepareColumnsCustomControls', this.header());
            //console.log(colIdx);
            //console.log($(DT.column( colIdx ).header()).attr('data'));
            //console.log('prepareColumnsCustomControls', colIdx, $(DT.column( colIdx ).header()).find('select'));
            //if ( $(DT.column( colIdx ).header()).attr('data')=='dvi_notes' ) {
            if ( $(this.header()).attr('data')=='dvi_notes' ) {
                //DT.column( colIdx )
                    this.nodes()
                    .to$()
                    .addClass( 'note_action' );
            } else if ( $(this.header()).attr('data')=='dvi_rappel' ) {
                    this.nodes()
                    .to$()
                    .addClass( 'rappel_action' );
            }
        });
    }

    function prepareRowCustomStyling(row, row_data) {
        apply_styling_etat(   row, row_data.get('dvi_etat'));
        apply_styling_couleur(row, row_data.get('dvi_couleur'))
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
            //    console.log(record); console.log(record.get('dvi_id'));
            var sauver = false;
            switch(action) {
                case "A":
                    record.set("dvi_etat",5);
                    record.set("ved_etat","Refusé");
                    sauver = true;
                    break;
                case "S6":
                    record.set("dvi_etat",6);
                    record.set("ved_etat","Projet abandonné");
                    sauver = true;
                    break;
                case "S7":
                    record.set("dvi_etat",7);
                    record.set("ved_etat","Projet repoussé");
                    sauver = true;
                    break;
                case "S8":
                    record.set("dvi_etat",8);
                    record.set("ved_etat","Passé par concurrence");
                    sauver = true;
                    break;
                case "S9":
                    record.set("dvi_etat",9);
                    record.set("ved_etat","En cours de signature");
                    sauver = true;
                    break;
                case "C1":
                    record.set("dvi_chaleur",1);
                    sauver = true;
                    break;
                case "C2":
                    record.set("dvi_chaleur",2);
                    sauver = true;
                    break;
                case "C3":
                    record.set("dvi_chaleur",3);
                    sauver = true;
                    break;
                case "SB":
                    record.set("dvi_couleur",0);
                    apply_styling_couleur(row, 0);
                    sauver = true;
                    break;
                case "SR":
                    record.set("dvi_couleur",1);
                    apply_styling_couleur(row, 1);
                    sauver = true;
                    break;
                case "RM":
                case "RI":
                    renvoi(action, row, record);
                    break;
                case "CR":
                    afficher_rappel(row,record);
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
                        message: 'Le devis a été modifié',
                        data: {
                            event: {
                                controleur: 'devis',
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
     *  @param function callback: The function to call after each sync is performed (regardless of success)
     *  @returns true|false; on true prior to return the table html is updated to reflect changes
     */
    function sync(row, record, callback) {
        //console.log('Sync record of row '+row.id()+':');
        //console.log(record);

        if (!callback) {
            callback = function(data){};
        }

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
        //console.log(record_as_array);
        $.ajax({
                url: '<?php echo site_url("devis/encours")?>/update',
                type: 'get',
                dataType: 'json',
                data: record_as_array
            }
        )
        .done(
            function(data) {
                // Get fresh copy of data
                $.ajax({
                        url: '<?php echo site_url("devis/encours")?>/get',
                        type: 'get',
                        dataType: 'json',
                        data: record_as_array
                    }
                )
                .done(
                    function(data) {     //console.log('SUCCESS getting updated record '); console.log(data[0]);
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
                        synced_cb.parent().find('img.loading_anim').remove();
                        synced_cb.show();
                        synced_cb.removeAttr("disabled");
                        notificationWidget.show("Enregistrement "+ record.get('dvi_id') +" mis à jour.","success");
                    },
                    callback
                )
                .fail(
                    function(data) {
                        //console.log('FAILed to get updated record '+(record_as_array.dvi_id)); console.log(data);
                        notificationWidget.show("Failed to reload updated record ("+ record.get('dvi_id') +"); UI is out of sync with database. Need to reload manually.","error");
                    },
                    callback
                )
            }
        )
        .fail(
            function(data) {
                //console.log(data.responseText);
                notificationWidget.show("Record "+ record.get('dvi_id') +" failed to update!","error");
            },
            callback
        )
        .always(
            function(){
            }
        );

    }
</script>
<!-- U I   F U N C T I O N S -->
<script type="text/javascript">

    // sélection / déselection
    $("#check-all").click(function(e) {
        var checkbox = $(this);
        var valeur = checkbox.is(":checked");
        DT.rows().every( function() {
            var cell = DT.cell(this.index(), 1);
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
        //console.log(id)
        $('#popup_1_dvi_id').html(record.get('dvi_id'));
        $('#popup_1_notes').val(record.get('dvi_notes'));
        $('#popup_1').modal('show');
    });

    // fonction de renvoi
    var renvoi = function(type, row, record) {
        var id = record.get('dvi_id');
        $.ajax({
            method:"POST",
            url:"<?php echo site_url("devis/renvoi")?>",
            data: {"type":type,"devis":id},
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

                    var broadcast = actionMenuBar.broadcastOnSuccess('localStorage');
                    var event = {
                        success: true,
                        notif: 'success',
                        message: 'Le devis a été renvoyé',
                        data: {
                            event: {
                                controleur: 'devis',
                                type: 'recordchange',
                                id: id,
                                timeStamp: Date.now()
                            }
                        }
                    };
                    broadcast(event);

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
        $('#popup_dvi_id').html(record.get('dvi_id'));
        _rappel_row = row;
        $('#popup_societe').html(record.get('scv_nom'));
        var tTTC = ((parseFloat(record.get('total_TTC'))+0.0).format(2, 3, '.', ','))
        $('#popup_ttc').html(tTTC);
        $('#popup_numero').html(record.get('dvi_reference'));
        var datedvi = (new Date( record.get('dvi_date') ) ).ddmmyyyy();
        $('#popup_datedvi').html(datedvi);
        $('#popup_info').val('D|'+record.get('dvi_id')+'|'+record.get('ctc_nom')+' - '+record.get('dvi_reference')+' - '+datedvi+' - '+record.get('total_HT')+' €');
        if (record.get('dvi_id_rappel') > 0) {
            var date_rappel = new Date( record.get('dvi_rappel') );
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
            var etat = parseInt(data.get('dvi_etat'), 10);

            // Fonction de status de bouton
            return function(button) {
                switch (button.id) {
                    case 'devis_envoyer_email':
                    case 'devis_marquer_transmis':
                    case 'devis_pdf':
                        return (etat >= 1);

                    case 'devis_supprimer':
                        return (etat < 3);

                    case 'devis_marquer_refus':
                    case 'devis_passer_commande':
                        return (etat == 1 || etat == 2 || etat == 3 || etat == 7 || etat == 9);
                }
                // Tous les autres boutons sont activés par défaut
                return true;
            }
        };

        // Redéfinition de la fonction qui génère les paramètres des boutons dans la barre action
        // C'est nécessaire pour les nouveaux devis pour sélectionner le même contact par défaut
        actionMenuBar.datatable.buttonParams = function(id) {
            var data = actionMenuBar.datatable.data(id);
            var contact_id = data.get('dvi_client');

            // Fonction de paramètres de bouton
            return function(button) {
                switch (button.id) {
                    case 'devis_nouveau':
                        return contact_id;
                }
                // Tous les autres boutons prennent simplement l'id dans l'URL
                return id;
            }
        };

        // création de rappel
        $("#popup_creer_rappel").on('click', function(){
            var row = _rappel_row;
            var id = $("#popup_dvi_id").text();
            var heure = $("#popup_heure").val() + '0000';
            heure = heure.substring(0, 2) + ':' + heure.substring(2,4);
            var rappel = $("#popup_date").val() + ' ' + heure;
            var info = $("#popup_info").val();
            var comment = $("#popup_comment").val();
            $.ajax({
                method:"POST",
                url:"<?php echo site_url("devis/rappel/0")?>",
                data: {"id":id,"rappel":rappel,"info":info,"comment":comment},
                success: function(reponse) {
                    //console.log(reponse);
                    if (reponse != false) {
                        // mise à jour du champ rappel
                        var record = row_data_map.get(id); //grid.dataSource.get(id);
                        var rappel_iso_8601 = rappel.substr(6,4)+'-'+rappel.substr(3,2)+'-'+rappel.substr(0,2)+rappel.substr(10);
                        //console.log(rappel_iso_8601);
                        record.set("dvi_rappel",rappel_iso_8601);
                        record.set("dvi_id_rappel",reponse);
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
                                        controleur: 'devis',
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
            var id = $("#popup_dvi_id").text();
            var record = row_data_map.get(id); //grid.dataSource.get(id);
            //console.log(record);
            var heure = $("#popup_heure").val() + '0000';
            heure = heure.substring(0, 2) + ':' + heure.substring(2,4);
            var rappel = $("#popup_date").val() + ' ' + heure;
            var info = $("#popup_info").val();
            var comment = $("#popup_comment").val();
            $.ajax({
                method:"POST",
                url:"<?php echo site_url("devis/rappel/2")?>",
                data: {"id":record.get('dvi_id_rappel'),"rappel":rappel,"info":info,"comment":comment},
                success: function(reponse) {
                    if (reponse != false) {
                        //console.log(reponse);
                        // mise à jour du champ rappel
                        var rappel_iso_8601 = rappel.substr(6,4)+'-'+rappel.substr(3,2)+'-'+rappel.substr(0,2)+rappel.substr(10);
                        record.set("dvi_rappel",rappel_iso_8601);
                        record.set("dvi_id_rappel",reponse);
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
                                        controleur: 'devis',
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
            var id = $("#popup_dvi_id").text();
            var record = row_data_map.get(id); //grid.dataSource.get(id);
            $.ajax({
                method:"POST",
                url:"<?php echo site_url("devis/rappel/1")?>",
                data: {"id":record.get('dvi_id_rappel')},
                success: function(reponse) {
                    if (reponse != false) {
                        $("#popup_heure").val('');

                        // mise à jour du champ rappel
                        record.set("dvi_couleur",0);
                        record.set("dvi_rappel",'');
                        record.set("dvi_id_rappel",0);
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
                                        controleur: 'devis',
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

        // enregistrement des remarques
        $("#popup_1_sauver").on('click', function(e) {
            var id = $("#popup_1_dvi_id").text();
            var record = row_data_map.get(id);
            record.set("dvi_notes",$("#popup_1_notes").val());
            sync(_note_row, record);
        });

        // mise en évidence d'un devis faisant l'objet d'un rappel
        function mise_evidence_devis(id) {
            //var grid = $("#grid").data("kendoGrid");
            //var record = grid.dataSource.get(id);
            var record = row_data_map.get(id); //grid.dataSource.get(id);
            var row = null;
            DT.rows().every(function() {
                if ( id == this.id() )
                    row = this;
                //console.log( 'Update Row: '+row.id()+' Column: '+this.index());
                //var cell = DT.cell(this.index(), 1);
                //console.log(cell.node());
                //$(cell.node()).find('input:checkbox').prop("checked", valeur);
            });
            if ( row == null ) {
                notificationWidget.show("Enregistrement "+id+" non trouvé dans la table","error");
            }
            else {
                record.set("dvi_couleur",3);
                sync(row, record);
                var sort_id = -1;
                DT.columns().every(function(colIdx){
                    //console.log(colIdx);
                    //console.log($(DT.column( colIdx ).header()).attr('data'));
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
                //grid.dataSource.sort({ field: "dvi_couleur", dir: "desc" });
            }
        };
    });
</script>