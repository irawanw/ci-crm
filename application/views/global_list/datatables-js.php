<script type="text/javascript">
    var dt_persistent_state = {};  // This object will be persisted in
    // database in order to enable store/reload
    // of current table view.
    // Default datatables column order initialisation
    dt_persistent_state.dt_columns = [  // Columns that are added to the datatable and are thus sortable, searchable, etc.
<?php $sep = '';
    foreach($descripteur['champs'] as $c) {
        echo $sep;
        $sep = ",\n";?>
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":false, "width":100 }
<?php }?>
    ];
    // Default datatables column sorting initialisation
    dt_persistent_state.dt_sorting = [  // Columns that are added to the datatable
        { "column": "<?php echo $descripteur['champs'][0][0]?>", "dir":"asc" }
    ];

    function dt_init_resizable() {
		$('.filter_combo_div input').width("60%");
		$('#datatable').colResizable({ disable: true });
        $('#datatable').colResizable({
            liveDrag: false,
            minWidth: 25,
            onResize: function(currentTarget){ 
				$('.filter_combo_div input').width("60%")
				dt_resizeheader();
            }
        });
    }

    Array.prototype.diff = function(a) {
        return this.filter(function(i) {return a.indexOf(i) < 0;});
    };
	
    function dt_persist_state() {
        var dt_new_state = [];
        var map_col_widths = [];
        var datatablesBodyCells = $('#datatable tr:first td');
        datatablesBodyCells.each(function(index){
            map_col_widths[$(this).attr('data')] = $(this).width();
        });
        $('#datatable').DataTable().columns().every(function(cl){
            var column = $('#datatable').DataTable().column(cl);
            var dt_head = column.header();
            var cl_head = $(dt_head);
            var _cl_data, _cl_label, _cl_width, _cl_hidden;
            _cl_data  = cl_head.attr('data');
            _cl_label = cl_head.find('span').text();
            _cl_width = parseInt(map_col_widths[cl_head.attr('data')]);
            _cl_hidden = !column.visible();
            dt_new_state.push({"data": _cl_data, "cl_label":_cl_label, "width":_cl_width, "hidden":_cl_hidden});
        });
        dt_persistent_state.dt_columns = dt_new_state;
        dt_persistent_state.datatable_div_width =  $('#datatable_div').width();
    }
	
    function dt_restore_state(st) {		
        if (typeof st == undefined) return;
        if (typeof st.dt_columns == undefined) return;
        $("#datatable").colResizable({ disable:true });        
        $('#datatable_div').width(st.datatable_div_width);

        // Reorder columns appropriately
        var current_column_order = [];
        $('#datatable').DataTable().columns().every(function(cl){
            var dt_head = $('#datatable').DataTable().column(cl).header();
            current_column_order[$(dt_head).attr('data')] = cl;
        });
        var new_column_order = [];
        for ( var i=0; i<st.dt_columns.length; i++)
        {
            var col = st.dt_columns[i];
            new_column_order.push(current_column_order[col.data]);
        }

        $('#datatable').DataTable().colReorder.order( new_column_order );

        // Show/Hide columns appropriately
        var hidden_columns = [];
        for ( var i=0; i<st.dt_columns.length; i++)
        {
            var col = st.dt_columns[i];
            if ( col.hidden == true )
                hidden_columns.push(col.data);
        }

        var columnVisible = [];
        var columnHidden = [];

        $('#datatable').DataTable().columns().every(function(cl){
            var dt_head = $('#datatable').DataTable().column(cl).header();
            
            if ( $.inArray($(dt_head).attr('data'), hidden_columns)>-1 ) {
                //$('#datatable').DataTable().column(cl).visible( false );
                columnHidden.push(cl);
            }else {
                columnVisible.push(cl);
                //$('#datatable').DataTable().column(cl).visible( true );
            }
        });

        $('#datatable').DataTable().columns(columnVisible).visible(true, false);
        $('#datatable').DataTable().columns(columnHidden).visible(false, false);
        $('#datatable').DataTable().columns.adjust().draw(false);

        var do_reload = false;
        // Finally, check sorting..
        if ( st.dt_sorting[0].column != dt_persistent_state.dt_sorting[0].column ||
            st.dt_sorting[0].dir != dt_persistent_state.dt_sorting[0].dir)
            do_reload=true;

        dt_persistent_state = st;
        dt_reapply_column_widths();
		setTimeout( function () {
			dt_init_resizable();
		}, 4000);
		/*		
        if (do_reload) {
            $('#datatable').DataTable().ajax.reload();
        }

        // Reinitialise resizability.
        setTimeout( function () {
            dt_init_resizable();
            dt_persist_state();
        }, 200 );
		*/
    }

    function dt_reapply_column_widths() {
        //console.log('dt_reapply_column_widths');
        if (typeof dt_persistent_state == undefined) return;
        if (typeof dt_persistent_state.dt_columns == undefined) return;
        var map_column_to_width = {};
        for ( var i=0; i<dt_persistent_state.dt_columns.length; i++)
        {
            var col = dt_persistent_state.dt_columns[i];
            map_column_to_width[col.data] = col.width;
        }
        
        //console.log('Reapplying widths:');
        //console.log(map_column_to_width);
        
        var datatablesBodyCells = $('#datatable tr:first td');
        datatablesBodyCells.each(function(index){
            //console.log(index+" - "+map_column_to_width[$(this).attr('data')] );
            $(this).width( map_column_to_width[$(this).attr('data')] );
            //var col = {"data": $(this).attr('data'), "width":$(this).width()};
            //dt_new_state.push(col);
        });
        
        var datatablesHeaderCells = $('#data_table_columns_header').first().find('td');
        datatablesHeaderCells.each(function(index){
            //var d= $(this).attr('data');
            $(this).width(map_column_to_width[$(this).attr('data')]);
        });
    }

    // For todays date;
    Date.prototype.today = function () { 
        return ((this.getDate() < 10)?"0":"") + this.getDate() +"/"+(((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"/"+ this.getFullYear();
    }
    
    // For the time now
    Date.prototype.timeNow = function () {
         return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes() +":"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
    }

    $(document).ready(function() {
        $('#err_msg').text(':-|');

        $('#datatable').DataTable( {
            language: {
                url: '<?php echo base_url('assets/js/French.json')?>'
            },
            serverSide: true,
            ordering: false,
            searching: false,
			"deferRender": true,
			"autoWidth": false,
			"bAutoWidth": false,
            scrollY: 400,
            scroller: {
                loadingIndicator: true
            },
            columns: dt_persistent_state.dt_columns,
            rowId: 'RowID',
			dom: 'Bfrtip',
			buttons: [
				
				// 'copy',
				// 'excel',
				// 'csv',
				// 'pdf',
				// 'print'
			],

            ajax: {
                url: '<?= site_url($descripteur['datasource']."_json/$id")?>',
                type: "POST",
                data: function ( d ) {
                    delete d['filters'];
                    d['filters'] = {};
                    delete d['filter_global'];
                    $('.filter_type').each(function(index){
                        if ( $(this).val() == '' ) return true;
                        //if ( d.hasOwnProperty( $(this).attr('id') ) ) return true;

                        var filter_key = $(this).attr('id').replace('filter_type_', '');
                        var input_value = $('#filter_input_'+filter_key).val();
                        //if ( input_value == '' && ($(this).val() != 'isempty' && $(this).val() != 'isnotempty') ) return true;
                        //d['filters'][filter_key] = {'type':$(this).val(), 'input':input_value};
                        if ( input_value == '' ) {
                            if ($(this).val() == 'isempty' || $(this).val() == 'isnotempty')
                                d['filters'][filter_key] = {'type':$(this).val(), 'input':''};
                            else
                                return true;
                        } else
                            d['filters'][filter_key] = {'type':$(this).val(), 'input':input_value};
                    });
                    if ( $('#filter_input_all').val()!='' )
                        d['filter_global'] = $('#filter_input_global').val();
                    for ( var i=0; i<dt_persistent_state.dt_columns.length; i++)
                    {
                        if ( dt_persistent_state.dt_columns[i].data == dt_persistent_state.dt_sorting[0].column) {
                            d['order'] = [{"column":i, "dir":dt_persistent_state.dt_sorting[0].dir}];
                        }
                    }
                },
                dataSrc: function ( json ) {
                    //console.log(json);
                    return json.data;
                },
                statusCode: {
                    500: function() {
                        $('#err_msg').html("HTTP500: Server exhausted :-( <br/>Try again in a bit, or refine your filters, e.g. looking for 'starts with' instead of 'contains'.");
                    },
                    404: function() {
                        $('#err_msg').html("HTTP404: I feel lost :-( <br/>Got back a strange 404 error. Logs should be enlighting.");
                    }
                },
                complete: function( data, textStatus, jqXHR ) {
                    //console.log(data);
                    var right_now = new Date();
                    $('#err_msg').html("Ajax call successful. :-) Got records "+data.responseJSON.recordsOffset+" to "+(parseInt(data.responseJSON.recordsOffset)+parseInt(data.responseJSON.recordsLimit)-1)+ " @ "+right_now.today() + " " + right_now.timeNow());
                    $('.sorter').hide();
                    //$('.sorter', 'td.movable').show();
                    $('.sorter').text('Trier');
                    $('.sorter').each(function(index, el) {
                        if ( index < dt_persistent_state.dt_columns.length )
                            $(el).show();
                    })
                    if ( data.responseJSON.ordercol && data.responseJSON.ordering )
                    {
                        if ( data.responseJSON.ordering == "asc")
                            $('#sorter_'+data.responseJSON.ordercol).html('&#9650;');
                        else if ( data.responseJSON.ordering == "desc")
                            $('#sorter_'+data.responseJSON.ordercol).html('&#9660;');
                    }
                }
            },
            colReorder: {
                realtime: true,
                fnReorderCallback: function() {					
					dt_init_resizable();
                }
            },
            initComplete: function(settings) {
				dt_initwidth();
				setTimeout(function(){
						autowidthHeader(groupheader);
					}, 500);
            },
            fnDrawCallback: function( settings ) {
            }

        } );

        $(window).bind('resizeEnd', function() {
			
        });
        $(window).resize(function() {
            if(this.resizeTO) clearTimeout(this.resizeTO);
            this.resizeTO = setTimeout(function() {
                $(this).trigger('resizeEnd');   //make sure resizing by user drag is finished
            }, 500);                            // before syncing table and headers.
        });

        $('#err_msg').text(':-)');
    } );

    $('.filter_select').on('click', function(event){
        toggle_filter_options_div( $('#'+event.currentTarget.id).attr('data'), $('#'+event.currentTarget.id).attr('datatype'));
        event.stopPropagation();
        return false;
    });
    $('.filter_clear').on('click', function(event){
        var filter_key = $(this).attr('id').replace('filter_clear_', '');
        clear_filter(filter_key, true);
        event.stopPropagation();
        return false;
    });
    $('.filter_clear_all').on('click', function(event){
        $('.filter_clear').each(function(index){
            var filter_key = $(this).attr('id').replace('filter_clear_', '');
            clear_filter(filter_key, false);
        });
        $('#datatable').DataTable().ajax.reload();
    });
    function clear_filter(filter_key, reload) {
        //console.log('clear_filter('+filter_key+', '+reload+')');
        $('#filter_type_'+filter_key).val('').attr('disabled','disabled');
        $('#filter_text_'+filter_key).text('').attr('disabled','disabled');
        $('#filter_input_'+filter_key).val('');
        $('#filter_input_'+filter_key).blur();
        if ( reload != false)
            $('#datatable').DataTable().ajax.reload();
        $('#filter_clear_'+filter_key).hide();
        $('#filter_select_'+filter_key).attr('src', '<?php echo base_url('assets/images/filter_20.png')?>');
    }
    $('#filter_input_global').on('change paste', function(){
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
        $('.filter_clear').each(function(index){
            var filter_key = $(this).attr('id').replace('filter_clear_', '');
            clear_filter(filter_key, false);
        });
    });
    $('#filter_global_clear').on('click', function(event){
        $('#filter_input_global').val('');
        $('#datatable').DataTable().ajax.reload();
        event.stopPropagation(); return false;
    });

    $('.filter_text' ).on('click', function(event){event.stopPropagation(); return false;});
    $('.filter_div'  ).on('click', function(event){event.stopPropagation(); return false;});
    $('.spacer25'    ).on('click', function(event){event.stopPropagation(); return false;});
    $('.filter_input').on('click', function(event){
        // check if filter is active, before focusing input
        // if filter type is not selected; select 'contains' as default
        var selected_filter_val = $(this).attr('id');
        var column_id = selected_filter_val.replace('filter_input_', '');
        var relative_filter_type = selected_filter_val.replace('filter_input', 'filter_type');
        $('#filter_input_global').val('');
        if ( $('#'+relative_filter_type).val()=='' ) {
            if ( $(this).hasClass('datetimepicker_mask') || $(this).hasClass('datepicker_mask') ) {
                $('#filter_select_'+column_id).attr('src', '<?php echo base_url('assets/images/filter_gte_20.png')?>');
                $('#filter_type_'+column_id).val('gte').attr('disabled',false);
                $('#filter_text_'+column_id).text('une plus grande étage ekyal').attr('disabled',false);
            } else {
                $('#filter_select_'+column_id).attr('src', '<?php echo base_url('assets/images/filter_cont_20.png')?>');
                $('#filter_type_'+column_id).val('cont').attr('disabled',false);
                $('#filter_text_'+column_id).text('contient').attr('disabled',false);
            }
            $('#filter_input_'+column_id).focus();
        }
        if ( $('#'+relative_filter_type).val()!='' && $('#'+relative_filter_type).val()!='isempty' && $('#'+relative_filter_type).val()!='isnotempty')
            $(this).focus();
        event.stopPropagation(); return false;
    });
    var typingTimer;                //timer identifier
    var doneTypingInterval = 500;  //time in ms (0.5 seconds)
    //$('.filter_input').on('change paste', function(){
    //    clearTimeout(typingTimer);
    //    typingTimer = setTimeout(doneTyping, doneTypingInterval);
    //});
    $('.filter_input').keyup(function(e){
        if(e.keyCode == 13) { doneTyping(); } //Enter key
        return true;
    });
    function doneTyping () {
        $('#datatable').DataTable().ajax.reload();
    }
    $('.sorter').on('click', function() {
        var sort_by = $(this).attr('id').replace('sorter_', '');
        if ( dt_persistent_state.dt_sorting[0].column == sort_by ) {
            dt_persistent_state.dt_sorting[0].dir = (dt_persistent_state.dt_sorting[0].dir=='asc')?'desc':'asc'; //flip direction
        }
        else {
            dt_persistent_state.dt_sorting[0].column = sort_by;
            dt_persistent_state.dt_sorting[0].dir = 'asc';
        }
        $('#datatable').DataTable().ajax.reload();
    });

    function update_filter_options(datatype) {
        var clear   = '<li value="_clear_" style="font-style: italic; "><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_20.png')?>"/>[effacer le filtre]</li>';
        var eq      = '<li value="eq"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_eq_20.png')?>"/>égal à</li>';
        var noteq   = '<li value="noteq"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_noteq_20.png')?>"/>différent de</li>';
        var cont    = '<li value="cont"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_cont_20.png')?>"/>contient</li>';
        var notcont = '<li value="notcont"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_notcont_20.png')?>"/>ne contient pas</li>';
        var st      = '<li value="st"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_st_20.png')?>"/>commence par</li>';
        var notst   = '<li value="notst"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_notst_20.png')?>"/>ne commence pas par</li>';
        var isempty = '<li value="isempty"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_isempty_20.png')?>"/>est vide</li>';
        var isnotempty = '<li value="isnotempty"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_isnotempty_20.png')?>"/>n\'est pas vide</li>';
        var lt      = '<li value="lt"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_lt_20.png')?>"/>moins</li>';
        var lte     = '<li value="lte"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_lte_20.png')?>"/>moins ekyal étage</li>';
        var gt      = '<li value="gt"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_gt_20.png')?>"/>plus grand</li>';
        var gte     = '<li value="gte"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_gte_20.png')?>"/>une plus grande étage ekyal</li>';
        var btw     =  '<li value="btw"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_btw_20.png')?>"/>entre</li>';
   
        // remove all options
        $('#filter_options li').remove();
        // add '_clear_' option
        $('#filter_options').append(clear);
        
        switch (datatype) {
            case 'text':
            case 'ref':
            case 'char':    
                $('#filter_options').append(eq).append(noteq).append(cont).append(notcont).append(st).append(notst).append(btw).append(isempty).append(isnotempty);
                break;
            case 'int':
            case 'decimal':
                $('#filter_options').append(eq).append(noteq).append(cont).append(notcont).append(st).append(notst).append(btw).append(isempty).append(isnotempty).append(lt).append(lte).append(gt).append(gte);
                break;
            case 'datetime':
            case 'date':
                $('#filter_options').append(eq).append(noteq).append(isempty).append(isnotempty).append(lt).append(lte).append(gt).append(gte);
                break;
        }
    }
    var current_filter_div="";
    function toggle_filter_options_div(column_id, column_datatype) {
        var filter_options_div = $("#filter_options_div");
        if (column_id != current_filter_div) {
            filter_options_div.hide();
            $("#filter_options li").unbind("click");
        }
        current_filter_div = column_id;
        if ( filter_options_div.css('display')=="none" )
        {
            //console.log('toggle_filter_options_div '+column_id+' - '+column_datatype);
            update_filter_options( column_datatype );
            var pos = $('#filter_select_'+column_id).offset();
            filter_options_div.css({
                "left": pos.left + 20,
                "top":  pos.top  + 20
            } );
            filter_options_div.show();

            $("#filter_options li").click(function() {
                //console.log( 'Update image of filter_select_'+current_filter_div );
                //console.log( $(this).children('img') );
                $( '#filter_select_'+current_filter_div).attr('src', $(this).children('img').first().attr('src'));
                
                $('#filter_input_global').val('');
                if ( $(this).attr('value')=="_clear_" ) {
                    $('#filter_type_'+column_id).val('').attr('disabled','disabled');
                    $('#filter_text_'+column_id).text('').attr('disabled','disabled');
                    $('#filter_input_'+column_id).val('');
                    $('#datatable').DataTable().ajax.reload();
                    $('#filter_clear_'+column_id).hide();
                }
                else if ( $(this).attr('value')=="isempty" || $(this).attr('value')=="isnotempty" ){
                    $('#filter_type_'+column_id).val($(this).attr('value')).attr('disabled',false);
                    $('#filter_text_'+column_id).text($(this).text()).attr('disabled',false);
                    $('#filter_input_'+column_id).val('');
                    //$('#filter_clear_'+column_id).show();
                    $('#datatable').DataTable().ajax.reload();
                }
                else {
                    $('#filter_type_'+column_id).val($(this).attr('value')).attr('disabled',false);
                    $('#filter_text_'+column_id).text($(this).text()).attr('disabled',false);
                    $('#filter_input_'+column_id).focus();
                    //$('#filter_clear_'+column_id).show();
                }
                filter_options_div.hide();
                $("#filter_options li").unbind("click");
            });
        }
        else
        {
            filter_options_div.hide();
            $("#filter_options li").unbind("click");
        }
    }
    
    $('.datetimepicker_mask').datetimepicker({
        format:'d/m/Y H:i',
        formatTime:'H:i',
    	formatDate:'d/m/Y',
    	defaultDate:'+01/01/1970',
    	defaultTime:'10:00'
    });
    $('.datepicker_mask').datetimepicker({
        format:'d/m/Y',
    	formatDate:'d/m/Y',
    	defaultDate:'+01/01/1970',
    	timepicker:false
    });

    // réglage de l'ordre et de la visibilité des colonnes
    $("#rule_list").click(function(e) {
        e.preventDefault();
        //console.log(dt_persistent_state.dt_columns);
        var colonnes = dt_persistent_state.dt_columns;
        var html = '';
        for (var i=0;i<colonnes.length;i++) {
            //console.log(colonnes[i]);
            var checked = ' checked';
            if (colonnes[i].hidden == true) {
                checked = '';
            }
            if (colonnes[i].visible == false)
                html += '<li style="display: none;"><div class="checkbox"><label><input type="checkbox" class="liste_colonnes_cb" value="'+i+'" data="'+colonnes[i].data+'" '+checked+'>'+colonnes[i].cl_label+'</label></div></li>';
            else 
                html += '<li><div class="checkbox"><label><input type="checkbox" class="liste_colonnes_cb" value="'+i+'" data="'+colonnes[i].data+'" '+checked+'>'+colonnes[i].cl_label+'</label></div></li>';
        }
        $("#liste_colonnes").html(html);
        $("#liste_colonnes").sortable();
        $("#liste_colonnes").disableSelection();
        $('#popup_reglage').modal('show');
    });
    $("#toutes_colonnes").click(function(e) {
        var etat = $(this).prop('checked');
        $(".liste_colonnes_cb").each(function(index){
            $(this).prop('checked', etat);
        });
    });
    $("#popup_reglage_sauver").click(function(e) {
        // Make table columns temporarily non-resizable
        $("#datatable").colResizable({ disable:true });

        // Note which columns should be visible
        var list_updated_visible_columns = [];
        $(".liste_colonnes_cb").each(function(index){
            var column_key = $(this).attr('data');
            var make_visible = $(this).prop('checked');
            if ( make_visible )
                list_updated_visible_columns.push(column_key);
        });

        // Reorder columns
        var new_column_order = [];
        $(".liste_colonnes_cb").each(function(index){
            new_column_order[index] = parseInt($(this).prop('value'));
        });
        $('#datatable').DataTable().colReorder.order( new_column_order );

        // Hide/Show columns goinng through DataTable().columns()
        $('#datatable').DataTable().columns().every(function(cl){
            var dt_head = $('#datatable').DataTable().column(cl).header();
            if ( $.inArray($(dt_head).attr('data'), list_updated_visible_columns)>-1 )
                $('#datatable').DataTable().column(cl).visible( true );
            else
                $('#datatable').DataTable().column(cl).visible( false );
        });

        // Reinitialise resizability.
        setTimeout( function () {
            dt_init_resizable();
            dt_persist_state();
        }, 200 );
    });

    // sauvegarde de la vue courante
    $("#save_list").click(function(e) {
        e.preventDefault();
        var vue = prompt("Nom de la vue", "ma vue");
        if ( vue == null ) return false;
        var controleur = '<?php echo $controleur?>';

		dt_persist_state();
        var data = JSON.stringify( dt_persistent_state );

        $.post( "<?php echo site_url("vues/nouvelle")?>", {vue:vue, data:data, ctrl:controleur}, function( data ) {
            if (data) {
                $('#liste_vues').append('<li><a class="vue" href="#'+data+'">'+vue+'</a></li>');
                notificationWidget.show("La vue a été sauvegardée","success");
                $('.vue').unbind('click');
                $('.vue').on('click', load_view_clicked);
            }
            else {
                notificationWidget.show("Un problème technique a empéché la sauvegarde de la vue","error");
            }
        });
    });
    // liste des vues enregistrées
    $('.vue').on('click', load_view_clicked);

    function load_view_clicked(e) {
        e.preventDefault();
        var hash = e.target.hash;
        var id = hash.substr(1);
        $.post( "<?php echo site_url("vues/reglages")?>", {id_vue:id}, function( data ) {
            if (data) {
                dt_restore_state(JSON.parse(data));
            }
        });
    }
	
	function dt_initwidth(width){
		$('#datatable thead td').width('100px');
		//$('#datatable thead td div').width('100px');
		$('#datatable td').css('word-break','break-all');
		$('#data_table_columns_header td').width('100px');
		//$('#data_table_columns_header td div').width('100px');
		$('#data_table_columns_header td').css('word-break','break-all');
		$('#data_table_columns_header td div').css('word-break','break-all');
		
		if(width == undefined)
			$('#datatable_div').width($('#datatable').width());
		else
			$('#datatable_div').width(width+'px');
			
		$('.dataTables_scrollBody').css('overflow-x', 'hidden');
		$('.dataTables_scrollBody').css('overflow-y', 'scroll');		
		dt_init_resizable();
	}

	function dt_autowidth(){
		return ($('.dataTables_scrollHeadInner thead td').length*101);
	}
	
	function dt_resizeheader(){
		$('.dataTables_scrollHeadInner thead td').each(function(){
			 index = ($(this).index()+1);
			 width = $('#datatable tbody tr:nth-child(1) td:nth-child('+index+')').width();
			 $(this).width(width);
		});
		autowidthHeader(groupheader);
	}
</script>