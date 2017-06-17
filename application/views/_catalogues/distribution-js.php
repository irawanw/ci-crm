<?php
$controleur  = "devis";
$descripteur = array(
    'datasource'         => 'articles_distributions/catalogues_distribution',
    'champs'             => array(       
        array('art_code', 'text', "Code", 'art_code'),
        array('art_description', 'text', "Description", 'art_description'),
        array('art_habitat', 'text', "Habitat", 'art_habitat'),
        array('art_document', 'text', "Type Document", 'art_document'),
        array('art_distribution', 'text', "Type Distribution", 'art_distribution'),
        array('art_delai', 'text', "Delai", 'art_delai'),
        array('art_controle', 'text', "Controle", 'art_controle'),
        array('art_prix', 'text', "Prix Unitaire", 'art_prix'),
        array('art_prix_total', 'text', "Prix Total", 'art_prix_total'),
    ),
    'filterable_columns' => array(
        'art_code' => 'char',
        'art_description' => 'char',
        'art_habitat' => 'char',
        'art_document' => 'char',
        'art_distribution' => 'char',
        'art_delai' => 'char',
        'art_controle' => 'char',
        'art_prix' => 'double',
        'art_prix_total' => 'double',
    ),
);
?>
<style>
    .dt-buttons{
        display: none;
    }
</style>


<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/cr-1.3.2/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/scroller/1.4.2/js/dataTables.scroller.min.js"></script>

<script type="text/javascript" src="https://cdn.datatables.net/select/1.2.1/js/dataTables.select.min.js"></script>
<!-- Datatables Print -->
<script type="text/javascript" src="<?php echo base_url();?>assets/js/colResizable-1.6.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-ui.min.js"></script>


<!-- Datetime picker jquery plugin -->
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.datetimepicker.full.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.periodpicker.full.min.js"></script>
<!-- Initialisation des listes -->
<script>
    var datasource_Dv = new kendo.data.DataSource({
        transport: {
            read:  {
                url: "<?php echo site_url("devis/lecture_catalogue/Dv")?>",
                dataType: "json"
            }
        },
        schema: {
            model: {
                fields: {
                    vil_id: {},
                    vil_nom: {}
                }
            }
        }
    });
    var datasource_Ds = new kendo.data.DataSource({
        transport: {
            read:  {
                url: "<?php echo site_url("devis/lecture_catalogue/Ds")?>",
                dataType: "json"                
            }
        },
        schema: {
            model: {
                fields: {
                    sec_id: {},
                    sec_ville: {},
                    sec_nom: {},
                    sec_type: {}
                }
            }
        }        
    });
</script>

<script>
    var quantitesData = null;
    function calculateQuantite() {
        var secteurs = datasource_Ds.view();
        $ville_entiere = $('input[name="form_D_integral"]').prop('checked');
        var habitatArr = ["HLM","RES","PAV"]; 
        var quantiteArr = {};

        console.log(habitatArr);

        $.each(habitatArr, function(index, item){
            var bal = item;
            if ($ville_entiere) {                   
                var quantite = [0,0,0,0,0,0,0,0];
                for (var i=0;i<secteurs.length;i++) {
                    var habitat = secteurs[i].sec_type - 1;
                    if (bal == 'HLM') {
                        quantite[habitat] += secteurs[i].sec_hlm - secteurs[i].sec_hlm_stop;
                    }
                    else if (bal == 'RES') {
                        quantite[habitat] += secteurs[i].sec_res - secteurs[i].sec_res_stop;
                    }
                    else {
                        quantite[habitat] += secteurs[i].sec_pav - secteurs[i].sec_pav_stop;
                    }
                }

                // calcul du prix                
                var quantite_totale = 0;
                for (var i=0;i<8;i++) {
                    if (quantite[i] > 0) {                                                    
                        quantite_totale += quantite[i];
                    }
                }

                quantiteArr[bal] = quantite_totale;
            }
            else {               
                var quantite = 0;
                $('input[name="form_D_secteur[]"]:checked').each(function() {
                    var id_secteur = $(this).attr('data-id-secteur');
                    var secteur = secteurs[parseInt(id_secteur)];                   

                    if (bal == 'HLM') {
                        quantite += secteur.sec_hlm - secteur.sec_hlm_stop;
                    }
                    else if (bal == 'RES') {
                        quantite += secteur.sec_res - secteur.sec_res_stop;
                    }
                    else {
                        quantite += secteur.sec_pav - secteur.sec_pav_stop;
                    }
                   
                    //console.log(secteur.sec_id + " " + bal + " :" + quantite)                    
                });

                quantiteArr[bal] = quantite;
            }
        });

        quantitesData = JSON.stringify(quantiteArr);
        doneTyping();
    }

    $(document).ready(function() {

        // conditions initiales
        $('#form_D_div_ville').hide();
        $('#form_D_div_secteurs').hide();
        var ville_select = 0;

        var html_secteur = $('#form_D_div_secteurs').html();
        
        $("#form_D_villes").kendoDropDownList({
            index: 0,
            minLength: 2,
            filter:"contains",
            dataTextField: "vil_nom",
            dataValueField: "vil_id",
            dataSource: datasource_Dv,
            optionLabel: "Sélectionnez une ville ..."
        });
        $("#form_D_secteurs").kendoDropDownList({
            index: 0,
            cascadeFrom: "form_D_villes",
            cascadeFromField: "sec_ville",
            dataTextField: "sec_nom",
            dataValueField: "sec_id",
            dataSource: datasource_Ds,
            cascade: function() {
                var ville = $("#form_D_villes").data("kendoDropDownList").dataItem();
                if (ville.vil_id != ville_select) {
                    ville_select = ville.vil_id;
                    datasource_Ds.filter({field: "sec_ville", operator: "eq", value: ville.vil_id});
                    var secteurs = datasource_Ds.view();
                    if (secteurs.length == 0) {
                        $('#form_D_div_ville').hide();
                        $('#form_D_div_secteurs').hide();
                    }
                    else if (secteurs.length == 1) {

                        // La ville compte un seul secteur
                        $('input[name="form_D_integral"]').prop('checked', true);
                        $('input[name="form_D_integral"]').prop('disabled',true);
                        $('#form_D_div_ville').show();
                        $('#form_D_div_secteurs').hide();
                    }
                    else if (secteurs.length > 1) {
                        $('input[name="form_D_integral"]').prop('checked', true);
                        $('input[name="form_D_integral"]').prop('disabled',false);

                        // remplissage des cases à cocher par secteur
                        $('#form_D_div_secteurs').empty();
                        for (var i=0;i<secteurs.length;i++) {
                            $('#form_D_div_secteurs').append(html_secteur);
                            $('#form_D_div_secteurs input:last').attr('data-id-secteur',i);
                            $('#form_D_div_secteurs input:last').after(secteurs[i].sec_nom)
                        }
                        $('input[name="form_D_secteur[]"]').prop('checked', false);
                        $('#form_D_div_ville').show();
                        $('#form_D_div_secteurs').hide();
                    }
                }
            }
        });

        // animation des checkbox
        $('input[name="form_D_integral"]').click(function() {
            var sens = $(this).prop('checked');
            if (sens == true) {
                $('#form_D_div_secteurs').hide();
            }
            else {
                $('input[name="form_D_secteur[]"]').prop('checked', true);
                $('#form_D_div_secteurs').show();
            }            
            calculateQuantite();
        });

        $(document).on("click", '.chk-secteur', function (e) {
            $('input[name="form_D_integral"]').prop('checked', false);
            
            calculateQuantite();
        });

       // fermeture de la fenêtre
        $('#form_D_close').click(function() {
            $("#form_D_villes").data("kendoDropDownList").value(-1);
            var win = $("#popup-D").data("kendoWindow");
            win.close();
        });        
    });
</script>
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
        $('div.dataTables_scrollHeadInner').find('table').width($('#datatable.JColResizer').width());
        dt_persist_state();
        dt_reapply_column_widths(); // to sync headers
        reWidthInputFields();  
        dt_persist_state();
        $('.JCLRgrip').height($('div.dataTables_scrollBody').find('div').last().height());
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
            $('#datatable').DataTable().ajax.reload();
        }

        // Reinitialise resizability.
        setTimeout( function () {
            dt_init_resizable();
            dt_persist_state();
        }, 200 );

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
        //$('#datatable_loading').append('<span>&nbsp;&nbsp;|&nbsp;&nbsp;Loading scripts...</span>')
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

            d['quantites'] = quantitesData;
        };

        DT = $('#datatable').DataTable( {
            language: {
                url: '<?php echo base_url('assets/js/French.json')?>'
            },
            autoWidth: true,
            serverSide: true,
            ordering: false,
            searching: false,
            scrollY: "480px",            
            scrollCollapse: true,
            scroller: {
                loadingIndicator: true,
                trace: true
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
                    //$('#datatable_loading').append('<span>&nbsp;got '+(data.responseJSON.data.length)+' items</span><span>&nbsp;&nbsp;|&nbsp;&nbsp;Building table...</span>')
                    var right_now = new Date();
                    $('#err_msg').html("Ajax call successful. :-) Got records "+data.responseJSON.recordsOffset+" to "+(parseInt(data.responseJSON.recordsOffset)+parseInt(data.responseJSON.recordsLimit)-1)+ " @ "+right_now.today() + " " + right_now.timeNow());
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
                    
                    //$('#datatable_loading').append('<span>&nbsp;done.</span>');
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
            },
            initComplete: function(settings) {
            },
            fnCreatedRow: function( nRow, aData, iDataIndex ) {
            },
            fnDrawCallback: function( settings ) {
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

        $('#datatable tbody').on('click', 'tr', function(e) {           
            var objVille = $("#form_D_villes").data("kendoDropDownList").dataItem();

            if(objVille.vil_id != "") {
                $('#form_D').removeAttr("disabled");
            } else {
                $('#form_D').attr("disabled", true);
            }
            
        });

        function filterDataBySecteurType(e) {
            var secteurs = datasource_Ds.view();
            var sec_type = secteurs.length == 0 ? "" : secteurs[0].sec_type;
            var art_code_filter = sec_type != "" ? "DS" + sec_type : "";
            
            $('#filter_type_art_code').val("cont");
            $('#filter_input_art_code').val(art_code_filter);    
            
            setTimeout(function() {
                calculateQuantite();
            }, 1000);
            //doneTyping();
        }

        datasource_Ds.bind("change", filterDataBySecteurType);

        // récupération des articles
        $('#form_D').click(function() {
            var habitatArr = [];
            var table = $('#datatable').DataTable();
            var rowSelected = table.row({selected: true}).data();

            var ville = $("#form_D_villes").data("kendoDropDownList").dataItem();
            $ville_entiere = $('input[name="form_D_integral"]').prop('checked');

            //adding type distribution & type document for instance "code" example "DSF (Distribution with type distribution 'Solo' & type document 'Flyer')"
            var typeDistribution = rowSelected.art_distribution;
            typeDistribution = typeDistribution.charAt(0);
            var typeDocument = rowSelected.art_document;
            typeDocument = typeDocument.charAt(0);          
            var article_id = rowSelected.art_id; 
            var code = 'D' + typeDistribution + typeDocument;
            var prix = rowSelected.art_prix;
            var habitat = rowSelected.art_habitat;

            if(habitat == 'TOUT') {
                habitatArr.push('HLM','RES','PAV');
            } else {
                var habitatString = habitat.split("+");

                for(var i=0; i< habitatString.length; i++) {
                  var valHabitat = habitatString[i];                  
                  habitatArr.push(valHabitat.trim());
                }
                
            }

        
            var secteurs = datasource_Ds.view();

            //console.log(habitatArr.length)

            // itération sur les 3 types de BAL
            $.each(habitatArr, function(index, item){

                // récupération des infos de la BAL
                var bal = item;
                var data = {
                    id: article_id,
                    code: code,
                    description: "Distribution " + bal,
                    info: bal + ville.vil_id,
                };

                if ($ville_entiere) {
                    // cas de la ville entière
                    data.description += " " + ville.vil_nom;
                    data.info += ":0";

                    // calcul de la somme des BAL
                    var quantite = [0,0,0,0,0,0,0,0];
                    for (var i=0;i<secteurs.length;i++) {
                        var habitat = secteurs[i].sec_type - 1;
                        if (bal == 'HLM') {
                            quantite[habitat] += secteurs[i].sec_hlm - secteurs[i].sec_hlm_stop;
                        }
                        else if (bal == 'RES') {
                            quantite[habitat] += secteurs[i].sec_res - secteurs[i].sec_res_stop;
                        }
                        else {
                            quantite[habitat] += secteurs[i].sec_pav - secteurs[i].sec_pav_stop;
                        }
                    }

                    // calcul du prix
                    var prix_total = 0;
                    var quantite_totale = 0;
                    for (var i=0;i<8;i++) {
                        if (quantite[i] > 0) {                            
                            prix_total += quantite[i] * prix;
                            quantite_totale += quantite[i];
                        }
                    }
                    if (quantite_totale > 0) {
                        data.prix = prix_total / quantite_totale;
                        data.quantite = quantite_totale;

                        //console.log(data);
                        nouvel_article(data);
                    }
                }
                else {
                    // cas d'une liste de secteurs
                    var description = data.description;
                    var info = data.info;
                    $('input[name="form_D_secteur[]"]:checked').each(function() {
                        var id_secteur = $(this).attr('data-id-secteur');
                        var secteur = secteurs[parseInt(id_secteur)];
                        data.description = description + " " + secteur.sec_nom;
                        data.info = info + ":" + secteur.sec_id;

                        var quantite = 0;
                        if (bal == 'HLM') {
                            quantite = secteur.sec_hlm - secteur.sec_hlm_stop;
                        }
                        else if (bal == 'RES') {
                            quantite = secteur.sec_res - secteur.sec_res_stop;
                        }
                        else {
                            quantite = secteur.sec_pav - secteur.sec_pav_stop;
                        }
                        if (quantite > 0) {                            
                            data.prix = prix;
                            data.quantite = quantite;
                            //console.log(data);
                            nouvel_article(data);
                        }
                    })
                }
            });
        });

        $("#popup-D").kendoWindow({            
            open: function(e) {
               var table = $('#datatable').DataTable();

               setTimeout(function() {
                    table.ajax.reload();
               },500);
            },
            close:function(e){
                $("#form_D_villes").data("kendoDropDownList").value(-1);
                $("#filter_input_art_code").val("");
                $("#filter_type_art_code").val("");
            }
        })

        $(window).bind('resizeEnd', function() {
            //console.log("resizeEnd state..")
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
        if ( e.keyCode!=13 && $(this).val()!='' &&  $(this).val() == filter_val_cache) return false; //If 
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
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
    });    
    $('.datepicker_mask, .datetimepicker_mask').on('change', function () {
        var new_value = $(this).periodpicker('valueStringStrong');
        var ref_input = $(this).closest('input.filter_input');
        ref_input.val( new_value );
        ref_input.trigger('click');
        doneTyping();
    });    

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
            var orderCol = parseInt($(this).val());
            if ( $.isNumeric( orderCol )  )
                $('#datatable').DataTable( )
                    .order([orderCol, 'asc'])
                    .draw();
        }
    });
  
</script>

