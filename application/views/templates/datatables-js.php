<style>
    .dt-buttons{
        display: none;
    }
</style>
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

    $.fn.getType = function(){ return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase(); }
    
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

    $viewable_fields = viewable_fields($controleur, 'read');

    foreach($descripteur['champs'] as $c) {

        $is_viewable = verify_viewable_field($c[0], $viewable_fields);

        if($is_viewable || $c[0] == "checkbox"):

        echo $sep;
        $sep = ",\n";
        $column_counter++;
        if ($c[0]=="RowID"): $FIXEDcolReorder++ 
    ?>
        { 
            "data": "<?php echo $c[0]?>", 
            "cl_label":"<?php echo $c[2]?>", 
            "hidden":true, 
            "visible": false, 
            "width":0 
        }
<?php   
    elseif($c[0]=="CBSelect"):   $CBSelect_exists = true; $FIXEDcolReorder++; $CBSelect_column_id=$column_counter; 
?>    
        { 
            "data": null, 
            "cl_label":"<?php echo $c[2]?>", 
            "hidden":false, 
            "width":20, 
            render: function (data, type, row) {
                if (type === "display") {
                    return "<input type='checkbox' class='dt_checkbox' style='margin-left:10px; margin-right:10px;' />";
                }
                return data;
            }
        }
<?php elseif($c[2]=="RELANCES"):  $FIXEDcolReorder++; ?>    
        { 
            "data": "<?php echo $c[0]?>", 
            "cl_label":"<?php echo $c[2]?>", 
            "hidden":false, 
            "width":20, 
            render: function (data, type) {
                if (type === "display") {
                    return "<select class='dt_select' style='margin-left:10px; margin-right:10px;'></select>";
                }
                return data;
            }
        }
<?php elseif($c[1]=="currency"): ?>    
        { 
            "data": "<?php echo $c[0]?>", 
            "cl_label":"<?php echo $c[2]?>", 
            "hidden":false, 
            render: function (data, type) {
                if (type === "display") {
                    if ( $.isNumeric(data) )
                        return '<div style="text-align: right; padding-right:5px;">&euro;'+((parseFloat(data)+0.0).format(2, 3, '.', ','))+'</div>';
                    else    
                        return '<div style="text-align: right; padding-right:5px;">&euro;-,--</div>';
                }
                return data;
            } 
        }
<?php elseif($c[1]=="date"): ?>    
        { 
            "data": "<?php echo $c[0]?>", 
            "cl_label":"<?php echo $c[2]?>", 
            "hidden":false, 
            render: function (data, type) {
                if (type === "display") {
                        if ( data == '') return '';
                        var d = new Date( data );
                        return d.ddmmyyyy();
                }
                return data;
            } 
        }
<?php elseif($c[1]=="datetime"): ?>    
        { 
            "data": "<?php echo $c[0]?>", 
            "cl_label":"<?php echo $c[2]?>", 
            "hidden":false, 
            render: function (data, type) {
                if (type === "display") {
                        if ( data == '') return '';
                        if ( data == 'Invalid Date') return '';
                        var d = new Date( data );
                        return d.ddmmyyyyhhmm();
                }
                return data;
            } 
        }
<?php   
    elseif($c[1]=="href"):

    if (substr($c[3], 0, 1) == '*') {
        $_cssClass = '';
        $_target = " target='_blank' ";
        $c[3] = substr($c[3], 1);
    } else {
        $_cssClass = ' view-detail ';
        $_target = '';
    }
?>
        { 
            "data": "<?php echo $c[0]?>", 
            "cl_label":"<?php echo $c[2]?>", 
            "hidden":false, 
            render: function (data, type) {
                if (type === "display") {
                        if ( data == '') return '';
                        return "<a href='<?php echo site_url($c[3]) ?>' <?php if (count($c)>=5): ?>ref='<?php echo $c[4]?>'<?php endif;?> class='dt_href <?php echo $_cssClass; ?>' <?php echo $_target; ?> style='text-decoration: none;'>"+data+"</a>";
                }
                return data;
            } 
        }
<?php elseif($c[1]=="hreflist"): ?>
        { 
            "data": "<?php echo $c[0]?>", 
            "cl_label":"<?php echo $c[2]?>", 
            "hidden":false, 
            render: function (data, type) {
                if (type === "display") {
                        if ( data == '') return '';
                        if ( data == null) return '';
                        var links = '';
                        var items = data.split(':');
                        var item_index = 0;
                        items.forEach(function(elem){
                            links += "<a href='<?php echo site_url(ltrim($c[3], '*')) ?>' <?php if (count($c)>=5): ?>ref='<?php echo $c[4]?>' ref_index='"+item_index+"'<?php endif;?><?php if ($c[3][0] == '*') {?> target='_blank'<?php } ?> class='dt_hreflist<?php if ($c[3][0] != '*') {?> view-detail<?php } ?>' style='text-decoration: none;'>"+elem+"</a><br/>";
                            item_index++;
                        });
                        return links;
                }
                return data;
            } 
        }
<?php elseif($c[1]=="hreffile"):  ?>    
        { 
            "data": "<?php echo $c[0]?>", 
            "cl_label":"<?php echo $c[2]?>", 
            "hidden":false, 
            render: function (data, type) {
                if (type === "display") {
                        if ( data == '') return '';
                        return "<a href='<?php echo base_url() ?>"+data+"' class='dt_hreffile' target='_blank' style='text-decoration: none;'>"+(/[^/]*$/.exec(data)[0])+"</a>";
                }
                return data;
            } 
        }
<?php elseif(count($c)>3 && $c[3]=="invisible"):?>    
        { 
            "data": "<?php echo $c[0]?>", 
            "cl_label":"<?php echo $c[2]?>", 
            "hidden":true, 
            "visible":false
        }        
<?php else: ?>    
        { 
            "data": "<?php echo $c[0]?>", 
            "cl_label":"<?php echo $c[2]?>", 
            "hidden":false, 
            "width":90 
        }
<?php 
    endif;  

    endif;
}
?>
    ];

// Default datatables column sorting initialisation
<?php if (!empty($descripteur['default_order'])):?>
    dt_persistent_state.dt_sorting = [  // Default ordering set by Controller
        { 
            "column": "<?php echo $descripteur['default_order'][0]?>", 
            "dir":"<?php echo $descripteur['default_order'][0]?>" 
        }
    ];
<?php   else: ?>
    dt_persistent_state.dt_sorting = [  // Default ordering set by Controller
        { 
            "column": "", 
            "dir":"" 
        }
    ];
<?php   endif; ?>
    dt_persistent_state.invisible = []; // Columns that are added to the datatable

<?php $i =0;
        foreach($descripteur['champs'] as $c) { ?>
<?php   if(count($c)>3 && $c[3]=="invisible"): ?>
            dt_persistent_state.invisible["<?php echo $c[0]; ?>"]="<?php echo $c[1]; ?>";
<?php   endif;
        }
?>

<?php if ($CBSelect_exists): ?>    
    $('#datatable_div').prepend('<div class="masse_actions"><label for="action">Actions de masse</label> \
                                    <select class="masse_actions_select"></select> \
                                    <button type="button" class="btn btn-default btn-xs" id="btn_action_all">Ok</button> \
                                    &nbsp;&nbsp;&nbsp;&nbsp; \
                                </div>');
    var CBSelect_column_id = <?php echo $CBSelect_column_id; ?>;
<?php   endif; ?>

    var row_data_map = new Map();

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
        $('#datatable_div').width($('#datatable.JColResizer').width());
        $('div.dataTables_scrollHeadInner').find('table').css('margin-right', getScrollBarWidth());
        /*$('div.dataTables_scrollHeadInner').find('table').css('float', 'left');*/
        $('div.dataTables_scrollHeadInner').find('table').width($('#datatable.JColResizer').width());
        dt_persist_state();
        dt_reapply_column_widths(); // to sync headers
        reWidthInputFields();  
        dt_persist_state();
        $('.JCLRgrip').height($('div.dataTables_scrollBody').find('div').last().height());
        //$('#make_default_list').attr('disabled','disabled');
        handle_modified_view();
    }
    
    function reWidthInputFields() {
        /*$('input.resizable').each(function (index, elem){
            var el = $(elem);
            el.width( Math.max(40, el.parent().width() -40) );
        });*/
        $('div.resizable').each(function (index, elem){
            var el = $(elem);
            el.width( Math.max(38, el.parent().width()-30 ) );
            el.children('input.resizable').each(function(i, e){
                $(e).width( el.width()-16 );
            });
            el.children('select.resizable').each(function(i, e){
                $(e).width( el.width()-16-(((!(window.mozInnerScreenX == null)))?25:0) ); // FF Mozilla needs a bit more spacing, 25px.
            });
        }); 
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
        return BROWSER_ScrollBarWidth;
    };    

    Array.prototype.diff = function(a) {
        return this.filter(function(i) {return a.indexOf(i) < 0;});
    };
    function dt_persist_state() {
        var dt_new_state = [];
        var dt_new_filters_state = [];
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
                
            var _flt_type  = $('#filter_type_' +_cl_data).val();
            var _flt_input ='';
            if ( $('#filter_input_'+_cl_data).prop('type') == 'text' )
                _flt_input  = $('#filter_input_'+_cl_data).val();
            else if ( $('#filter_input_'+_cl_data).prop('type') == 'select-one' )
                _flt_input  = $('#filter_input_'+_cl_data+' option:selected').val();
            
            if (_flt_type!=undefined && _flt_type!='undefined' && _flt_type!="" ) 
                //console.log(cl, _cl_data, _flt_type, _flt_input );
                dt_new_filters_state.push({"filter":_cl_data,"type": _flt_type, "input":_flt_input}); 
        });
        dt_persistent_state.dt_columns = dt_new_state;
        dt_persistent_state.dt_filters = dt_new_filters_state;
        dt_persistent_state.datatable_div_width =  $('#datatable_div').width();
    }
    function dt_restore_state(st, restore_filters) {
        //console.log('dt_restore_state');
        if (typeof st == undefined) return;
        if (typeof st.dt_columns == undefined) return;
        if (typeof restore_filters == undefined) restore_filters=false;
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
        
        if (restore_filters) {
            if (typeof st.dt_filters == undefined || typeof st.dt_filters == 'undefined') {
                console.log('Instructed to restore state incl. filters; but no filters present.');
            } else {
                //console.log('restore_filters');
                //console.log('Instructed to restore state incl. filters; ',st.dt_filters.length,'filters found.', st.dt_filters);
                for ( var fc=0; fc<st.dt_filters.length; fc++)
                {
                    var flt = st.dt_filters[fc];
                    var _flt_name = flt.filter;
                    var _flt_type = flt.type;
                    var _flt_input = flt.input;
                    //console.log(fc, 'Restore filter: ', _flt_name, _flt_type, _flt_input, $('#filter_input_' +_flt_name).getType());
                    if ( _flt_type!=undefined && _flt_type!='undefined' && _flt_type!="") {
                        $('#filter_type_' +_flt_name).prop("disabled", false);
                        $('#filter_type_' +_flt_name).val(_flt_type);
                        var _flt_select_img = FILTER_ICONS[_flt_type];
                        if ( _flt_select_img == undefined || _flt_select_img == 'undefined' || _flt_select_img == '' ) {
                            console.log('Restore filter [ERROR]:Is '+_flt_type+' a valid type?');
                        }else {
                            $('#filter_select_' +_flt_name).attr('src', _flt_select_img);
                            if ( $('#filter_input_'+_flt_name).getType() == 'select' ) {
                                var select_id = '#filter_input_'+_flt_name;
                                var select_default_val = _flt_input;
                                restore_select__timed(select_id, select_default_val);
                            }
                            else if ( $('#filter_input_'+_flt_name).hasClass('datetimepicker_mask') || $('#filter_input_'+_flt_name).hasClass('datepicker_mask') ) {
                                $('#filter_input_'+_flt_name).val(_flt_input);
                                var datetime_id = '#filter_input_'+_flt_name;
                                var datetime_default_val = _flt_input.split('-',2);
                                setTimeout( function(){ //console.log('restore_filters-10', datetime_default_val);
                                    $(datetime_id).periodpicker('value', datetime_default_val); 
                                    //setTimeout( function(){doneTyping()}, 2000);
                                }, 50);
                            }
                            else { //if ( $('#filter_input_'+_flt_name).prop('type') == 'text' ) {
                                $('#filter_input_'+_flt_name).val(_flt_input);
                            }
                            //console.log( 'Restore red [x] ', $('#filter_type_' +_flt_name).parent().find('div.deleteicon span'));
                            $('#filter_type_' +_flt_name).parent().find('div.deleteicon span').removeClass('is_hidden');
                        }
                    }
                }
                do_reload=true;
            }
        }

        if (do_reload) {
            //$('#datatable').DataTable().ajax.reload();
            doneTyping();
        }

        // Reinitialise resizability.
        setTimeout( function () {
            dt_init_resizable();
            dt_persist_state();
            //if (restore_filters && do_reload) doneTyping();
        }, 200 );

    }
    
    function restore_select__timed(select_id, select_default_val) {
        if (typeof _masseActionsPrepared == "boolean") { //earlier 'liste-js.php' style
            // NB: Deprecated style
            setTimeout( function(){ //console.log('restore_select__timed-2000');
                                    $(select_id).find('option').each(function(index,element){
                                        if (element.value==select_default_val) {
                                            $(element).attr('selected', 'selected');
                                        }
                                    }); 
                                    //doneTyping();
                                    $(select_id).trigger('paste');
            }, 2000);
        }
        else if (typeof _masseActionsPrepared == "number") {
            // NB: New style, accomodating restore of filter values in vues (2016-05-26)
            setTimeout( function(){ //console.log('restore_select__timed-250', select_default_val);
                                    if ( _masseActionsPrepared <= 0 ) { 
                                        restore_select__timed(select_id, select_default_val);
                                        return;
                                    }
                                    $(select_id).find('option').removeAttr('selected');
                                    $(select_id).prop('selectedIndex',0)
                                    $(select_id).find('option').each(function(index,element){
                                        //console.log(element.value, select_default_val, (element.value==select_default_val)?"found":"");
                                        if (element.value==select_default_val) {
                                            $(element).attr('selected', 'selected');
                                            $(select_id).prop('selectedIndex',index)
                                        }
                                    }); 
                                    setTimeout( function(){ $(select_id).trigger('paste')}, 500);
                                    //$(select_id).trigger('paste');
            }, 250);
        }
    }


    function dt_reapply_column_widths() {
        if (typeof dt_persistent_state == undefined) return;
        if (typeof dt_persistent_state.dt_columns == undefined) return;
        var map_column_to_width = {};
        for ( var i=0; i<dt_persistent_state.dt_columns.length; i++)
        {
            var col = dt_persistent_state.dt_columns[i];
            map_column_to_width[col.data] = (col.width<80)?80:col.width;
        }

        var datatablesBodyCells = $('#datatable tr:first td');
        datatablesBodyCells.each(function(index){
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
        
        //$('#make_default_list').attr('disabled','disabled');
        
        $('#err_msg').text(':-|');
        $('#datatable_loading').append('<span>&nbsp;&nbsp;|&nbsp;&nbsp;Loading scripts...</span>')
        _rowCustomControlsPrepared=false;
        _fnDrawCallback_called=0;
        invisible_filters = {};

        var DT_ajax_data_builder = function (d) {
            dt_timer = (new Date()).getTime();
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
                    var input_value = '';
                    if ($('#filter_input_'+filter_key).hasClass('datepicker_mask') || $('#filter_input_'+filter_key).hasClass('datetimepicker_mask'))
                        input_value = $('#filter_input_'+filter_key).periodpicker('valueStringStrong');
                    else 
                        input_value = $('#filter_input_'+filter_key).val();
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
            //console.log('DT_ajax_data_builder', d);
        };

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
                data: DT_ajax_data_builder,
                dataSrc: function ( json ) {
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
                    if ( data.responseJSON )
                    $('#datatable_loading').append('<span>&nbsp;got '+(data.responseJSON.data.length)+' items</span><span>&nbsp;&nbsp;|&nbsp;&nbsp;Building table...</span>')
                    var right_now = new Date();
                    //$('#err_msg').html("Ajax call successful. :-) Got records "+data.responseJSON.recordsOffset+" to "+(parseInt(data.responseJSON.recordsOffset)+parseInt(data.responseJSON.recordsLimit)-1)+ " @ "+right_now.today() + " " + right_now.timeNow());
                    $('.sorter').hide();
                    //$('.sorter').text('Trier');   replaced by ◇ &#9671;
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
                    
                    $('#datatable_loading').append('<span>&nbsp;done.</span>');
                }
            },
            dom: 'Bfrtip',
            buttons: [
                
                'copy',
                {
                    extend: 'excelHtml5',
                    text: 'Export Xls',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            header: function ( data, columnIdx ) {
                                if(data.search('check-all') > 0) {
                                    var text = "";
                                } else {
                                    var text = data.substring(data.indexOf("<span>")+6,data.indexOf("</span>"));
                                }

                                return text;
                            }
                        }
                    }
                },
                'csv',
                'pdf',
                {
                    extend: 'print',
                    footer: true,
                    exportOptions: {
                        format: {
                            header: function ( data, columnIdx ) {
                                if(data.search('check-all') > 0) {
                                    var text = "";
                                } else {
                                    var text = data.substring(data.indexOf("<span>")+6,data.indexOf("</span>"));
                                }
                                
                                return text;
                            }
                        }
                    }
                },
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
                        handle_modified_view();
                    }, 200 );
                }
            },
            initComplete: function(settings) {
                //console.log('initComplete');
                dt_persist_state();
                $.ajax({
                    type: 'POST',
                    url: "<?php echo site_url("vues/get_default")?>",
                    data: {ctrl:'<?php echo $controleur?>'},
                    dataType: "json",
                    success: function( data ) {
                        if (data) {
                            notificationWidget.show("Mise à jour de la vue; s'il vous plaît, attendez","warning");
                            try {
                                dt_restore_state(JSON.parse(data), true);
                                $.post( "<?php echo site_url("vues/get_default_id")?>", {ctrl:'<?php echo $controleur?>'}, function( data ) {
                                    if (data) {
                                        currently_loaded_view=data;
                                        display_current_view();
                                        notificationWidget.show("Vue appliquée","success");
                                    }
                                }); 
                            } catch (ex) {
                                notificationWidget.show("Un problème technique empêché de voir charger","error");
                            }
                        }
                        else {
                            console.log('Probably no default view. :-(', data);
                        }
                    },
                    async:false
                });
                
                $('#datatable_loading').append('<span>&nbsp;done.</span><span>&nbsp;&nbsp;|&nbsp;&nbsp;Fetching data...</span>');
            },
            fnCreatedRow: function( nRow, aData, iDataIndex ) {
                var row = DT.row(iDataIndex);
                var row_data = objectToMap(aData);
                row_data_map.set( aData.RowID, row_data);
                if (typeof prepareRowCustomStyling == 'function') { 
                    prepareRowCustomStyling(row, row_data); 
                }
                
            },
            fnDrawCallback: function( settings ) {
                //console.log('fnDrawCallback');
                dt_reapply_column_widths();
                // Destroy and init again colResizable in order to
                // recreate resize handles for new column order.
                $("#datatable").colResizable({ disable:true });
                $('#datatable_loading').hide();
                $('#datatable_div').show();

                if (actionMenuBar.datatable.reselect) {
                    actionMenuBar.datatable.reselect();
                    actionMenuBar.datatable.reselect = null;
                }
                if (actionMenuBar.datatable.highlight) {
                    actionMenuBar.datatable.highlight();
                    actionMenuBar.datatable.highlight = null;
                }

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
                            $('.resizable').width(40);
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
                    }, 500);
                }
                _fnDrawCallback_called++;
            }
        } );

        // Function to generate a datatable helper object
        var datatable_helper = function(datatable, url, ajaxDataBuilder) {
            var helper = {
                controleur: url.replace(/^.+\/([^\/]+)\/[^\/]+_json\/$/, '$1'),

                // Returns the URL for getting the data from the server for the given ID
                dataSource: function(id) {
                    return url + id;
                },

                // Returns the AJAX parameters to use to request a row from the server
                ajaxParams: function(id) {
                    var data = {};
                    ajaxDataBuilder(data);
                    return {
                        url: helper.dataSource(id),
                        method: "POST",
                        dataType: "json",
                        data: data
                    };
                },

                // Reloads the currently selected row
                reloadCurrent: function() {
                    var id = helper.selected();
                    if (id) {
                        helper.reload(id);
                    }
                },

                $row: function(id) {
                    return $($(datatable).DataTable().row('#' + id).node());
                },

                // Returns the ID of the currently selected row,
                // or tells whether the row for the given ID is selected
                selected: function() {
                    if (arguments.length == 1) {
                        return helper.selected() == arguments[0];
                    }
                    return $(datatable).DataTable().row({selected: true}).id();
                },

                // used internally to keep track of selected row.
                // The value should be a callback function
                reselect: null,

                // used internally to keep track of row to highlight (after being added for instance)
                // The value should be a callback function
                highlight: null,

                // Reloads (requests) data from the server for the given ID
                reload: function(id) {
                    if (arguments.length != 1) {
                        $(datatable).DataTable().draw();
                    } else {
                        var isSelected = helper.selected(id);
                        $.ajax(
                            helper.ajaxParams(id)
                        ).done(function(data) {
                            // The record wasn't returned, maybe it doesn't exist anymore
                            // or it was filtered out
                            if (data.recordsFiltered == 0) {
                                return helper.unload(id);
                            }
                            if (data.recordsTotal && data.recordsTotal == 1) {
                                // Pseudo-event
                                var event = {
                                    type: null,
                                    bubbles: false,
                                    cancelable: false,
                                    id: id,
                                    data: data.data[0]
                                };
                                // Call the callbacks before the row is added to the datatable
                                helper._callbacks.loading.forEach(function(handler) {
                                    event.type = "loading";
                                    handler(event);
                                });

                                var $row = helper.$row(id);
                                $(datatable).DataTable().row($row).data(data.data[0]);

                                row_data_map.set(id, objectToMap(data.data[0]));

                                if (isSelected) {
                                    // Give the time for the DOM to catch up, so the "click" handler
                                    // can be properly registered and propagated on the "new" data
                                    setTimeout(function() {
                                        $row.click()
                                    }, 300);
                                }

                                // Call the callbacks after the row was added to the datatable
                                helper._callbacks.loaded.forEach(function(handler) {
                                    event.type = "loaded";
                                    handler(event);
                                });
                            }
                        });
                    }
                },

                // Removes the row with the given ID from the DataTable
                unload: function(id) {
                    // Pseudo-event
                    var event = {
                        type: null,
                        bubbles: false,
                        cancelable: false,
                        id: id
                    };
                    // Call the callbacks before removing from the datatable
                    helper._callbacks.unloading.forEach(function(handler) {
                        event.type = "unloading";
                        handler(event);
                    });

                    var $row = helper.$row(id);
                    $(datatable).DataTable().row($row).remove();
                    $row.remove();

                    // Call the callbacks after the row was removed from the datatable
                    helper._callbacks.unloaded.forEach(function(handler) {
                        event.type = "unloaded";
                        handler(event);
                    });
                },

                // Loads (requests) data from the server for the given ID
                load: function(id) {
                    $.ajax(
                        helper.ajaxParams(id)
                    ).done(function(data) {
                        // The record wasn't returned, it was most likely filtered out
                        // or a race condition deleted the record already.
                        // In any case, we don't need to do anything, since we assume
                        // the record wasn't in the datatable in the first place.
                        if (data.recordsFiltered == 0) {
                            return;
                        }
                        if (data.recordsTotal && data.recordsTotal == 1) {
                            // Pseudo-event
                            var event = {
                                type: null,
                                bubbles: false,
                                cancelable: false,
                                id: id,
                                data: data.data[0]
                            };

                            // Call the registered callbacks before the row is added to the datatable
                            helper._callbacks.loading.forEach(function(handler) {
                                event.type = "loading";
                                if (handler) {
                                    handler(event);
                                }
                            });

                            // Enforce re-selecting the current row (if needed)
                            // This may need to be done after a datatables redraw (see fnDrawCallback)
                            var selected = helper.selected();
                            if (selected) {
                                helper.reselect = function() {
                                    var row = $(datatable).DataTable().row('#' + selected);
                                    row.select();
                                    $(row.node()).click();
                                }
                            }
                            helper.highlight = function() {
                                var rowAdded = $(datatable).DataTable().row('#' + id);
                                $(rowAdded.node()).addClass('highlighted');
                            }

                            actionMenuBar.reset();
                            $(datatable).DataTable().row.add(data.data[0]).draw();

                            row_data_map.set(id, objectToMap(data.data[0]));

                            // Call the registered callbacks after the row was added to the datatable
                            helper._callbacks.loaded.forEach(function(handler) {
                                event.type = "loaded";
                                if (handler) {
                                    handler(event);
                                }
                            });
                        }
                    });
                },

                /**
                 * Array of callbacks for pseudo-events related to the DataTable
                 */
                _callbacks: {
                    loading: [],
                    loaded: [],
                    unloading: [],
                    unloaded: []
                },

                // Registers a pseudo-event handler
                on: function(event, handler) {
                    if (typeof event === 'string') helper._callbacks[event].push(handler);
                },

                // Unregisters all the handlers for a pseudo-event
                off: function(event) {
                    if (typeof event === 'string') helper._callbacks[event] = [];
                },

                // Returns the data for the given ID
                data: function(id) {
                    if (typeof id != 'undefined' && row_data_map && row_data_map.get) {
                        return row_data_map.get(id);
                    }
                    return null;
                },

                // Returns a toolbar-button status-checking function
                buttonStatus: function(id) {
                    return true;
                },

                // Returns a toolbar-button parameter-generating function
                buttonParams: function(id) {
                    return id;
                }
            };
            return helper;
        };

        // On informe la barre action de la présence de la DataTable
        actionMenuBar.datatable = datatable_helper('#datatable', '<?= site_url($descripteur['datasource'])?>_json/', DT_ajax_data_builder);

        actionMenuBar.datatable.on('unloading', function(ev) {
            if (Map.prototype.delete) row_data_map.delete(ev.id);
        });

        <?php if (ENVIRONMENT != 'production') { ?>
        actionMenuBar.datatable.on('loaded', function(ev) {
            console.log("Loaded in datatable: ", ev);
        });
        actionMenuBar.datatable.on('unloading', function(ev) {
            console.log("Unloading from datatable: ", ev);
        });
        <?php } ?>

        // Configure le système de propagation des pseudo-events pour la mise à jour de la DataTable
        try {
            var storageArea = 'localStorage';
            window[storageArea] && window[storageArea].length;
            $(window).bind('storage', actionMenuBar.listenBroadcast(storageArea));
            <?php if (ENVIRONMENT != 'production') { ?>
            $(window).bind('storage', function(e) {
                if (e.originalEvent) {
                    e = e.originalEvent;
                }
                console.log('Storage event received: ', e)
            });
            <?php } ?>
        } catch (e) {
            console.log("Could not bind a listener for storage events");
        }

        // Action à prendre quand une ligne entrée est sélectionnée dans la DataTable
        $('#datatable').on("click", "tr", function () {
            var row = DT.row(this);
            var id = row.id();

            // Est-ce que l'objet d'interfaçage à la DataTable a été configuré pour la barre d'action ?
            var status, params;
            if (actionMenuBar.datatable) {
                // Utilisons les fonctions de status et paramètrage spécifiques à cette entrée
                status = actionMenuBar.datatable.buttonStatus(id, row);
                params = actionMenuBar.datatable.buttonParams(id, row);
            } else {
                status = true;
                params = id;
            }
            // On ne considère que les boutons qui sont paramètrables avec l'id
            actionMenuBar.switch(".action-bar li:has(a[data-href-template!='#'])", status, params);
            $(this).removeClass('highlighted');
            DT.rows().deselect().row(this).select();

            $('#template-modal-detail').attr("data-id", id);
        });

        // Dans la grille de données :
        // Pour les liens vers des pages detail
        $("#datatable").on('click', "a.view-detail, a.open-form", function(ev){
            ev.preventDefault();
            $(this).closest("tr[id]").click();
            actionMenuBar.loadInModal(this, '#template-modal-detail');
        });

        $('#datatable').on('dblclick', "tbody tr[id]", function (ev) {
            // Trigger the click on the DOM element itself, in case no click event was registered
            $(".action-bar .action-double-click-handler").not('.disabled').find('a').get(0).click();
        });

        $(window).bind('resizeEnd', function() {
            //Due to infinite scrolling plugin, table header is disconnected from
            // the table itself, so syncing is required.
            dt_init_resizable();
            $('div.dataTables_scrollHeadInner').find('table').css('margin-right', getScrollBarWidth());
            /*$('div.dataTables_scrollHeadInner').find('table').css('float', 'left');*/
            $('div.dataTables_scrollHeadInner').find('table').width($('#datatable.JColResizer').width());
            dt_persist_state();
            dt_reapply_column_widths(); // to sync headers
            dt_persist_state();
            $('.JCLRgrip').height($('div.dataTables_scrollBody').find('div').last().height());
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
        $('#filter_type_'+filter_key).val('').attr('disabled','disabled');
        $('#filter_text_'+filter_key).text('').attr('disabled','disabled');
        $('#filter_input_'+filter_key).val('');
        $('#filter_input_'+filter_key).blur();
        if ($('#filter_input_'+filter_key).hasClass('datepicker_mask') || $('#filter_input_'+filter_key).hasClass('datetimepicker_mask')) 
            $('#filter_input_'+filter_key).periodpicker('clear');
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
                if ( $(this).closest('div.deleteicon').length>0 && $(this).closest('div.deleteicon').children('span').length>0) {
                    $(this).closest('div.deleteicon').children('span').removeClass('is_hidden');
                }
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
        if ( e.keyCode!=13 && $(this).val()!='' &&  $(this).val() == filter_val_cache) return false; //If filter value is unchanged, no need to perform filtering.
        
        //if(e.keyCode == 13) { //=Enter key
        //        doneTyping(); 
        //} 
        //else {
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        //}
        handle_modified_view();
        return true;
    });
    $('.filter_input').on('keydown', function () {
        filter_val_cache = $(this).val(); // Cache value before filter change.
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
    
    function quick_call_filters(filters, do_clear_all) {
        if (typeof do_clear_all == undefined) do_clear_all=false;
        // clear all filters
        $('#filter_input_global').val('');
        
        if (do_clear_all) $('.filter_clear').each(function(index){
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
            try {
                console.log("Quick apply filter: ",  f, " on ", $($('#filter_input_'+f['field'])));
                console.log($('#filter_input_'+f['field']).getType());
                var column_type = $('#filter_input_'+f['field']).getType();
                if ( column_type == 'select' ) {
                    $('#filter_input_'+f['field']).append('<option val="'+f['value']+'" selected>'+f['value']+'</option>');
                    var select_id = '#filter_input_'+f['field'];
                    var select_default_val = f['value'];
                    setTimeout( function(){ $(select_id).find('option').each(function(index,element){
                                                if (element.value==select_default_val) $(element).attr('selected', 'selected');
                                            }); 
                    }, 1000);
                }
                if ( $('#filter_input_'+f['field']).hasClass('datetimepicker_mask') || $('#filter_input_'+f['field']).hasClass('datepicker_mask') ) {
                    var datetime_id = '#filter_input_'+f['field'];
                    var datetime_default_val = f['value'].split('-',2);
                    setTimeout( function(){ $(datetime_id).periodpicker('value', datetime_default_val); }, 10);
                }
            } catch (ex) {
                //console.error(ex);
                // Expected to fail when column is invisible; maybe in future change behaviour to make 
                // column visible and then apply filter.
            }
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
        handle_modified_view();
    });


    var FILTER_ICONS = {
        'clear'   :'<?php echo base_url('assets/images/filter_20.png')?>',
        'eq'      :'<?php echo base_url('assets/images/filter_eq_20.png')?>',
        'noteq'   :'<?php echo base_url('assets/images/filter_noteq_20.png')?>',
        'cont'    :'<?php echo base_url('assets/images/filter_cont_20.png')?>',
        'notcont' :'<?php echo base_url('assets/images/filter_notcont_20.png')?>',
        'st'      :'<?php echo base_url('assets/images/filter_st_20.png')?>',
        'notst'   :'<?php echo base_url('assets/images/filter_notst_20.png')?>',
        'isempty' :'<?php echo base_url('assets/images/filter_isempty_20.png')?>',
        'isnotempty' :'<?php echo base_url('assets/images/filter_isnotempty_20.png')?>',
        'lt'      :'<?php echo base_url('assets/images/filter_lt_20.png')?>',
        'lte'     :'<?php echo base_url('assets/images/filter_lte_20.png')?>',
        'gt'      :'<?php echo base_url('assets/images/filter_gt_20.png')?>',
        'gte'     :'<?php echo base_url('assets/images/filter_gte_20.png')?>',
        'btw'     :'<?php echo base_url('assets/images/filter_btw_20.png')?>'
    }
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
            update_filter_options( column_datatype );
            var pos = $('#filter_select_'+column_id).offset();
            filter_options_div.css({
                "left": pos.left + 20,
                "top":  pos.top  + 20
            } );
            filter_options_div.show();

            $("#filter_options li").click(function() {
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
                    if ( $('#filter_input_'+column_id).closest('div.deleteicon').length>0 && $('#filter_input_'+column_id).closest('div.deleteicon').children('span').length>0) {
                        $('#filter_input_'+column_id).closest('div.deleteicon').children('span').addClass('is_hidden');
                    }
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
    
    $('div.deleteicon span').on('click', function(event){
        var span_clicked = event.target;
        event.stopPropagation();
        if ( $(span_clicked).hasClass('is_hidden') ) return; // Nothing to do, no filter should be on anyway.
        var rel_filter_input_field = $(span_clicked).closest('.filter_combo_div').find('.filter_input');
        if ( rel_filter_input_field.length>0 ) {
            var rel_filter_input_field_id = (rel_filter_input_field).attr('id');
            var rel_column_id = rel_filter_input_field_id.replace('filter_input_', '');
            $('#filter_type_'+rel_column_id).val('').attr('disabled','disabled');
            $('#filter_text_'+rel_column_id).text('').attr('disabled','disabled');
            if ( rel_filter_input_field.hasClass('select_mask')) {
                $('#filter_input_'+rel_column_id).prop('selectedIndex', 0);
            }
            else if ( rel_filter_input_field.hasClass('datetimepicker_mask') || rel_filter_input_field.hasClass('datepicker_mask')) {
                $('#filter_input_'+rel_column_id).periodpicker('clear');
                $('#filter_type_'+rel_column_id).val('').attr('disabled','disabled');
                $('#filter_text_'+rel_column_id).text('').attr('disabled','disabled');
            }
            else {
                $('#filter_input_'+rel_column_id).val('');
            }
            $('#datatable').DataTable().ajax.reload();
            $('#filter_clear_'+rel_column_id).hide();
            if ( $('#filter_input_'+rel_column_id).closest('.filter_combo_div').find('div.deleteicon').length>0 ) {
                 $('#filter_input_'+rel_column_id).closest('.filter_combo_div').find('div.deleteicon').children('span').addClass('is_hidden');
            }
            rel_filter_input_field.blur();
            $('#filter_select_'+rel_column_id).attr('src', '<?php echo base_url('assets/images/filter_20.png')?>');
        }
    });
    
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
                    handle_modified_view();
                }, 100);
                if ( $(select).closest('div.deleteicon').length>0 && $(select).closest('div.deleteicon').children('span').length>0) {
                    $(select).closest('div.deleteicon').children('span').removeClass('is_hidden');
                }
                
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
        //clearButtonInButton: true,
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
        var ref_input = $(this).closest('input.filter_input');
        ref_input.val( new_value );
        ref_input.trigger('click');
        ref_input.trigger('keyup');
        doneTyping();
        $(this).closest('.filter_combo_div').find('div.deleteicon').children('span').removeClass('is_hidden');
        if ( $(this).closest('.filter_combo_div').find('input.filter_type').prop('disabled') || $(this).closest('.filter_combo_div').find('input.filter_type').val()=='' ) {
            $(this).closest('.filter_combo_div').find('img.filter_select').attr('src', '<?php echo base_url('assets/images/filter_btw_20.png')?>');
            $(this).closest('.filter_combo_div').find('input.filter_type').val('btw').attr('disabled',false);
            $(this).closest('.filter_combo_div').find('div.filter_text').text('entre').attr('disabled',false);
        }        
    });    

    //$('#save_list').css('cursor', 'not-allowed');
    //$('#liste_vues').css('cursor', 'not-allowed');
    //$('#load_list').css('cursor', 'not-allowed');

    // réglage de l'ordre et de la visibilité des colonnes
    $("#rule_list").click(function(e) {
        e.preventDefault();
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
    $("#save_list").click(function(e){ handle_save_list(e)});
    function handle_save_list(e, cb, dialog_msg) {
        e.preventDefault();
        if ( typeof dialog_msg === "string")
            var vue = prompt(dialog_msg, "ma vue");
        else 
            var vue = prompt("Nom de la vue", "ma vue");
        if ( vue == null ) return false;
        var controleur = '<?php echo $controleur?>';
        //var data = kendo.stringify(grid.getOptions());
        var data = JSON.stringify( dt_persistent_state );
        $.post( "<?php echo site_url("vues/nouvelle")?>", {vue:vue, data:data, ctrl:controleur}, function( data ) {
            if (data) {
                var new_elem = '';
                new_elem += '<li><a class="vue" href="#'+data+'" style ="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="'+vue+'" >'+vue+'';
                new_elem += '<button class="btn btn-default delete_view_btn" style="float:right; font-size:8pt; color:#EEE60B; background-color:#E49C05; padding:1px; padding-left:3px; padding-right:3px; ">DEL</button>';
                new_elem += '</a></li>';
                $('#liste_vues').append(new_elem);
                notificationWidget.show("La vue a été sauvegardée","success");
                $('.vue').unbind('click');
                $('.vue').on('click', load_view_clicked);
                currently_loaded_view = data;
                if ( typeof cb === "function") { console.log(cb.name); cb(); }
            }
            else {
                notificationWidget.show("Un problème technique a empéché la sauvegarde de la vue","error");
            }
        });
    }
    // liste des vues enregistrées
    $('.vue').on('click', load_view_clicked);

    var currently_loaded_view = false; //No view loaded yet.
    function load_view_clicked(e) {
        e.preventDefault();
        var hash = e.target.hash;
        if (hash==undefined || hash=='undefined' || hash=='') return;
        var id = hash.substr(1);
        handle_load_view(id);
    }
    function handle_load_view(id) {
        console.log('Load view', id);
        notificationWidget.show("Mise à jour de la vue; s'il vous plaît, attendez","warning");
        $.post( "<?php echo site_url("vues/reglages")?>", {id_vue:id}, function( data ) {
            if (data) {
                try { 
                    dt_restore_state(JSON.parse(data), true);
                    currently_loaded_view = id;
                    notificationWidget.show("Vue appliquée","success");
                    display_current_view();
                } catch(err) {
                    notificationWidget.show("Un problème technique empêché de voir charger","error");
                }
            }
        });
    }
    function display_current_view() {
        if (currently_loaded_view) {
            $.post( "<?php echo site_url("vues/get_name")?>", {id_vue:currently_loaded_view}, function( data ) {
                if (data) {
                    $('#current_view_label').hide().html('Vue actuelle: <i>'+data+'</i>').fadeIn(500);
                } 
            });
        }
    }
    function handle_modified_view() {
        currently_loaded_view = false; //Last loaded view has been resized; thus no longer current.
        var is_modified = $('#current_view_label').find('#current_view_is_modified');
        if ( is_modified.length==0 ) {
            $('#current_view_label').append('<span id="current_view_is_modified" style="font-style:italic; font-weight:bold; size: 8pt; "> (modifié)</span>');
        }
    }
    
    $('#make_default_list').on('click', function(e){
        if ( currently_loaded_view == false ) { //Request that this view is first saved.
            handle_save_list(e, function(){ 
                handle_make_default_list();
            });
        } else {
            $.ajax({
                type: 'POST',
                url: "<?php echo site_url("vues/is_owned")?>",
                data: {id_vue:currently_loaded_view},
                dataType: "json",
                success: function( data ) {
                    console.log('Is view owned? ', data);
                    if (data) {
                        handle_make_default_list();                        
                    }
                    else {
                        console.log('This view is not owned; cannot make default. :-(', data);
                            handle_save_list(e, function(){ 
                                handle_make_default_list(); 
                            }, 
                            "Il s'agit d'une vue globale, donc il faut cloner avant de la rendre par défaut.\nVeuillez indiquer un nom."
                        );
                    }
                },
                async:false
            });
        }
    });
    function handle_make_default_list(){ 
        console.log('Making default view: ', currently_loaded_view)
        var controleur = '<?php echo $controleur?>';
        var default_view_id = currently_loaded_view;
        $.post( "<?php echo site_url("vues/set_default")?>", {id_vue:default_view_id, ctrl:controleur}, function( data ) {
            if (data) {
                notificationWidget.show("La vue actuelle est définie comme étant par défaut","success");
                var moved_element = ($('#default_view_label').length>0)?$('#default_view_label').detach():false;
                $('.vue').each(function(index, elem){
                    if ( $(this).attr('href')==("#"+default_view_id)) {
                        if (moved_element==false) {
                            $(this).append('<span id="default_view_label" title="User default" style="float:right; font-size:10pt; font-weoght:bold; color:#11aa11">&nbsp;<img src="<?php echo base_url('assets/images/default_view_user.png')?>" style="width:20px; height:20px;"/>&nbsp;</span>');
                        } else {
                            moved_element.appendTo( $(this) );
                        }
                    }
                })
            } else {
                notificationWidget.show("Un problème technique a empêché de définir la vue par défaut","error");
            }
        });
    }
    
    <?php if($this->session->userdata('utl_profil')==1): ?>
    $('#make_global_default_list').on('click', function(e){ 
        console.log('#make_global_default_list', currently_loaded_view);
        if ( confirm("Cette vue deviendra une valeur par défaut globale; Chaque utilisateur peut l'annuler.\nVeuillez confirmer pour l'appliquer.")) {
            if ( currently_loaded_view ) {
                handle_make_global_default_list();
            }
            else {
                handle_save_list(e, function(){ 
                    handle_make_global_default_list(); 
                });
            }
        } else {
            notificationWidget.show("Définir comme vue par défaut globale interrompue","warning");
        }
    });
    function handle_make_global_default_list() {
        console.log('Making default view: ', currently_loaded_view)
        var controleur = '<?php echo $controleur?>';
        $.post( "<?php echo site_url("vues/set_global_default")?>", {id_vue:currently_loaded_view, ctrl:controleur}, function( data ) {
            if (data) {
                notificationWidget.show("La vue actuelle a été définie comme une valeur par défaut globale.","success");
            } else {
                notificationWidget.show("Un problème technique a empêché de définir la vue par défaut","error");
            }
        });
    }
    <?php endif; ?>
    
    $('body').on('click', '.delete_view_btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        handle_delete_view_btn(e);
        return false;
    });
    function handle_delete_view_btn(e){
        e.preventDefault();
        e.stopPropagation();
        try {
            var hash = e.target.parentElement.hash;
            var li_removed = e.target.parentElement;
            console.log('Delete view ', hash);
            var delete_view_id = hash.substr(1);
            $.post( "<?php echo site_url("vues/delete")?>", {id_vue:delete_view_id}, function( data ) {
                console.log(data);
                if (data) {
                    li_removed.remove();
                    notificationWidget.show("Affichage effacé avec succès","success");
                    if ( currently_loaded_view==delete_view_id ) {
                        currently_loaded_view = false;
                        $('#current_view_label').hide().html('');
                    }
                }
                else {
                    if ( data == false )
                        console.log('false');
                    else if ( data == -1 )
                        console.log('-1: user does not own view');
                    else if ( data == -2 )
                        console.log('-2: view is selected as Default by admin');
                }
            });
        } catch (ex) {
            console.log(ex);
            notificationWidget.show("Un problème technique a empêché la suppression de vue","error");
        }
    }
    
    $('#manual_order_by').on('keyup', function(e){
        if(e.keyCode == 13)
        {
            var orderCol = parseInt($(this).val());
            if ( $.isNumeric( orderCol )  )
                $('#datatable').DataTable( )
                    .order([orderCol, 'asc'])
                    .draw();
        }
    });

    //Export button for exporting the list into file
    $('#export_pdf').click(function(e){
        e.preventDefault();
        $('.buttons-pdf').click();
    });
    $('#print_list').click(function(e){
        e.preventDefault();
        $('.buttons-print').click();
    });

    $('#export_xls').click(function(e){
        e.preventDefault();
        //$('.buttons-excel').click();
        var url = $('#datatable').DataTable().ajax.url();
        var data = $('#datatable').DataTable().ajax.params();
        data.export = true;

        $('#modal-generate-export').modal('show');

        exportXls(url, data, function(result) {
            if(result.url) {
                $('#modal-generate-export').modal('hide');
                window.open(result.url,'_blank');
            }
        });

    });
    //Eof Export button for exporting the list into file

    function exportXls(url, data, callback) {
        $.ajax({
            url : url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if(callback) {
                    callback(response);
                }
            }
        });
    }
</script>

<script type="text/javascript">
    /* event handler for retour la liste button */
    $(document).ready(function(){
        $(document).on('click',"#show-modal-retour",function (e) {
            e.preventDefault();
            $('#modal-retour-confirm').modal({
                show: true
            });
        });
        
        $('#enregister-retour').click(function(){
            $('form.modal-form.form-horizontal:first').submit();            
            $('.modal').modal('hide');
        });
        
        $('#annuler-retour').click(function(){
            $('.modal').modal('hide');
        });
        
        $('#close-retour').click(function(){
            $('#modal-retour-confirm').modal('hide');            
            $('body').removeAttr("class");
            setTimeout(function() {
                $('body').addClass("modal-open");
            },1000);
        });
    });
</script>

<?php
if(isset($mass_action_toolbar)) {
    if($mass_action_toolbar == true) {
        $this->load->view('templates/mass_action_toolbar_js.php');
    }
}
?>
