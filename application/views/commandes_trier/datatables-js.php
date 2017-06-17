<script type="text/javascript">
    dt_timer = (new Date()).getTime();
    DT = null;                 // This is THE datatables object that all should use.
                                   // Prior to its initialisation it might by empty (null)
                                   // once data from server have been loaded, it is a reference to the
                                   // DataTable object
    function DTinitialised() {     // Returns true if DataTable object has been initialised, false otherwise.
        
        if (typeof DT == undefined) return false;
        if ( DT == null ) return false;
        return true;
    }

    Number.prototype.format = function(n, x, s, c) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
            num = this.toFixed(Math.max(0, ~~n));
        return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
    };
    Date.prototype.ddmmyyyy = function() {
        if( !( Object.prototype.toString.call(this) === '[object Date]' && isFinite(this)) ) return "";
        var yyyy = this.getFullYear();
        var mm = this.getMonth() < 9 ? "0" + (this.getMonth() + 1) : (this.getMonth() + 1); // getMonth() is zero-based
        var dd  = this.getDate() < 10 ? "0" + this.getDate() : this.getDate();
        return "".concat(dd).concat('/').concat(mm).concat('/').concat(yyyy);
    };
    Date.prototype.ddmmyyyyhhmm = function() {
        if( !( Object.prototype.toString.call(this) === '[object Date]' && isFinite(this)) ) return "";
        var yyyy = this.getFullYear();
        var mm = this.getMonth()  < 9 ? "0" + (this.getMonth() + 1) : (this.getMonth() + 1); // getMonth() is zero-based
        var dd = this.getDate()  < 10 ? "0" + this.getDate() : this.getDate();
        var hh = this.getHours() < 10 ? "0" + this.getHours() : this.getHours();
        var mmin = this.getMinutes() < 10 ? "0" + this.getMinutes() : this.getMinutes();
        return "".concat(dd).concat('/').concat(mm).concat('/').concat(yyyy).concat(' ').concat(hh).concat(':').concat(mmin);
    };
    Date.prototype.yyyymmdd_dashed = function() {
        if( !( Object.prototype.toString.call(this) === '[object Date]' && isFinite(this)) ) return "";
        var yyyy = this.getFullYear();
        var mm = this.getMonth() < 9 ? "0" + (this.getMonth() + 1) : (this.getMonth() + 1); // getMonth() is zero-based
        var dd  = this.getDate() < 10 ? "0" + this.getDate() : this.getDate();
        return "".concat(yyyy).concat('-').concat(mm).concat('-').concat(dd);
    };
    Date.prototype.yyyymmddhhmm_dashed = function() {
        if( !( Object.prototype.toString.call(this) === '[object Date]' && isFinite(this)) ) return "";
        var yyyy = this.getFullYear();
        var mm = this.getMonth()  < 9 ? "0" + (this.getMonth() + 1) : (this.getMonth() + 1); // getMonth() is zero-based
        var dd = this.getDate()  < 10 ? "0" + this.getDate() : this.getDate();
        var hh = this.getHours() < 10 ? "0" + this.getHours() : this.getHours();
        var mmin = this.getMinutes() < 10 ? "0" + this.getMinutes() : this.getMinutes();
        return "".concat(yyyy).concat('-').concat(mm).concat('-').concat(dd).concat(' ').concat(hh).concat(':').concat(mmin);
    };
    String.prototype.endsWith = function(pattern) {
        var d = this.length - pattern.length;
        return d >= 0 && this.lastIndexOf(pattern) === d;
    };
    
    function objectToMap(obj) {
        var map = new Map();
        for (var key in obj) {
            if (obj.hasOwnProperty(key)) {
                map.set(key, obj[key]);
            }
        }  
        return map;
    };
    
    var dt_persistent_state = {};  // This object will be persisted in
    // database in order to enable store/reload
    // of current table view.

    // Default datatables column order initialisation
    dt_persistent_state.dt_columns = [  // Columns that are added to the datatable and are thus sortable, searchable, etc.
<?php $sep = '';
      $CBSelect_exists = false; $CBSelect_column_id = -1;
      $RELANCES_exists = false;
      $FIXEDcolReorder = 0;
      $column_counter = -1;
    foreach($descripteur['champs'] as $c) {
        echo $sep;
        $sep = ",\n";
        $column_counter++;
        if ($c[0]=="RowID"): $FIXEDcolReorder++ ?>
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":true, "visible": false, "width":0 }
<?php   elseif($c[0]=="CBSelect"):   $CBSelect_exists = true; $FIXEDcolReorder++; $CBSelect_column_id=$column_counter; ?>    
        { "data": null, "cl_label":"<?php echo $c[2]?>", "hidden":false, "width":20, 
            render: function (data, type, row) {
                    if (type === "display") {
                        return "<input type='checkbox' class='dt_checkbox' style='margin-left:10px; margin-right:10px;' />";
                    }
                    return data;
                }
        }
<?php   elseif($c[2]=="RELANCES"):  $FIXEDcolReorder++; ?>    
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":false, "width":20, 
            render: function (data, type) {
                    if (type === "display") {
                        return "<select class='dt_select' style='margin-left:10px; margin-right:10px;'></select>";
                    }
                    return data;
                }
        }
<?php   elseif($c[1]=="currency"):  ?>    
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":false, 
            render: function (data, type) {
                    if (type === "display") {
                        if ( $.isNumeric(data) )
                            return '<div style="text-align: right; padding-right:5px;">&euro;'+((parseFloat(data)+0.0).format(2, 3, '.', ','))+'</div>';
                        else    
                            return '<div style="text-align: right; padding-right:5px;">&euro;-,--</div>';
                    }
                    return data;
                } }
<?php   elseif($c[1]=="date"):  ?>    
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":false, 
            render: function (data, type) {
                    if (type === "display") {
                            if ( data == '') return '';
                            var d = new Date( data );
                            return d.ddmmyyyy();
                    }
                    return data;
                } }
<?php   elseif($c[1]=="datetime"):  ?>    
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":false, 
            render: function (data, type) {
                    if (type === "display") {
                            if ( data == '') return '';
                            if ( data == 'Invalid Date') return '';
                            var d = new Date( data );
                            return d.ddmmyyyyhhmm();
                    }
                    return data;
                } }
<?php   elseif($c[1]=="href"):  ?>    
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":false, 
            render: function (data, type) {
                    if (type === "display") {
                            if ( data == '') return '';
                            return "<a href='<?php echo site_url($c[3]) ?>' <?php if (count($c)>=5): ?>ref='<?php echo $c[4]?>'<?php endif;?> class='dt_href' style='text-decoration: none;'>"+data+"</a>";
                    }
                    return data;
                } }
<?php   elseif($c[1]=="hreflist"):  ?>    
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":false, 
            render: function (data, type) {
                    if (type === "display") {
                            if ( data == '') return '';
                            if ( data == null) return '';
                            var links = '';
                            var items = data.split(':');
                            var item_index = 0;
                            items.forEach(function(elem){
                                links += "<a href='<?php echo site_url('factures/detail/') ?>' <?php if (count($c)>=5): ?>ref='<?php echo $c[4]?>' ref_index='"+item_index+"'<?php endif;?> class='dt_hreflist' style='text-decoration: none;'>"+elem+"</a><br/>";
                                item_index++;
                            });
                            return links;
                    }
                    return data;
                } }
<?php   elseif($c[1]=="hreffile"):  ?>    
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":false, 
            render: function (data, type) {
                    if (type === "display") {
                            if ( data == '') return '';
                            return "<a href='<?php echo base_url() ?>"+data+"' class='dt_hreffile' target='_blank' style='text-decoration: none;'>"+(/[^/]*$/.exec(data)[0])+"</a>";
                    }
                    return data;
                } }
<?php   elseif(count($c)>3 && $c[3]=="invisible"):  ?>    
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":true, "visible":false}        
<?php   else:   ?>    
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":false, "width":90 }
<?php   endif;  }?>
    ];
    // Default datatables column sorting initialisation
<?php   if (!empty($descripteur['default_order'])) :    ?>
    dt_persistent_state.dt_sorting = [  // Default ordering set by Controller
        { "column": "<?php echo $descripteur['default_order'][0]?>", "dir":"<?php echo $descripteur['default_order'][0]?>" }
    ];
<?php   else: ?>
    dt_persistent_state.dt_sorting = [  // Default ordering set by Controller
        { "column": "", "dir":"" }
    ];
<?php   endif; ?>
    dt_persistent_state.invisible = [ ]; // Columns that are added to the datatable
<?php   $i =0;
        foreach($descripteur['champs'] as $c) { ?>
//            dt_persistent_state.column_order["<?php echo $c[0]; ?>"] = <?php echo $i++ ?>;
<?php   if(count($c)>3 && $c[3]=="invisible"): ?>
            dt_persistent_state.invisible["<?php echo $c[0]; ?>"]="<?php echo $c[1]; ?>";
<?php   endif;
        }?>

    
<?php if ($CBSelect_exists): ?>    
    $('#datatable_div').prepend('<div class="masse_actions"><label for="action">Actions de masse</label> \
                                    <select class="masse_actions_select"></select> \
                                    <button type="button" class="btn btn-default btn-xs" id="btn_action_all">Ok</button> \
                                    &nbsp;&nbsp;&nbsp;&nbsp; \
                                </div>');
    var CBSelect_column_id = <?php echo $CBSelect_column_id; ?>;
<?php   endif; ?>

    var row_data_map = new Map();

/*    function dt_init_resizable() {
        $('#datatable').colResizable({
            liveDrag:true,
            resizeMode:'overflow',
            minWidth: 25, 
            // Callback function:
            // After table column resize, get the widths of the table columns
            onResize: function(currentTarget){ 
                //console.log( $('#datatable.JColResizer').width());
                $('#datatable_div').width($('#datatable.JColResizer').width());
                $('div.dataTables_scrollHeadInner').find('table').width($('#datatable.JColResizer').width());
                $('div.dataTables_scrollHeadInner').width($('#datatable.JColResizer').width());
                dt_persist_state();
                dt_reapply_column_widths(); // to sync headers
                //$('.filter_combo_div').css('border', '1px solid red');
                $('input.resizable').each(function (index, elem){
                    var el = $(elem);
                    el.width( el.parent().width() -30 );
                });
                
            }
        });
    }*/
    function dt_init_resizable() {
        $('#datatable').colResizable({
            liveDrag:true,
            resizeMode:'overflow',
            // Callback function:
            // After table column resize, get the widths of the table columns
            onResize: onResizeDoSync
        });
        reWidthInputFields();        
    }
    function onResizeDoSync(currentTarget) {
        //console.log('datatable_div | datatable.JColResizer', $('#datatable_div').width(), $('#datatable.JColResizer').width()+getScrollBarWidth());
        $('#datatable_div').width($('#datatable.JColResizer').width());
        $('div.dataTables_scrollHeadInner').find('table').css('margin-right', getScrollBarWidth());
        /*$('div.dataTables_scrollHeadInner').find('table').css('float', 'left');*/
        $('div.dataTables_scrollHeadInner').find('table').width($('#datatable.JColResizer').width());
        dt_persist_state();
        dt_reapply_column_widths(); // to sync headers
        reWidthInputFields();  
        dt_persist_state();
        //console.log('div.dataTables_scrollBody last div height: ', $('div.dataTables_scrollBody').find('div').last().height());
        $('.JCLRgrip').height($('div.dataTables_scrollBody').find('div').last().height());
        //console.log('.JCLRgrip height: ', $('.JCLRgrip').height());
    }
    
    function reWidthInputFields() {
        $('.resizable').each(function (index, elem){
            var el = $(elem);
            el.width( el.parent().width() -40 );
        });
    }
    (function($) {
        $.fn.hasScrollBar = function() {
            if ( typeof this != undefined && typeof this != 'undefined') {
                //console.log('fn.hasScrollBar', this.scrollHeight, this.clientHeight);
                if ( typeof this.get(0) != undefined && typeof this.get(0) != 'undefined') {
                    //console.log('fn.hasScrollBar', this.scrollHeight, this.clientHeight, this.get(0).scrollHeight, this.get(0).clientHeight);
                    //console.log(this.get(0).scrollHeight > this.get(0).clientHeight);
                    return this.get(0).scrollHeight > this.get(0).clientHeight;
                }
            }
            return false;
        }
    })(jQuery);
    BROWSER_ScrollBarWidth = -1;
    function getScrollBarWidth () {
        if ( BROWSER_ScrollBarWidth>=0 )
            return BROWSER_ScrollBarWidth;
        var inner = document.createElement('p');
        inner.style.width = "100%";
        inner.style.height = "200px";
        
        var outer = document.createElement('div');
        outer.style.position = "absolute";
        outer.style.top = "0px";
        outer.style.left = "0px";
        outer.style.visibility = "hidden";
        outer.style.width = "200px";
        outer.style.height = "150px";
        outer.style.overflow = "hidden";
        outer.appendChild (inner);
        
        document.body.appendChild (outer);
        var w1 = inner.offsetWidth;
        outer.style.overflow = 'scroll';
        var w2 = inner.offsetWidth;
        if (w1 == w2) w2 = outer.clientWidth;
        
        document.body.removeChild (outer);
        
        BROWSER_ScrollBarWidth = (w1 - w2);
        //console.log('BROWSER_ScrollBarWidth', BROWSER_ScrollBarWidth);
        return BROWSER_ScrollBarWidth;
    };    

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
            _cl_label = cl_head.find('div').find('span').text();
            _cl_width = parseInt(map_col_widths[cl_head.attr('data')]);
            _cl_hidden = !column.visible();
            if (_cl_data=="RowID")
                dt_new_state.push({"data": _cl_data, "cl_label":_cl_label, "width":_cl_width, "hidden":_cl_hidden, "visible":false});
            else 
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
        $('#datatable').DataTable().columns().every(function(cl){
            var dt_head = $('#datatable').DataTable().column(cl).header();
            if ( $.inArray($(dt_head).attr('data'), hidden_columns)>-1 )
                $('#datatable').DataTable().column(cl).visible( false );
            else
                $('#datatable').DataTable().column(cl).visible( true );
        });

        var do_reload = false;
        // Finally, check sorting..
        if ( st.dt_sorting[0].column != dt_persistent_state.dt_sorting[0].column ||
            st.dt_sorting[0].dir != dt_persistent_state.dt_sorting[0].dir)
            do_reload=true;

        dt_persistent_state = st;
        dt_reapply_column_widths();

        if (do_reload) {
            console.log('I reload!');
            $('#datatable').DataTable().ajax.reload();
        }

        // Reinitialise resizability.
        setTimeout( function () {
            dt_init_resizable();
            dt_persist_state();
        }, 200 );

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
        $('#datatable_loading').append('<span>&nbsp;&nbsp;|&nbsp;&nbsp;Loading scripts...</span>')
        _rowCustomControlsPrepared=false;
        _fnDrawCallback_called=0;
        invisible_filters = {};
        
        DT = $('#datatable').DataTable( {
            language: {
                url: '<?php echo base_url('assets/js/French.json')?>'
            },
            serverSide: true,
            ordering: false,
            searching: false,
            scrollY: 575,
            scroller: {
                loadingIndicator: true
            },
            columns: dt_persistent_state.dt_columns,
    <?php   if ($CBSelect_exists): ?>    
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            } ], 
            select: { style: 'os', selector: 'td:first-child' },
    <?php   endif; ?>    
            rowId: 'RowID',
            ajax: {
    <?php if (!isset($id)) {
                $id = '';
    }?>
                url: '<?= site_url($descripteur['datasource']."_json/$id")?>',
                type: "POST",
                data: function ( d ) {
                    dt_timer = (new Date()).getTime();
                    //console.log($('#filter_input_global').val(), typeof $('#filter_input_global').val()!="undefined", typeof $('#filter_input_global').val()!="undefined" && $('#filter_input_global').val().length>0);
                    if ( typeof $('#filter_input_global').val()!="undefined" && $('#filter_input_global').val().length>0 ) {
                        delete d['filters'];
                        d['filter_global'] = $('#filter_input_global').val();
                    } else {
                        delete d['filter_global'];
                        d['filters'] = {};
                        Object.assign(d, invisible_filters);
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
                    }
                    for ( var i=0; i<dt_persistent_state.dt_columns.length; i++)
                    {
                        if ( dt_persistent_state.dt_columns[i].data == dt_persistent_state.dt_sorting[0].column) {
                            d['order'] = [{"column":i, "dir":dt_persistent_state.dt_sorting[0].dir}];
                        }
                    }
                    console.log('ajax.reload() params: ',d);
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
                    if ( data.responseJSON )
                    $('#datatable_loading').append('<span>&nbsp;got '+(data.responseJSON.data.length)+' items</span><span>&nbsp;&nbsp;|&nbsp;&nbsp;Building table...</span>')
                    var right_now = new Date();
                    $('#err_msg').html("Ajax call successful. :-) Got records "+data.responseJSON.recordsOffset+" to "+(parseInt(data.responseJSON.recordsOffset)+parseInt(data.responseJSON.recordsLimit)-1)+ " @ "+right_now.today() + " " + right_now.timeNow());
                    $('.sorter').hide();
                    //$('.sorter').text('Trier'); 	replaced by ? &#9671;
                    $('.sorter').html('&#9671;');
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

                    //detect response json if have date du tri and vil_nom
                    if (data.responseJSON.hasOwnProperty('date_du_tri') && data.responseJSON.hasOwnProperty('vil_nom')) {
                        $('.cdf').append('<p style="text-align:center">Ville : '+data.responseJSON.vil_nom+'<br>Date du Tri : '+data.responseJSON.date_du_tri+'</p>');
                    }
                    
                    $('#datatable_loading').append('<span>&nbsp;done.</span>');
                }
            },
			dom: 'Bfrtip',
			buttons: [
				
				'copy',
				'excel',
				'csv',
				'pdf',
				{
                    extend: 'print',
                    footer: true
                }
			],			
            colReorder: {
                realtime: true,
                fixedColumnsLeft: <?php echo $FIXEDcolReorder; ?>,
                fnReorderCallback: function() {
                    dt_reapply_column_widths();
                    // Destroy and init again colResizable in order to
                    // recreate resize handles for new column order.
                    $("#datatable").colResizable({ disable:true });
                    setTimeout( function () {
                        dt_init_resizable();
                        dt_persist_state();
                    }, 200 );
                }
            },
            initComplete: function(settings) {
                dt_persist_state();
                $('#datatable_loading').append('<span>&nbsp;done.</span><span>&nbsp;&nbsp;|&nbsp;&nbsp;Fetching data...</span>')
                
            },
            fnCreatedRow: function( nRow, aData, iDataIndex ) {
                //console.log(nRow);
                //console.log(aData);
                //console.log(iDataIndex);
                var row = DT.row(iDataIndex);
                var row_data = objectToMap(aData);
                row_data_map.set( aData.RowID, row_data);
                if (typeof prepareRowCustomStyling == 'function') { 
                    prepareRowCustomStyling(row, row_data); 
                }
                
            },
            fnDrawCallback: function( settings ) {

                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=null;
     
                api.column(2, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class="group"><td colspan="8">'+group+'</td></tr>'
                        );
     
                        last = group;
                    }
                });

                dt_reapply_column_widths();
                /*
                $('.dataTables_scrollBody').css('overflow-x','hidden');
                $('.dataTables_scrollBody').css('overflow-y','overlay');
                $('.dataTables_scrollHeadInner').css('padding-left', '0px');
                */
                // Destroy and init again colResizable in order to
                // recreate resize handles for new column order.
                $("#datatable").colResizable({ disable:true });
                $('#datatable_loading').hide();
                $('#datatable_div').show();
                
                if ( _fnDrawCallback_called > 0 ) {
                    
                    dt_init_resizable();
                    var jcolresizer_width= $('#datatable.JColResizer').width();
                    $('#datatable_div').width(jcolresizer_width);
                    $('div.dataTables_scrollHeadInner').find('table').css('margin-right', getScrollBarWidth());
                    $('div.dataTables_scrollHeadInner').find('table').width(jcolresizer_width);
                    
                    dt_persist_state();
                    dt_reapply_column_widths(); // to sync headers
                    reWidthInputFields();  
                    dt_persist_state();
                    
                    setTimeout(function(){
                        if (typeof prepareMasseActions == 'function') { 
                            prepareMasseActions();
                        }
                        if (typeof prepareColumnsCustomControls == 'function') { 
                            prepareColumnsCustomControls(); 
                        }
                        $("#datatable").colResizable({ disable:true });
                        if (typeof prepareRowCustomControls == 'function') { 
                            var rows = DT.rows();
                            var rows_count = rows.count();
                    		$('.resizable').width(5);
                            rows.every(function ( rowIdx, tableLoop, rowLoop ) {
                                prepareRowCustomControls(this); 
                            });
                        }
                        dt_init_resizable();
                        $('div.dataTables_scrollHeadInner').find('table').css('margin-right', getScrollBarWidth());
                        $('div.dataTables_scrollHeadInner').find('table').width($('#datatable.JColResizer').width());
                        dt_persist_state();
                        dt_reapply_column_widths(); // to sync headers
                        dt_persist_state();
                        $('.JCLRgrip').height($('div.dataTables_scrollBody').find('div').last().height());
                        console.log('Delayed UI setup and re-sync finished in ', (((new Date()).getTime()-dt_timer)/1000).toFixed(2), 'sec');
                    }, 500);
                }
                _fnDrawCallback_called++;
                console.log('DataTable fnDrawCallback finished in ', (((new Date()).getTime()-dt_timer)/1000).toFixed(2), 'sec');
            }

        } );

        $(window).bind('resizeEnd', function() {
            console.log('resizeEnd');
            //Due to infinite scrolling plugin, table header is disconnected from
            // the table itself, so syncing is required.
            /*$('.dataTables_scrollHeadInner').css('width',$('#datatable').css('width'));
            $('.dataTables_scrollHeadInner table').css('width',$('#datatable').css('width'));
            dt_persist_state();
            dt_reapply_column_widths();*/
            dt_init_resizable();
            $('div.dataTables_scrollHeadInner').find('table').css('margin-right', getScrollBarWidth());
            /*$('div.dataTables_scrollHeadInner').find('table').css('float', 'left');*/
            $('div.dataTables_scrollHeadInner').find('table').width($('#datatable.JColResizer').width());
            dt_persist_state();
            dt_reapply_column_widths(); // to sync headers
            dt_persist_state();
            $('.JCLRgrip').height($('div.dataTables_scrollBody').find('div').last().height());
            console.log('resizeEnd finished');
            
        });
        $(window).resize(function() {
            $("#datatable").colResizable({ disable:true });
            if(this.resizeTO) clearTimeout(this.resizeTO);
            this.resizeTO = setTimeout(function() {
                $(this).trigger('resizeEnd');   //make sure resizing by user drag is finished
            }, 500);                            // before syncing table and headers.
        });

        $('#err_msg').text(':-)');
    } );

    $('#masse_actions_cb').on('click', function(){
        var cb_val = $(this).is(":checked");
        var table = DT;
        table.column( CBSelect_column_id ).nodes().each( function ( value, index ) {
            $(value).find('input.dt_checkbox').prop('checked', cb_val); 
        } );    
        
    })
    
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
                /* Datetimepicker
                $('#filter_select_'+column_id).attr('src', '<?php echo base_url('assets/images/filter_gte_20.png')?>');
                $('#filter_type_'+column_id).val('gte').attr('disabled',false);
                $('#filter_text_'+column_id).text('une plus grande étage ekyal').attr('disabled',false);
                */
                $('#filter_select_'+column_id).attr('src', '<?php echo base_url('assets/images/filter_btw_20.png')?>');
                $('#filter_type_'+column_id).val('btw').attr('disabled',false);
                $('#filter_text_'+column_id).text('entre').attr('disabled',false);
            } else if ( $(this).hasClass('select_mask') ) {
                $('#filter_select_'+column_id).attr('src', '<?php echo base_url('assets/images/filter_eq_20.png')?>');
                $('#filter_type_'+column_id).val('eq').attr('disabled',false);
                $('#filter_text_'+column_id).text('égal à').attr('disabled',false);
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
    $('.select_mask').on('click', function(event){
        update_select_mask_options($(this));
        toggle_select_mask_options_div($(this));
    });
    
    var typingTimer;                //timer identifier
    var doneTypingInterval = 250;  //time in ms (1/4 seconds)
    var filter_val_cache = '';
    $('.filter_input').keyup(function(e){
        clearTimeout(typingTimer);
        //console.log('Caching input val: ', filter_val_cache, $(this).val());
        if ( e.keyCode!=13 && $(this).val()!='' &&  $(this).val() == filter_val_cache) return false; //If filter value is unchanged, no need to perform filtering.
        
        //if(e.keyCode == 13) { //=Enter key
        //        doneTyping(); 
        //} 
        //else {
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        //}
        return true;
    });
    $('.filter_input').on('keydown', function () {
        filter_val_cache = $(this).val(); // Cache value before filter change.
        //console.log('Caching input val: ', filter_val_cache);
        clearTimeout(typingTimer);
    });
    $('.filter_input').on('paste', function(){
        clearTimeout(typingTimer);
        doneTyping();
    });
    function doneTyping () {
        invisible_filters = {}; 
        $('#datatable').DataTable().ajax.reload();
    }
    
    function quick_call_filters(filters) {
        // clear all filters
        $('#filter_input_global').val('');
        $('.filter_clear').each(function(index){
            var filter_key = $(this).attr('id').replace('filter_clear_', '');
            clear_filter(filter_key, false);
        });

        //visualise new filters
        for (var i=0; i< filters.length; i++) {
            var f = filters[i];
            $('#filter_input_'+f['field']).val(f['value']);
            $('#filter_type_'+f['field']).val(f['operator']).attr('disabled',false);
            $('#filter_text_'+f['field']).text(f['operator']).attr('disabled',false);
            $('#filter_select_'+f['field']).attr('src', '<?php echo base_url('assets/images/filter_')?>'+f['operator']+'_20.png');
        }
        
/*        var invisibles = [];
        for (var key in dt_persistent_state.invisible) {
            invisibles.push(key);
        }
        invisible_filters={};
*/
        invisible_filters['filters']={};
        for (var i=0; i< filters.length; i++) {
            var f = filters[i];
            //if ( invisibles.includes(f['field'])) {
                invisible_filters['filters'][f['field']] = {'type':f['operator'], 'input':f['value']};
            //}
        }
        DT.ajax.reload();
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
        var lt      = '<li value="lt"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_lt_20.png')?>"/>inférieur</li>';
        var lte     = '<li value="lte"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_lte_20.png')?>"/>inférieur ou égal</li>';
        var gt      = '<li value="gt"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_gt_20.png')?>"/>supérieur</li>';
        var gte     = '<li value="gte"><img style="display: inline-block; margin-right: 5px; width:18px; height:18px;" src="<?php echo base_url('assets/images/filter_gte_20.png')?>"/>supérieur ou égal</li>';
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
            case 'select':
                $('#filter_options').append(eq).append(noteq).append(isempty).append(isnotempty);
                break;
            case 'int':
            case 'decimal':
                $('#filter_options').append(eq).append(noteq).append(cont).append(notcont).append(st).append(notst).append(btw).append(isempty).append(isnotempty).append(lt).append(lte).append(gt).append(gte);
                break;
            case 'datetime':
            case 'date':
                $('#filter_options').append(btw).append(eq).append(noteq).append(isempty).append(isnotempty).append(lt).append(lte).append(gt).append(gte);
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
                console.log( column_id, column_datatype);
                $( '#filter_select_'+current_filter_div).attr('src', $(this).children('img').first().attr('src'));
                
                $('#filter_input_global').val('');
                if ( $(this).attr('value')=="_clear_" ) {
                    if ( column_datatype=='date' || column_datatype=='datetime' ) {
                        $('#filter_input_'+column_id).periodpicker('clear');
                    }
                    $('#filter_type_'+column_id).val('').attr('disabled','disabled');
                    $('#filter_text_'+column_id).text('').attr('disabled','disabled');
                    $('#filter_input_'+column_id).val('');
                    $('#datatable').DataTable().ajax.reload();
                    $('#filter_clear_'+column_id).hide();
                }
                else if ( $(this).attr('value')=="isempty" || $(this).attr('value')=="isnotempty" ){
                    if ( column_datatype=='date' || column_datatype=='datetime' ) {
                        $('#filter_input_'+column_id).periodpicker('clear');
                    }
                    $('#filter_type_'+column_id).val($(this).attr('value')).attr('disabled',false);
                    $('#filter_text_'+column_id).text($(this).text()).attr('disabled',false);
                    $('#filter_input_'+column_id).val('');
                    //$('#filter_clear_'+column_id).show();
                    $('#datatable').DataTable().ajax.reload();
                }
                else {
                    $('#filter_type_'+column_id).val($(this).attr('value')).attr('disabled',false);
                    $('#filter_text_'+column_id).text($(this).text()).attr('disabled',false);
                    if ( column_datatype!='date' && column_datatype!='datetime' ) {
                        $('#filter_input_'+column_id).focus();
                    }
                    else {
                        if( $('#filter_input_'+column_id).val() !='')
                            $('#datatable').DataTable().ajax.reload();
                    }
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
    
    function update_select_mask_options(select) {
        // remove all options
        $('#select_mask_options li').remove();
        
        select.find('option').each(function(){
            $('#select_mask_options').append('<li value="'+$(this).text()+'">'+$(this).text()+'</li>');
        });
    }    
    function toggle_select_mask_options_div(select) { 
            var pos = $(select).offset();
            $('#select_mask_options_div').css({
                "left": pos.left + 1,
                "top":  pos.top  + 1
            } );
            $("#select_mask_options li").click(function() {
                $('#select_mask_options_div').hide();
                $(select).prop('selectedIndex', $(this).index());
                setTimeout(function() {
                    $("#select_mask_options li").unbind('click');
                    doneTyping();
                }, 100);
            });
            $('#select_mask_options_div').show();
    }    
    
    /*$('.datetimepicker_mask').datetimepicker({
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
    
    */
    $('.datetimepicker_mask__').periodpicker({
        cells: [1, 1],
        withoutBottomPanel: true,
        yearsLine: false,
        title: false,
        closeButton: false,
        fullsizeButton: false,
        format:'d/m/Y',
    	formatDate:'d/m/Y'
    	
    });
    $('.datepicker_mask, .datetimepicker_mask').periodpicker({
        draggable: false,
        resizeButton: false,
        cells: [1, 3],
        //withoutBottomPanel: true,
        yearsLine: true,
        title: false,
        closeButton: true,
        clearButton: true,
        fullsizeButton: false,
        format:'DD/MM/YYYY',
    	formatDate:'DD/MM/YYYY',
    	formatDecoreDate: 'MM/YYYY',
    	formatDecoreDateWithYear: 'DD/MM/YYYY'
    });
    $('.datetimepicker_mask_DISABLED').periodpicker({
        draggable: false,
        resizeButton: false,
        //cells: [1, 3],
        //withoutBottomPanel: true,
        yearsLine: true,
        //timepicker: true, // use timepicker
    	timepickerOptions: {
        		hours: true,
        		minutes: true,
        		seconds: true,
        		ampm: true,
        		defaultTime: '00:00:00',
        		defaultEndTime: '23:59:59',
    	    }, 
    	//defaultEndTime: '23:59:59',
		defaultTime: '00:00:00',
		defaultEndTime: '23:59:59',
        title: false,
        closeButton: true,
        clearButton: true,
        fullsizeButton: false,
        format:'DD/MM/YYYY HH:mm',
    	formatDate:'DD/MM/YYYY',
    	formatDateTime:'DD/MM/YYYY HH:mm',
    	formatDecoreDateTime: 'DD/MM/YYYY HH:mm', //'D/M/Y HH:mm',
    	formatDecoreDateTimeWithYear: 'DD/MM/YYYY HH:mm',
    	//formatDecoreDate: 'D/M/Y HH:mm',
    	//formatDecoreDateWithYear: 'D/M/Y HH:mm'
    });    
    $('.datepicker_mask, .datetimepicker_mask').on('change', function () {
        var new_value = $(this).periodpicker('valueStringStrong');
    	//console.log(new_value);
    	var ref_input = $(this).closest('input.filter_input');
    	ref_input.val( new_value );
    	//console.log(ref_input);
    	ref_input.trigger('click');
    	doneTyping();
    });    

    //$('#save_list').css('cursor', 'not-allowed');
    //$('#liste_vues').css('cursor', 'not-allowed');
    //$('#load_list').css('cursor', 'not-allowed');

    // réglage de l'ordre et de la visibilité des colonnes
    $("#rule_list").click(function(e) {
        e.preventDefault();
        //console.log(dt_persistent_state.dt_columns);
        var colonnes = dt_persistent_state.dt_columns;
        var html = '';
        for (var i=0;i<colonnes.length;i++) {
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
        /*$("#liste_colonnes").kendoSortable({
         ignore: "input",
         cursor: "move",
         hint: function(element) {
         return $("<span></span>")
         .text(element.text());
         }
         });*/
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
        //var data = kendo.stringify(grid.getOptions());
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
    
    $('#manual_order_by').on('keyup', function(e){
        if(e.keyCode == 13)
        {
            console.log('Order by: '+ $(this).val());
            var orderCol = parseInt($(this).val());
            if ( $.isNumeric( orderCol )  )
                $('#datatable').DataTable( )
                    .order([orderCol, 'asc'])
                    .draw();
        }
    });

</script>
<style>
	.dt-buttons{
		display: none;
	}
</style>
