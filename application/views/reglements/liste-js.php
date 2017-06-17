<script>

    function apply_default_ordering() {
        console.log('apply_default_ordering()');
        DT.order([ [2, "desc"], [1, "asc"]]);
    }

    var _masseActionsPrepared =  0 ; //false;
                                     // 0:false; -1:in progress, 1:done
    function prepareMasseActions() {
        if ( _masseActionsPrepared != 0 /* != false*/) return;
        _masseActionsPrepared = -1;  //true; // -1 = in progress

        DT.columns().every(function(colIdx){
            $(DT.column( colIdx ).header()).find('.select_mask').each(function(){
                //console.log("SELECT: ",$(DT.column( colIdx ).header()).attr('data'), this);
                var cur_select = this;
                $(cur_select).find('option').remove();
                $(cur_select).append('<option value="">(sélectionner)</option>');
            <?php   foreach($descripteur['select_types'] as $t) { ?>
                    $(cur_select).append($('<option>').text('<?php echo $t->vtr_type; ?>'));
            <?php   } ?>
            });
        });
        _masseActionsPrepared = 1; // 1 = completed
    };

    function prepareRowCustomControls(row) {
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
    
    $(document).ready(function(){

        // Redéfinition de la fonction qui génère les paramètres des boutons dans la barre action
        // C'est nécessaire pour les nouveaux règlements pour sélectionner le même contact par défaut
        actionMenuBar.datatable.buttonParams = function(id) {
            var data = actionMenuBar.datatable.data(id);
            var contact_id = data.get('ctc_id');

            // Fonction de paramètres de bouton
            return function(button) {
                switch (button.id) {
                    case 'reglements_nouveau':
                        return contact_id;
                }
                // Tous les autres boutons prennent simplement l'id dans l'URL
                return id;
            }
        };

    });
</script>