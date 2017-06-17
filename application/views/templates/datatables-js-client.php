<script type="text/javascript">
    dt_timer = (new Date()).getTime();

    console.log('Loading..');  
    DT = null;                 // This is THE datatables object that all should use.
                                   // Prior to its initialisation it might by empty (null)
                                   // once data from server have been loaded, it is a reference to the
                                   // DataTable object
    function DTinitialised() {     // Returns true if DataTable object has been initialised, false otherwise.
        
        if (typeof DT == undefined) return false;
        if ( DT == null ) return false;
        return true;
    }
    (function($) {
        $.fn.hasScrollBar = function() {
            if ( typeof this != undefined && typeof this != 'undefined') {
                if ( typeof this.get(0) != undefined && typeof this.get(0) != 'undefined') {
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
        console.log('BROWSER_ScrollBarWidth', BROWSER_ScrollBarWidth);
        return BROWSER_ScrollBarWidth;
    };
        
    /* Custom filtering function which will search data in column four between two values */
    filters_map = {};
    $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            if ( filters_map.hasOwnProperty('filter_global') && filters_map['filter_global']!="" ) {
                for ( var k in data ) {
                    if ( data.hasOwnProperty(k) ) {
                        var v = (data[k]).toString();
                        var v_length = v.length;
                        if ( v=="" || v_length<1 ) continue;
                        if ( v.search( filters_map['filter_global_regexp'] )>-1 ) {
                            //console.log('In custom search. TRUE '+dataIndex+ ' '+k+':'+data[k] +' regexp='+v.search( filters_map['filter_global_regexp'] ));
                            return true;
                        }
                    }
                }
                return false;
            }
            else if ( filters_map.hasOwnProperty('filters') && Object.keys(filters_map['filters']).length>0) {
                var column_filters =  filters_map['filters'];
                //console.log( column_filters );
                var filter_results = [];
                for ( var fk in column_filters ) {
                    if ( data.hasOwnProperty(fk) ) {
                        var v = (data[fk]).toString();
                        v = (v!='')?v.trim():'';
                        var v_length = v.length;
                        var regexp = column_filters[fk]['regexp'];
                        var input = column_filters[fk]['input'];
                        var type = column_filters[fk]['type'];
                        var datatype = column_filters[fk]['datatype'];
                        switch(type) {
                            case "eq":
                                //console.log("filter eq: ",datatype, input, v);
                                if ( datatype=='int' || datatype=='decimal' ) {
                                    filter_results.push ( v == input);
                                }
                                else if ( datatype=='char' || datatype=='select' ) {
                                    filter_results.push ( v == input);
                                }
                                else if ( datatype=='date') { //format dd/mm/yyyy
                                    var parts = input.split("/");
                                    var dt = new Date(parseInt(parts[2], 10),
                                                      parseInt(parts[1], 10) - 1,
                                                      parseInt(parts[0], 10));
                                    filter_results.push ( v_length>0 && v==dt.yyyymmdd_dashed());
                                }
                                else if ( datatype=='datetime') { //format dd/mm/yyyy hh:mm
                                    var parts = input.split("/");
                                    var year_time = parts[2].toString().split(" "); // yyyy hh:mm
                                    var parts_time = year_time[1].toString().trim().split(":");
                                    var dt = new Date(parseInt(year_time[0], 10),
                                                      parseInt(parts[1], 10) - 1,
                                                      parseInt(parts[0], 10),
                                                      parseInt(parts_time[0], 10),
                                                      parseInt(parts_time[1], 10));
                                    filter_results.push ( v_length>0 && v==(dt.yyyymmddhhmm_dashed()+':00'));
                                }                                
                                break;
                            case "noteq":
                                if ( datatype=='int' || datatype=='decimal' ) {
                                    filter_results.push ( v=="" || v_length<1 || v != input);
                                }
                                else if ( datatype=='char' || datatype=='select' ) {
                                    filter_results.push ( v=="" || v_length<1 || v != input);
                                }
                                else if ( datatype=='date') { //format dd/mm/yyyy
                                    var parts = input.split("/");
                                    var dt = new Date(parseInt(parts[2], 10),
                                                      parseInt(parts[1], 10) - 1,
                                                      parseInt(parts[0], 10));
                                    filter_results.push ( v_length>0 && v==dt.yyyymmdd_dashed());
                                }
                                else if ( datatype=='datetime') { //format dd/mm/yyyy hh:mm
                                    var parts = input.split("/");
                                    var year_time = parts[2].toString().split(" "); // yyyy hh:mm
                                    var parts_time = year_time[1].toString().trim().split(":");
                                    var dt = new Date(parseInt(year_time[0], 10),
                                                      parseInt(parts[1], 10) - 1,
                                                      parseInt(parts[0], 10),
                                                      parseInt(parts_time[0], 10),
                                                      parseInt(parts_time[1], 10));
                                    filter_results.push ( v_length>0 && v!=(dt.yyyymmddhhmm_dashed()+':00'));
                                }                                
                                break;
                            case "cont":
                                filter_results.push ( v.search( regexp )>-1 );
                                break;
                            case "notcont":
                                filter_results.push ( v=="" || v_length<1 || v.search( regexp )==-1 );
                                break;
                            case "st":
                                filter_results.push ( v.search( regexp )==0 );
                                break;
                            case "notst":
                                filter_results.push ( v=="" || v_length<1 || v.search( regexp )!=0 );
                                break;
                            case "isempty":
                                filter_results.push ( v=="" || v_length<1 );
                                break;
                            case "isnotempty":
                                filter_results.push ( v_length>0 );
                                break;
                            case "gt":
                                if ( datatype=='int' || datatype=='decimal' ) {
                                    var v_as_number = Number(v);
                                    var input_as_number = Number(input);
                                    filter_results.push ( v_length>0 && v_as_number>input_as_number);
                                }
                                else if ( datatype=='date') { //format dd/mm/yyyy
                                    var parts = input.split("/");
                                    var dt = new Date(parseInt(parts[2], 10),
                                                      parseInt(parts[1], 10) - 1,
                                                      parseInt(parts[0], 10));
                                    filter_results.push ( v_length>0 && v>dt.yyyymmdd_dashed());
                                }
                                else if ( datatype=='datetime') { //format dd/mm/yyyy hh:mm
                                    var parts = input.split("/");
                                    var year_time = parts[2].toString().split(" "); // yyyy hh:mm
                                    var parts_time = year_time[1].toString().trim().split(":");
                                    var dt = new Date(parseInt(year_time[0], 10),
                                                      parseInt(parts[1], 10) - 1,
                                                      parseInt(parts[0], 10),
                                                      parseInt(parts_time[0], 10),
                                                      parseInt(parts_time[1], 10));
                                    filter_results.push ( v_length>0 && v>dt.yyyymmddhhmm_dashed());
                                }
                                else 
                                    continue;
                                break;
                            case "gte":
                                if ( datatype=='int' || datatype=='decimal' ) {
                                    var v_as_number = Number(v);
                                    var input_as_number = Number(input);
                                    filter_results.push ( v_length>0 && v_as_number>=input_as_number);
                                }
                                else if ( datatype=='date' ) {
                                    var parts = input.split("/");
                                    var dt = new Date(parseInt(parts[2], 10),
                                                      parseInt(parts[1], 10) - 1,
                                                      parseInt(parts[0], 10));
                                    filter_results.push ( v_length>0 && v>=dt.yyyymmdd_dashed());
                                }
                                else if ( datatype=='datetime') { //format dd/mm/yyyy hh:mm
                                    var parts = input.split("/");
                                    var year_time = parts[2].toString().split(" "); // yyyy hh:mm
                                    var parts_time = year_time[1].toString().trim().split(":");
                                    var dt = new Date(parseInt(year_time[0], 10),
                                                      parseInt(parts[1], 10) - 1,
                                                      parseInt(parts[0], 10),
                                                      parseInt(parts_time[0], 10),
                                                      parseInt(parts_time[1], 10));
                                    filter_results.push ( v_length>0 && v>=dt.yyyymmddhhmm_dashed());
                                }
                                else 
                                    continue;
                                break;
                            case "lt":
                                if ( datatype=='int' || datatype=='decimal' ) {
                                    var v_as_number = Number(v);
                                    var input_as_number = Number(input);
                                    filter_results.push ( v_length>0 && v_as_number<input_as_number);
                                }
                                else if ( datatype=='date' ) {
                                    var parts = input.split("/");
                                    var dt = new Date(parseInt(parts[2], 10),
                                                      parseInt(parts[1], 10) - 1,
                                                      parseInt(parts[0], 10));
                                    filter_results.push ( v_length>0 && v<dt.yyyymmdd_dashed());
                                }
                                else if ( datatype=='datetime') { //format dd/mm/yyyy hh:mm
                                    var parts = input.split("/");
                                    var year_time = parts[2].toString().split(" "); // yyyy hh:mm
                                    var parts_time = year_time[1].toString().trim().split(":");
                                    var dt = new Date(parseInt(year_time[0], 10),
                                                      parseInt(parts[1], 10) - 1,
                                                      parseInt(parts[0], 10),
                                                      parseInt(parts_time[0], 10),
                                                      parseInt(parts_time[1], 10));
                                    filter_results.push ( v_length>0 && v<dt.yyyymmddhhmm_dashed());
                                }
                                else 
                                    continue;
                                break;
                            case "lte":
                                if ( datatype=='int' || datatype=='decimal' ) {
                                    var v_as_number = Number(v);
                                    var input_as_number = Number(input);
                                    filter_results.push ( v_length>0 && v_as_number<=input_as_number);
                                }
                                else if ( datatype=='date' ) {
                                    var parts = input.split("/");
                                    var dt = new Date(parseInt(parts[2], 10),
                                                      parseInt(parts[1], 10) - 1,
                                                      parseInt(parts[0], 10));
                                    filter_results.push ( v_length>0 && v<=dt.yyyymmdd_dashed());
                                }
                                else if ( datatype=='datetime') { //format dd/mm/yyyy hh:mm
                                    var parts = input.split("/");
                                    var year_time = parts[2].toString().split(" "); // yyyy hh:mm
                                    var parts_time = year_time[1].toString().trim().split(":");
                                    var dt = new Date(parseInt(year_time[0], 10),
                                                      parseInt(parts[1], 10) - 1,
                                                      parseInt(parts[0], 10),
                                                      parseInt(parts_time[0], 10),
                                                      parseInt(parts_time[1], 10));
                                    filter_results.push ( v_length>0 && v<=dt.yyyymmddhhmm_dashed());
                                }
                                else 
                                    continue;
                                break;
                            case "btw":
                                if ( datatype=='int' || datatype=='decimal' ) {
                                    var limits = input.split("-");
                                    if ( limits.length != 2) filter_results.push(false);
                                    var limit_lower  = Number(limits[0]);
                                    var limit_higher = Number(limits[1]);
                                    if ( limit_lower>limit_higher ) {
                                        var tmp = limit_higher;
                                        limit_higher = limit_lower;
                                        limit_lower = tmp;
                                    }
                                    var v_as_number = Number(v);
                                    filter_results.push ( v_length>0 && v_as_number>=limit_lower && v_as_number<=limit_higher );
                                }
                                else 
                                    continue;
                                break;
                                
                        }
                    }
                    
                }
                var filter_res = true;
                for ( var i=0; i<filter_results.length; i++)
                    filter_res = filter_res && filter_results[i];
                return filter_res;
            }
            
            /*var min = parseInt( $('#min').val(), 10 );
            var max = parseInt( $('#max').val(), 10 );
            var age = parseFloat( data[3] ) || 0; // use data for the age column
     
            if ( ( isNaN( min ) && isNaN( max ) ) ||
                 ( isNaN( min ) && age <= max ) ||
                 ( min <= age   && isNaN( max ) ) ||
                 ( min <= age   && age <= max ) )
            {
                return true;
            } */
            return true;
        }
    );       
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
        var yyyy = this.getFullYear();
        var mm = this.getMonth()  < 9 ? "0" + (this.getMonth() + 1) : (this.getMonth() + 1); // getMonth() is zero-based
        var dd = this.getDate()  < 10 ? "0" + this.getDate() : this.getDate();
        var hh = this.getHours() < 10 ? "0" + this.getHours() : this.getHours();
        var mmin = this.getMinutes() < 10 ? "0" + this.getMinutes() : this.getMinutes();
        return "".concat(dd).concat('/').concat(mm).concat('/').concat(yyyy).concat(' ').concat(hh).concat(':').concat(mmin);
    };
    Date.prototype.yyyymmdd_dashed = function() {
        var yyyy = this.getFullYear();
        var mm = this.getMonth() < 9 ? "0" + (this.getMonth() + 1) : (this.getMonth() + 1); // getMonth() is zero-based
        var dd  = this.getDate() < 10 ? "0" + this.getDate() : this.getDate();
        return "".concat(yyyy).concat('-').concat(mm).concat('-').concat(dd);
    };
    Date.prototype.yyyymmddhhmm_dashed = function() {
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

    dt_persistent_state = {};  // This object will be persisted in
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
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":false, 
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
                            var d = new Date( data );
                            return d.ddmmyyyyhhmm();
                    }
                    return data;
                } }
<?php   elseif($c[1]=="ref"):  ?>    
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":false, 
            render: function (data, type) {
                    if (type === "display") {
                            if ( data == '') return '';
                            return "<a href='<?php echo site_url($c[3]) ?>' ref='<?php echo $c[4]?>' class='dt_href' style='text-decoration: none;'>"+data+"</a>";
                    }
                    return data;
                } }
<?php   elseif(count($c)>3 && $c[3]=="invisible"):  ?>    
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":true, "visible":false}
<?php   else:   ?>    
        { "data": "<?php echo $c[0]?>", "cl_label":"<?php echo $c[2]?>", "hidden":false}
<?php   endif;  }?>
    ];
    // Default datatables column sorting initialisation
            dt_persistent_state.dt_sorting = [  // Columns that are added to the datatable
                { "column": "<?php echo $descripteur['champs'][0][0]?>", "dir":"desc" }
            ];
    dt_persistent_state.column_order = [ ]; // Columns that are added to the datatable
    dt_persistent_state.invisible = [ ]; // Columns that are added to the datatable
<?php   $i =0;
        foreach($descripteur['champs'] as $c) { ?>
            dt_persistent_state.column_order["<?php echo $c[0]; ?>"] = <?php echo $i++ ?>;
<?php   if(count($c)>3 && $c[3]=="invisible"): ?>
            dt_persistent_state.invisible["<?php echo $c[0]; ?>"]="<?php echo $c[1]; ?>";
<?php   endif;
        }?>


<?php if ($CBSelect_exists): ?>    
    $('#datatable_div').prepend('<div class="masse_actions"><label for="action">Actions de masse</label>'+
                                    '<select class="masse_actions_select"></select>'+
                                    '<button type="button" class="btn btn-default btn-xs" id="btn_action_all">Ok</button>'+
                                    '&nbsp;&nbsp;&nbsp;&nbsp;'+
                                '</div>');
    var CBSelect_column_id = <?php echo $CBSelect_column_id; ?>;
<?php   endif; ?>

    var row_data_map = new Map();

    function dt_init_resizable() {
        $('#datatable').colResizable({
            liveDrag:true,
            resizeMode:'overflow',
            //resizeMode:'fit',
            //minWidth: 25, 
            // Callback function:
            // After table column resize, get the widths of the table columns
            onResize: onResizeDoSync
        });
        reWidthInputFields();        
    }
    function onResizeDoSync(currentTarget) {
        $('#datatable_div').width($('#datatable.JColResizer').width());
        $('div.dataTables_scrollHeadInner').find('table').css('margin-right', getScrollBarWidth());
        $('div.dataTables_scrollHeadInner').find('table').css('float', 'left');
        $('div.dataTables_scrollHeadInner').find('table').width($('#datatable.JColResizer').width());
        dt_persist_state();
        dt_reapply_column_widths(); // to sync headers
        //reWidthInputFields();  
        dt_persist_state();
    }
    function reWidthInputFields() {
        $('input.resizable').each(function (index, elem){
            var el = $(elem);
            el.width( el.parent().width() -40 );
        });
    }

    Array.prototype.diff = function(a) {
        return this.filter(function(i) {return a.indexOf(i) < 0;});
    };
    function dt_persist_state() {
         if ( !DTinitialised() ) return;
        var dt_new_state = [];
        var map_col_widths = [];
        var datatablesBodyCells = $('#datatable tr:first td');
        datatablesBodyCells.each(function(index){
            map_col_widths[$(this).attr('data')] = $(this).width();
        });
        DT.columns().every(function(cl){
            var column = DT.column(cl);
            var dt_head = column.header();
            var cl_head = $(dt_head);
            var _cl_data, _cl_label, _cl_width, _cl_hidden;
            _cl_data  = cl_head.attr('data');
            _cl_label = cl_head.find('div').find('span').text();
            _cl_width = parseInt(map_col_widths[cl_head.attr('data')]);
            _cl_hidden = !column.visible();
            if (_cl_data=="RowID")
                dt_new_state.push({"data": _cl_data, "cl_label":_cl_label, "width":_cl_width, "hidden":_cl_hidden, "visible":false});
            else if ((Object.keys(dt_persistent_state.invisible)).includes(_cl_data))
                dt_new_state.push({"data": _cl_data, "cl_label":_cl_label, "width":_cl_width, "hidden":_cl_hidden, "visible":false});
            else 
                dt_new_state.push({"data": _cl_data, "cl_label":_cl_label, "width":_cl_width, "hidden":_cl_hidden});
        });
        dt_persistent_state.dt_columns = dt_new_state;
        dt_persistent_state.datatable_div_width =  $('#datatable_div').width();
        //console.log('Persistent state: ', dt_persistent_state);
    }
    function dt_restore_state(st) {
        if ( !DTinitialised() ) return;
        if (typeof st == undefined) return;
        if (typeof st.dt_columns == undefined) return;
        $("#datatable").colResizable({ disable:true });
        
        $('#datatable_div').width(st.datatable_div_width);

        // Reorder columns appropriately
        var current_column_order = [];
        DT.columns().every(function(cl){
            var dt_head = DT.column(cl).header();
            current_column_order[$(dt_head).attr('data')] = cl;
        });
        var new_column_order = [];
        for ( var i=0; i<st.dt_columns.length; i++)
        {
            var col = st.dt_columns[i];
            new_column_order.push(current_column_order[col.data]);
        }
        DT.colReorder.order( new_column_order );

        // Show/Hide columns appropriately
        var hidden_columns = [];
        for ( var i=0; i<st.dt_columns.length; i++)
        {
            var col = st.dt_columns[i];
            if ( col.hidden == true )
                hidden_columns.push(col.data);
        }
        DT.columns().every(function(cl){
            var dt_head = DT.column(cl).header();
            if ( $.inArray($(dt_head).attr('data'), hidden_columns)>-1 )
                DT.column(cl).visible( false );
            else
                DT.column(cl).visible( true );
        });

        var do_reload = false;
        // Finally, check sorting..
        if ( st.dt_sorting[0].column != dt_persistent_state.dt_sorting[0].column ||
            st.dt_sorting[0].dir != dt_persistent_state.dt_sorting[0].dir)
            do_reload=true;

        dt_persistent_state = st;
        DT.draw();
        dt_reapply_column_widths();

        if (do_reload) {
            update_filters_map( );
            DT.draw();
        }

        // Reinitialise resizability.
        //setTimeout( function () {
            dt_init_resizable();
            dt_persist_state();
            dt_reapply_column_widths();
        //}, 250 );

    }


    function dt_reapply_column_widths() {
        if (typeof dt_persistent_state == undefined) return;
        if (typeof dt_persistent_state.dt_columns == undefined) return;
        var map_column_to_width = {};
        for ( var i=0; i<dt_persistent_state.dt_columns.length; i++)
        {
            var col = dt_persistent_state.dt_columns[i];
            map_column_to_width[col.data] = col.width;
        }
        
        var datatablesBodyCells = $('#datatable tr:first td');
        datatablesBodyCells.each(function(index){
            $(this).width( map_column_to_width[$(this).attr('data')] );
        });
        
        var datatablesHeaderCells = $('#data_table_columns_header').first().find('td');
        datatablesHeaderCells.each(function(index){
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
        //$('#datatable_loading').append((((new Date()).getTime()-dt_timer)/1000).toFixed(2)+ 'sec <span>&nbsp;&nbsp;|&nbsp;&nbsp;Fetching data...</span>')
        console.log(((((new Date()).getTime()-dt_timer)/1000).toFixed(2)+ 'sec \nFetching data...'));
        var DT_DATA = [];
        $.ajax({
            url: '<?= site_url($descripteur['datasource']."_json_client/")?>',
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if ( typeof data.data != undefined && data.data != null && data.data.length>0 ) {
                    DT_DATA = data.data;
                    console.log((((new Date()).getTime()-dt_timer)/1000).toFixed(2)+ 'sec - '+(DT_DATA.length)+' items.\nLoading DataTables & scripts...');
                    load_datatable();
                }
            }
        });
        /*$.ajax({
            url: '<?= site_url($descripteur['datasource']."_json_client/")?>',
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if ( typeof data.data != undefined && data.data != null && data.data.length>0 ) {
                    var row_array = data.data;
                    //console.log(row_array);
                    //$('#datatable_loading').append('<span>&nbsp;got '+(row_array.length)+' items</span><span>&nbsp;&nbsp;'+(((new Date()).getTime()-dt_timer)/1000).toFixed(2)+ 'sec'+'|&nbsp;&nbsp;Building table...</span>')
                    console.log((((new Date()).getTime()-dt_timer)/1000).toFixed(2)+ 'sec - '+(row_array.length)+' items.\nBuilding table...');
    
                    $('#datatable').append('<tbody>');
                    row_array.forEach(function(row){
                        var row_data = objectToMap(row);
                        row_data_map.set( row.RowID, row_data );
                        $('#datatable').find('tbody').append($('<tr>')); 
                        var last_tr = $('#datatable>tbody>tr:last');
                        dt_persistent_state.dt_columns.forEach( function(col) {
                            if ( col.data != null && col.data != 'null' )
                                last_tr.append($('<td>').html(row[col.data]));
                            else 
                                last_tr.append($('<td>').html(''));
                        });

                    });
                    //$('#datatable_loading').append('<span>&nbsp;done.'+(((new Date()).getTime()-dt_timer)/1000).toFixed(2)+ 'sec</span><span>&nbsp;&nbsp;|&nbsp;&nbsp;Loading DataTables & scripts...</span>')
                    console.log((((new Date()).getTime()-dt_timer)/1000).toFixed(2)+ ' sec\nLoading DataTables & scripts...')
                    load_datatable();
                }
            }
        });*/
        function load_datatable () {
            getScrollBarWidth(); // call it in order to calculate scroller width anf cache result.
            //console.log(dt_persistent_state.dt_sorting[0].column, dt_persistent_state.dt_sorting[0].dir);
            DT = $('#datatable').DataTable( {
                language: {
                    url: '<?php echo base_url('assets/js/French.json')?>'
                },
                /* NO, THIS IS CLIENT VERSION!! serverSide: true, */
                data: DT_DATA,
                "createdRow": function ( row, data, index ) {
                    row_data_map.set( data.RowID, objectToMap(data) );
                },
                ordering: true,
                "order": [[0, "desc"]],
                searching: true,
                scrollY: 575,
                scroller: {
                    loadingIndicator: true
                },
                columns: dt_persistent_state.dt_columns,
        <?php   if ($CBSelect_exists): ?>    
                columnDefs: [ {
                    orderable: false,
                    searchable: false,
                    targets:   0
                }, {
                    orderable: false,
                    searchable: false,
                    targets:   1
                }, {
                    orderable: false,
                    searchable: false,
                    targets:   2
                } ], 
                select: { style: 'os', selector: 'td:first-child' },
        <?php   endif; ?>    
                rowId: 'RowID',
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
                    if (typeof prepareColumnsCustomControls == 'function') {
                        prepareColumnsCustomControls();
                    } 
                    if (typeof apply_default_filters == 'function') {
                        apply_default_filters();
                    }
                    if (typeof apply_default_ordering == 'function') {
                        apply_default_ordering();
                    }
                    dt_persist_state();
                    console.log('initComplete-END');
                },
                fnDrawCallback: function( settings ) {
                    dt_reapply_column_widths();
                    
                    $('.dataTables_scrollBody').css('overflow-x','hidden');
                    $('.dataTables_scrollBody').css('overflow-y','overlay');
                    $('.dataTables_scrollHeadInner').css('padding-left', '0px');

                    reWidthInputFields();        
                    // Destroy and init again colResizable in order to
                    // recreate resize handles for new column order.
                    $("#datatable").colResizable({ disable:true });
                    <?php if ($CBSelect_exists): ?>                    
                    prepareMasseActions();
                    <?php endif; ?>
                    if (_rowCustomControlsPrepared==false && typeof prepareRowCustomControls == 'function') { 
                            _rowCustomControlsPrepared = true;
                            DT.rows().every(function ( rowIdx, tableLoop, rowLoop ) {
                                prepareRowCustomControls(this); 
                            });
                        }
                    syncSorting(DT.order());
                    $('#datatable_loading').hide();
                    $('#datatable_div').show();
                    
                    dt_init_resizable();
                    $('#datatable_div').width($('#datatable.JColResizer').width());
                    $('div.dataTables_scrollHeadInner').find('table').css('margin-right', getScrollBarWidth());
                    $('div.dataTables_scrollHeadInner').find('table').css('float', 'left');
                    $('div.dataTables_scrollHeadInner').find('table').width($('#datatable.JColResizer').width());
                    //$('div.dataTables_scrollHeadInner').width($('#datatable.JColResizer').width()+getScrollBarWidth());
                    dt_persist_state();
                    dt_reapply_column_widths(); // to sync headers
                    reWidthInputFields();  
                    dt_persist_state();
                    //$('#datatable_loading').append('done. Table fully displayed '+(((new Date()).getTime()-dt_timer)/1000).toFixed(2)+ 'sec._');
                    console.log('DONE-Table fully displayed in '+(((new Date()).getTime()-dt_timer)/1000).toFixed(2)+ 'sec._');
                }
    
            } );   
        }
        
        $(window).bind('resizeEnd', function() {
            //Due to infinite scrolling plugin, table header is disconnected from
            // the table itself, so syncing is required.
            $('.dataTables_scrollHeadInner').css('width',$('#datatable').css('width'));
            $('.dataTables_scrollHeadInner table').css('width',$('#datatable').css('width'));
            dt_persist_state();
            dt_reapply_column_widths();
        });
        $(window).resize(function() {
            if(this.resizeTO) clearTimeout(this.resizeTO);
            this.resizeTO = setTimeout(function() {
                $(this).trigger('resizeEnd');   //make sure resizing by user drag is finished
            }, 500);                            // before syncing table and headers.
        });

        $('#err_msg').text(':-)');
    } );

    $('#masse_actions_cb').on('click', function(){
        
        var cb_val = $(this).is(":checked");
        console.log ('Set all checkboxes to '+ cb_val );
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
        update_filters_map( );
        DT.draw();
    });
    function clear_filter(filter_key, reload) {
        
        //console.log('clear_filter('+filter_key+', '+reload+')');
        $('#filter_type_'+filter_key).val('').attr('disabled','disabled');
        $('#filter_text_'+filter_key).text('').attr('disabled','disabled');
        $('#filter_input_'+filter_key).val('');
        $('#filter_input_'+filter_key).blur();
        if ( reload != false) {
            update_filters_map( );
            DT.draw();
        }
        $('#filter_clear_'+filter_key).hide();
        $('#filter_select_'+filter_key).attr('src', '<?php echo base_url('assets/images/filter_20.png')?>');
    }
    $('#filter_input_global').on('change paste', function(){
        $('.filter_clear').each(function(index){
            var filter_key = $(this).attr('id').replace('filter_clear_', '');
            clear_filter(filter_key, false);
        });
        update_filters_map( );
        DT.draw();
    });
    $('#filter_global_clear').on('click', function(event){
        
        $('#filter_input_global').val('');
        update_filters_map( );
        DT.draw();
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
    var doneTypingInterval = 500;  //time in ms (0.5 seconds)
    //$('.filter_input').on('change paste', function(){
    //    clearTimeout(typingTimer);
    //    typingTimer = setTimeout(doneTyping, doneTypingInterval);
    //});
    $('.filter_input').on('keypress', function(e){e.stopPropagation(); return true;});
    $('.filter_input').on('keydown', function(e){e.stopPropagation(); return true;});
    $('.filter_input').keyup(function(e){
        e.stopPropagation();
        if(e.keyCode == 13) { doneTyping(); } //Enter key
        return true;
    });
    function doneTyping () {
        update_filters_map();
        DT.draw();
    }
    function update_filters_map( ) {
        filters_map = {};
        delete filters_map['filters'] ;
        delete filters_map['filter_global'];
        delete filters_map['filter_global_regexp'];
        if ( !($('#filter_input_global').val()=="") ) {
            filters_map['filter_global'] = $('#filter_input_global').val(); 
            filters_map['filter_global_regexp'] = new RegExp(filters_map['filter_global'], "i");
        } else {
            filters_map['filters'] = {};
            $('.filter_type').each(function(index){
                if ( $(this).val() == '' ) return true;
                var column_idx = DT.column( $(this).parent().parent() ).index();
                var filter_key = $(this).attr('id').replace('filter_type_', '');
                var input_value = $('#filter_input_'+filter_key).val();
                var input_datatype = $('#filter_select_'+filter_key).attr('datatype');
                if ( input_value == '' ) {
                    if ($(this).val() == 'isempty' || $(this).val() == 'isnotempty')
                        filters_map['filters'][column_idx] = {'type':$(this).val(), 'input':'', 'key':filter_key, 'regexp':''};
                    else
                        return true;
                } else
                    filters_map['filters'][column_idx] = {'type':$(this).val(), 'input':input_value.trim(), 'key':filter_key, 'regexp':new RegExp(input_value, "i"), 'datatype':input_datatype};
            });
            if (Object.keys(filters_map['filters']).length == 0) 
                delete filters_map['filters'] ;
        }
        //dt_timer = new Date();
        return filters_map;
    }
    function do_filters( filters_map) {
        if (filters_map.hasOwnProperty('filter_global') && filters_map['filter_global']!="") {
            DT.search(filters_map['filter_global']).draw();
        }
        else if (filters_map.hasOwnProperty('filters') ) {
            var column_filters = filters_map['filters'];
            for (var k in column_filters){
                console.log("Filter: Column " + column_filters[k]['key']+ "["+ k + "], type "+column_filters[k]['type']+" val " + column_filters[k]['input']);
                DT.columns( k )
                    .search( column_filters[k]['input'] )
                    .draw();
            }       
        }
    }
    function syncSorting(orderArray) {
        var order_col = orderArray[0][0];
        var order_dir = orderArray[0][1];
        $('.sorter').text('Trier');
        var current_sorter = $(DT.column(order_col).header()).find('.sorter');
        if ( order_dir == "asc")
            current_sorter.html('&#9650;');
        else if ( order_dir == "desc")
            current_sorter.html('&#9660;');
        onResizeDoSync();
        onResizeDoSync();
        dt_reapply_column_widths();
    }
    $('.sorting').on('keypress keyup keydown', function(e){console.log(e);});
    $('.sorting_asc').on('keypress keyup keydown', function(e){console.log(e);});
    $('.sorting_desc').on('keypress keyup keydown', function(e){console.log(e);});
    $('.sorter').on('click', function(event) {
        event.stopPropagation(); 
        var sort_by = $(this).attr('id').replace('sorter_', '');
        //console.log(sort_by);
        //console.log( $(this).parent().parent() );
        //console.log( DT.column( $(this).parent().parent() ).index() );
        
        var order_by_index = DT.column( $(this).parent().parent() ).index();
        var order_dir = "asc";
        var orderArray = DT.order();
        if ( order_by_index == orderArray[0][0] ) {
            //console.log('flip order 1: '+ orderArray[0][0] + '-'+orderArray[0][1]);
            order_dir = (orderArray[0][1]=="asc")?"desc":"asc";
            //console.log('flip order 2: ' +order_by_index+'-'+order_dir);
        }
        //console.log('Do order: '+order_by_index+' '+order_dir);
        DT.order([order_by_index, order_dir]).draw();

        //console.log( DT.cell( $(this).parent() ) );
        //console.log( DT.cell( $(this).parent() ).eq(0).node() );
        //var idx = DT.cell( $(this).parent() ).index().column;
        
        /*
        if ( dt_persistent_state.dt_sorting[0].column == sort_by ) {
            dt_persistent_state.dt_sorting[0].dir = (dt_persistent_state.dt_sorting[0].dir=='asc')?'desc':'asc'; //flip direction
        }
        else {
            dt_persistent_state.dt_sorting[0].column = sort_by;
            dt_persistent_state.dt_sorting[0].dir = 'asc';
        }
        DT.; */
        return false;
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
            case 'select':    
                $('#filter_options').append(eq).append(noteq).append(isempty).append(isnotempty);
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
            console.log('toggle_filter_options_div '+column_id+' - '+column_datatype);
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
                    update_filters_map( );
                    DT.draw();
                    $('#filter_clear_'+column_id).hide();
                }
                else if ( $(this).attr('value')=="isempty" || $(this).attr('value')=="isnotempty" ){
                    $('#filter_type_'+column_id).val($(this).attr('value')).attr('disabled',false);
                    $('#filter_text_'+column_id).text($(this).text()).attr('disabled',false);
                    $('#filter_input_'+column_id).val('');
                    //$('#filter_clear_'+column_id).show();
                    update_filters_map( );
                    DT.draw();
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

    //$('#save_list').css('cursor', 'not-allowed');
    //$('#liste_vues').css('cursor', 'not-allowed');
    //$('#load_list').css('cursor', 'not-allowed');

    // réglage de l'ordre et de la visibilité des colonnes
    $("#rule_list").click(function(e) {
        e.preventDefault();
        <?php if ($FIXEDcolReorder>0) :?>
        $('#liste_colonnes_fixed_label').show();
        $('#liste_colonnes_fixed').show();
        var FIXEDcols = <?php echo $FIXEDcolReorder;?>;
        <?php endif; ?>
        console.log(dt_persistent_state.dt_columns);
        var colonnes = dt_persistent_state.dt_columns;
        var html = '';
        for (var i=0;i<FIXEDcols;i++) {
            var checked = ' checked';
            if (colonnes[i].hidden == true) {
                checked = '';
            }
            if (colonnes[i].visible == false)
                //html += '<li style="display: none;"><div class="checkbox"><label><input type="checkbox" class="liste_colonnes_cb fixed" value="'+i+'" data="'+colonnes[i].data+'" '+checked+'>'+colonnes[i].cl_label+'</label></div></li>';
                html += '<li style="display: none;"><div class="checkbox"><label>'
                    +'<input type="checkbox" value="'+i+'" data="'+colonnes[i].data+'" '+checked+' disabled readonly>'
                    +'<input type="text" class="liste_colonnes_cb" value="'+i+'" data="'+colonnes[i].data+'" style="width:20px;font-size:6pt;text-align:center;" readonly/>'
                    +colonnes[i].cl_label+'</label></div></li>';
            else 
                html += '<li><div class="checkbox"><label>'
                    +'<input type="checkbox" value="'+i+'" data="'+colonnes[i].data+'" '+checked+' disabled readonly />'
                    +'<input  style="display: none;" type="text" class="liste_colonnes_cb" value="'+i+'" data="'+colonnes[i].data+'" style="width:20px;font-size:6pt;text-align:center;" readonly/>'
                    +colonnes[i].cl_label+'</label></div></li>';
        }
        $("#liste_colonnes_fixed").html(html);
        $("#liste_colonnes_fixed").disableSelection();
        html = '';
        for (var i=FIXEDcols;i<colonnes.length;i++) {
            var checked = ' checked';
            if (colonnes[i].hidden == true) {
                checked = '';
            }
            if (colonnes[i].visible == false)
                //html += '<li style="display: none;"><div class="checkbox"><label><input type="checkbox" class="liste_colonnes_cb" value="'+i+'" data="'+colonnes[i].data+'" '+checked+'>'+colonnes[i].cl_label+'</label></div></li>';
                html += '<li style="display: none;"><div class="checkbox"><label>'
                    +'<input type="checkbox" value="'+i+'" data="'+colonnes[i].data+'" '+checked+' disabled readonly>'
                    +'<input type="text" class="liste_colonnes_cb" value="'+i+'" data="'+colonnes[i].data+'"  style="width:20px;font-size:6pt;text-align:center;" readonly/>'
                    +colonnes[i].cl_label+'</label></div></li>';
            else 
                html += '<li><div class="checkbox"><label>'
                    +'<input type="checkbox" class="liste_colonnes_cb" value="'+i+'" data="'+colonnes[i].data+'" '+checked+'>'
                    +'<input  style="display: none;" type="text" value="'+i+'" data="'+colonnes[i].data+'"  style="width:20px;font-size:6pt;text-align:center;" readonly/>'
                    +colonnes[i].cl_label+'</label></div></li>';
        }
        $("#liste_colonnes").html(html);
        $("#liste_colonnes").sortable();
        $("#liste_colonnes").disableSelection();
        $('#popup_reglage').modal('show');
    });
    $("#toutes_colonnes").click(function(e) {
        var etat = $(this).prop('checked');
        $(".liste_colonnes_cb").each(function(index){
            if ( $(this).hasClass('fixed') ) return true;
            if ( $(this).is('[readonly]') ) return true;
            $(this).prop('checked', etat);
        });
    });
    $("#popup_reglage_sauver").click(function(e) {
        
        // Make table columns temporarily non-resizable
        $("#datatable").colResizable({ disable:true });

        // Note which columns should be visible
        var list_updated_visible_columns = [];
        var colonnes = dt_persistent_state.dt_columns;
        var FIXEDcols = <?php echo $FIXEDcolReorder;?>;
        for (var i=0; i<FIXEDcols; i++) {
            if (colonnes[i].visible != false) {
                list_updated_visible_columns.push(colonnes[i].data);
            }
        }        
        $(".liste_colonnes_cb").each(function(index){
            var column_key = $(this).attr('data');
            var make_visible = $(this).prop('checked');
            if ( make_visible )
                list_updated_visible_columns.push(column_key);
        });
        console.log( 'list_updated_visible_columns:', list_updated_visible_columns );

        // Reorder columns
        var new_column_order = [];
        $(".liste_colonnes_cb").each(function(index){
            new_column_order[index] = parseInt($(this).prop('value'));
        });
        console.log( 'new_column_order:', new_column_order );
        DT.colReorder.order( new_column_order );

        // Hide/Show columns goinng through DataTable().columns()
        DT.columns().every(function(cl){
            
            var dt_head = DT.column(cl).header();
            if ( $.inArray($(dt_head).attr('data'), list_updated_visible_columns)>-1 )
                DT.column(cl).visible( true );
            else
                DT.column(cl).visible( false );
        });

        // Reinitialise resizability.
        setTimeout( function () {
            dt_init_resizable();
            dt_persist_state();
            //dt_reapply_column_widths();
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
        console.log('Save list: ', vue, data);
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
        var cache_invisible = dt_persistent_state.invisible;
        $.post( "<?php echo site_url("vues/reglages")?>", {id_vue:id}, function( data ) {
            if (data) {
                var new_state = JSON.parse(data);
                new_state.invisible = cache_invisible;
                console.log(new_state);
                dt_restore_state(new_state);
            }
        });
    }
</script>