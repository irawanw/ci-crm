<!-- Custom modal confirm archive -->
<div id="modal-custom-archive" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation d'opération</h4>
      </div>        
      <div class="modal-body">
            Veuillez confirmer la archive du Global list
      </div>
     
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <a href="" class="btn btn-warning btn-custom-confirm-archive" role="button">Archiver</a>
    </div>
     
    </div>
  </div>
</div>
<!-- ./Custom modal confirm archive -->

<!-- Custom modal confirm remove -->
<div id="modal-custom-remove" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation d'opération</h4>
      </div>        
      <div class="modal-body">
            Veuillez confirmer la suppression du global list
      </div>
     
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <a href="" class="btn btn-danger btn-custom-confirm-remove" role="button">Supprimer</a>
    </div>
     
    </div>
  </div>
</div>
<!-- ./Custom modal confirm remove -->

<script>
	function getIndexColumn(column, lastcolumn){		
		result = [];
		for(i=0; i<column.length; i++){
			result[i] = global_column.indexOf(column[i]);
			if(lastcolumn == true){
				if( i == (column.length-1) ) {
					result[i] = (result[i]+1);
				}
			}
		}
		return result;
	}
		
	function getIndexDrawedColumn(column, lastcolumn){
		result = [];
		for(i=0; i<column.length; i++){
			result[i] = $('#data_table_columns_header td[data="'+column[i]+'"]').index();
			if(lastcolumn == true){
				if( i == (column.length-1) ) {
					result[i] = (result[i]+1);
				}
			}
		}
		return result
	}

    $(document).ready(function() {
        //array to determine width of each header
        //groupheader = [1, 5, 10, 15, 25, 35, 47] means
        //col 0-1 group A; col 2-5 group B; col 6-10 group C
        //etc...	
		groupheader = getIndexColumn(default_header, true);
		
        //view all button to list all result
        $('#voir_liste a').click(function(e) {
            e.preventDefault();
            console.log("show all");
            var isList = $(this).attr('data-list');
            var table = $('#datatable');
            var setting = table.DataTable().init();
            var textHtml = $(this).html();
            if(isList) {
                setting.iDisplayLength = 100;
                setting.sScrollY = 575;
                setting.scroller = {loadingIndicator: true};
                setting.bPaginate = true;
                table.DataTable().destroy();
                table.DataTable(setting);
                textHtml = textHtml.replace("Défaut la liste", "Voir la liste");
                $(this).html(textHtml);
                $(this).removeAttr("data-list");
                autowidthHeader(groupheader);
            } else {
                delete setting.scrollY;
                delete setting.scroller;
                setting.sScrollY = false;
                setting.iDisplayLength = -1;
                setting.bPaginate = false;
                table.DataTable().destroy();
                table.DataTable(setting);
                textHtml = textHtml.replace("Voir la liste", "Défaut la liste");
                $(this).html(textHtml);
                $(this).attr("data-list", true);
                autowidthHeader(groupheader);
            }
        });
       
        $('#view_commerciale').click(function(e){
            e.preventDefault();
            showColumn('view_commerciale');
        })
        $('#view_synthetique').click(function(e){
            e.preventDefault();
            showColumn('view_synthethique');
        })      
        $('#sel_view').change(function(e) {
            var view = $(this).val();
            window.location = view;
        });

        //action to open modal form upload file
        $('#datatable tbody').on('click', '.btn-upload-file', function(e) {
          e.preventDefault();
          var id = $(this).attr('data-id');
          $('#upload_id').val(id);
          $('#modal-form-upload').modal('show');
        });
        //action to open modal form view long text
        $('#datatable tbody').on('click', '.view-text', function(e) {
          e.preventDefault();
          var id = $(this).attr('data-id');
          var message = $(this).attr('data-message');
          $('#id').val(id);
          $('#message').val(message);
          $('#modal-form-view-text').modal('show');
        });

        // Redéfinition de la fonction qui génère les paramètres des boutons dans la barre action
        actionMenuBar.datatable.buttonParams = function(id, row) {
            // Fonction de paramètres de bouton
            return function(button) {
                switch (button.id) {
                    case 'global_list_modification':
                        return row.software;
                }
                // Tous les autres boutons prennent simplement l'id dans l'URL
                return id;
            }
        };
 
        //action to copy text on textarea form
        $('.btn-copy-text').click(function(){
                textarea = $('#message');
                textarea.select();
                document.execCommand('copy');
            });
			
        //mass action
        $("#btn_action_all").click(function(e) {
			var action = $("#sel_action_all" ).val();
			if(action == 'remove')
				$('#modal-mass-remove').modal('show');
			else
				$('#modal-mass-re-integrer').modal('show');		
        })
		
        //check/uncheck all boxes
        $("#datatable thead").on('click', '#check-all', function(e) {
            $("tbody input:checkbox").not(this).prop('checked', this.checked);
        });
        $('.filter_clear_all').on('click', function(event){
            $('.filter_clear').each(function(index){
                var filter_key = $(this).attr('id').replace('filter_clear_', '');
                clear_filter(filter_key, false);
            });
            $('#datatable').DataTable().ajax.reload();
            showAllColumn();
			window.groupheader = getIndexDrawedColumn(default_header, true);
			autowidthHeader(groupheader);
        });
		
        //add grouped header
        setTimeout(function() {
            groupedHeader();
			customHeaderCheckbox();
        }, 500);
		
        var old_init = dt_persist_state;
        dt_persist_state = function() {
            old_init.apply(this, arguments);
            autowidthHeader(groupheader);
        };
        var old_done_typing = doneTyping;
        doneTyping = function() {
            old_done_typing.apply(this, arguments);
            showColumnByFilterSoftware();
        }

        $('#global_list_nouveau').click(function(e){
            e.preventDefault();            
            $('#modal-form-emailing').attr('data-type', 'parent');
            $('#modal-form-emailing').modal('show');
        });

        /** Show Modal Archive Confirmation**/
        $("#global_list_archiver a").click(function(ev){
            ev.preventDefault();
            var href = $(this).attr('href');

            $('#modal-custom-archive').modal('show');
            $('.btn-custom-confirm-archive').attr('href', href);
        });

        /** Show Modal Remove Confirmation**/
        $("#global_list_supprimer a").click(function(ev){
            ev.preventDefault();
            var href = $(this).attr('href');

            $('#modal-custom-remove').modal('show');
            $('.btn-custom-confirm-remove').attr('href', href);
        });

        /** Custom action remove **/
        $(".btn-custom-confirm-remove").click(function(ev) {
            ev.preventDefault();
            var url = $(this).attr('href') + '/ajax';
            var helper = actionMenuBar.datatable;
            var parentId = $('#datatable').find('tr.selected').attr('data-parent');

            $.ajax({
                type: 'POST',
                url: url,
                data: {},
                dataType: 'json',
                success: function(response) {                    
                    if(response.success == true) {
                        var event = response.data.event;
                        var isChild = event.hasOwnProperty("isChild") ? true : false;

                        if(!isChild) {
                            helper.unload(event.id);
                        } else {
                            $('#datatable').find('#child-id-' + event.id).fadeOut();
                            helper.reload(parentId);
                        }
                    } 

                    notificationWidget.show(response.message, response.notif);
                    $('#modal-custom-remove').modal('hide');
                },
                error: function(err) {
                    notificationWidget.show("Request error", 'warning');
                }
            })
        });

        /** Custom action archive **/
        $(".btn-custom-confirm-archive").click(function(ev) {
            ev.preventDefault();
            var url = $(this).attr('href') + '/ajax';
            var helper = actionMenuBar.datatable;
            var parentId = $('#datatable').find('tr.selected').attr('data-parent');

            $.ajax({
                type: 'POST',
                url: url,
                data: {},
                dataType: 'json',
                success: function(response) {                    
                    if(response.success == true) {
                        var event = response.data.event;
                        var isChild = event.hasOwnProperty("isChild") ? true : false;

                        if(!isChild) {
                            helper.unload(event.id);
                        } else {
                            $('#datatable').find('#child-id-' + event.id).fadeOut();
                            helper.reload(parentId);
                        }
                    } 

                    notificationWidget.show(response.message, response.notif);
                    $('#modal-custom-archive').modal('hide');
                },
                error: function(err) {
                    notificationWidget.show("Request error", 'warning');
                }
            })
        });

        //add class danger for button remove
        $("#global_list_supprimer").addClass("action-confirm-danger");

        //action to view detaill child
        $('#datatable tbody').on('click', '.btn-view-detail', function(e) {
            e.preventDefault();
            var btnSelected = $(this);
            var isHide = btnSelected.attr('data-hide') == "1" ? true : false;
            var tr = $(this).closest('tr');
            var row = DT.row( tr );
            var rowId = tr.attr('id');
            var rowIdArr = rowId.split("_");
            var tr_id = rowIdArr[0];
            var softwareId = rowIdArr[1];

            if(isHide == false) {
                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    
                    //remove child row
                    $('.child-'+rowId).remove();
                }
                else {
                    // Open this row
                    //row.child( format(row.data()) ).show();
                    tr.addClass('shown');                               

                    //reinit click selected function
                    $('#datatable tr').click(function(){
                        $('#datatable tr').removeClass('selected');
                        $(this).addClass('selected');
                    })
                    
                    //get child data
                    $.post(SITE_URL+ '/global_list/index_child_json/0_' + softwareId, {'parentId':tr_id}, function(data){

                        //toggle button hide
                        btnSelected.attr('data-hide', 1);
                        btnSelected.text('hide detail');
                        
                        if($('.child-'+rowId).length>=1)
                            $('.child-'+rowId).remove();
                        
                        if(data.data.length > 0) {
                            $.each(data.data, function(index, value){
                                //we might change this later
                                new_html = '<tr id="child-id-'+value.id+'_'+softwareId+'" data-parent="'+rowId+'" class="child child-'+rowId+'">'+
                                                '<td colspan="22">Sub Sending '+(index+1)+'</td>'+
                                                '<td>'+value.date_envoi+'</td>'+
                                                '<td>'+value.segment_part+'</td>'+                
                                                '<td>'+value.stats+'</td>'+
                                                '<td>'+value.quantite_envoyee+'</td>'+
                                                '<td>'+value.open+'</td>'+
                                                '<td>'+value.open_pourcentage+'</td>'+
                                                '<td>'+value.openemm_number_of_click+'</td>'+
                                                '<td>'+value.openemm_click_rate_pct+'</td>'+
                                                '<td>'+value.verification_number+'</td>'+
                                                '<td>'+value.number_sent_through+'</td>'+
                                                '<td>'+value.number_sent_mail+'</td>'+
                                                '<td>'+value.deliv_sur_test_orange+'</td>'+
                                                '<td>'+value.deliv_sur_test_free+'</td>'+
                                                '<td>'+value.deliv_sur_test_sfr+'</td>'+
                                                '<td>'+value.deliv_sur_test_gmail+'</td>'+
                                                '<td>'+value.deliv_sur_test_microsoft+'</td>'+
                                                '<td>'+value.deliv_sur_test_yahoo+'</td>'+
                                                '<td>'+value.deliv_sur_test_ovh+'</td>'+
                                                '<td>'+value.deliv_sur_test_oneandone+'</td>'+
                                                '<td>'+value.deliv_reelle_bounce+'</td>'+
                                                '<td>'+value.deliv_reelle_bounce_percentage_pct+'</td>'+
                                                '<td>'+value.deliv_reelle_hard_bounce_rate_pct+'</td>'+
                                                '<td>'+value.deliv_reelle_soft_bounce_rate_pct+'</td>'+
                                                '<td>'+value.deliv_reelle_orange+'</td>'+
                                                '<td>'+value.deliv_reelle_free+'</td>'+
                                                '<td>'+value.deliv_reelle_sfr+'</td>'+
                                                '<td>'+value.deliv_reelle_gmail+'</td>'+
                                                '<td>'+value.deliv_reelle_microsoft+'</td>'+
                                                '<td>'+value.deliv_reelle_yahoo+'</td>'+
                                                '<td>'+value.deliv_reelle_ovh+'</td>'+
                                                '<td>'+value.deliv_reelle_oneandone+'</td>'+
                                                '<td>'+value.operateur_qui_envoie+'</td>'+
                                                '<td>'+value.number_sent+'</td>'+
                                                '<td>'+value.physical_server+'</td>'+
                                                '<td>'+value.provider+'</td>'+
                                                '<td>'+value.ip+'</td>'+
                                                '<td>'+value.smtp+'</td>'+
                                                '<td>'+value.rotation+'</td>'+
                                                '<td>'+value.domain+'</td>'+
                                                '<td>'+value.computer+'</td>'+
                                                '<td>'+value.manual_sender+'</td>'+
                                                '<td>'+value.manual_sender_domain+'</td>'+
                                                '<td>'+value.copy_mail+'</td>'+
                                                '<td>'+value.speed_hours+'</td>'+
                                                '<td>'+value.number_hours+'</td>'+               
                                            '</tr>';
                                
                                if($('.child-'+rowId).length>=1)
                                    $('.child-'+rowId).last().after(new_html);
                                else
                                    $(tr).after(new_html);
                                
                                if ($('.child-'+rowId).length == (index+1)) {
                                    $('#datatable').unbind();
                                    $('#datatable tr').click(function(){
                                        parent_id = $(this).attr('id');                             
                                        $('#datatable tr').removeClass('selected');
                                        $(this).addClass('selected');

                                        $('#global_list_supprimer').addClass("action-confirm-danger");                            
                                        //modify button modification & remove
                                        if($(this).hasClass('child')){                                  
                                            child_id = $(this).attr('id').split('-');
                                            child_id = child_id[2];

                                            $("#global_list_modification a").attr('href', 
                                                '<?php echo site_url(); ?>/global_list/modification_child/'+child_id);
                                            $("#global_list_archiver a").attr('href', 
                                                '<?php echo site_url(); ?>/global_list/archive_child/'+child_id);
                                            $("#global_list_supprimer a").attr('href', 
                                                '<?php echo site_url(); ?>/global_list/remove_child/'+child_id);
                                            
                                        } else {
                                            $("#global_list_modification a").attr('href', 
                                                '<?php echo site_url(); ?>/global_list/modification/'+parent_id);

                                            $("#global_list_supprimer a").attr('href', 
                                                '<?php echo site_url(); ?>/global_list/remove/'+parent_id);

                                            $("#global_list_archiver a").attr('href', 
                                                '<?php echo site_url(); ?>/global_list/archive/'+parent_id);
                                        }
                                    })
                                }
                            })
                        } else {
                            new_html = '<tr data-parent="'+rowId+'" class="child child-'+rowId+'"><td colspan="70">None</td></tr>';
                            $(tr).after(new_html);
                        }
                    });             
                }
            } else {
                //toggle button view
                btnSelected.removeAttr('data-hide');
                btnSelected.text('view detail');
                $('.child-'+rowId).remove();
            }
        });
    });
	
	function massAction(){
		var action = $("#sel_action_all" ).val();
		theid = [];
		theid['pages_jaunes'] = [];
		theid['manual_sending'] = [];
		theid['max_bulk'] = [];
		theid['openemm'] = [];
		$("tbody input:checkbox:checked").each(function(i){
			software = $(this).parent().next().text();
			if(software == 'pages jaunes')
				theid['pages_jaunes'][i] = $(this).val();
			if(software == 'manual sending')
				theid['manual_sending'][i] = $(this).val();				
			if(software == 'max bulk')
				theid['max_bulk'][i] = $(this).val();
			if(software == 'openemm')
				theid['openemm'][i] = $(this).val();
			
			$(this).parent().parent().fadeOut();
		});
		
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('pages_jaunes/mass_');?>" + action,
			data: {ids: JSON.stringify(theid['pages_jaunes'])},
		});
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('manual_sending/mass_');?>" + action,
			data: {ids: JSON.stringify(theid['manual_sending'])},
		});
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('max_bulk/mass_');?>" + action,
			data: {ids: JSON.stringify(theid['max_bulk'])},
		});
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('openemm/mass_');?>" + action,
			data: {ids: JSON.stringify(theid['openemm'])},
		});			
	}
	
    function showColumnByFilterSoftware() {
        var filterSoftware = $('#filter_input_software').val();
        //console.log(filterSoftware);
        //showAllColumn();
        if(filterSoftware.length > 2) {
            var MANUAL_SENDING_TEXT = "manual sending";
            var MAX_BULK_TEXT = "max bulk";
            var PAGES_JAUNES_TEXT = "pages jaunes";
            var OPENEMM_TEXT = "openemm";
            if(MANUAL_SENDING_TEXT.search(filterSoftware) > -1) {
                console.log("manual_sending filtered")
                showColumn(MANUAL_SENDING_TEXT);
            } else if(MAX_BULK_TEXT.search(filterSoftware) > -1) {
                console.log("max_bulk filtered")
                showColumn(MAX_BULK_TEXT);                
            } else if(PAGES_JAUNES_TEXT.search(filterSoftware) > -1) {
                console.log("pages_jaunes filtered")
                showColumn(PAGES_JAUNES_TEXT);                
            } else if(OPENEMM_TEXT.search(filterSoftware) > -1) {
                console.log("openemm filtered")
                showColumn(OPENEMM_TEXT);
            } else {
                //showAllColumn();
            }
        } else {
            //showAllColumn();
        }
    }
	
    function showColumn(software) {							
        var table = $('#datatable').DataTable();
        var columnHidden = {
			"manual sending": getIndexColumn(hide_manual_sending, false),
			"max bulk": getIndexColumn(hide_max_bulk, false),
			"openemm": getIndexColumn(hide_openemm, false),
			"pages jaunes": getIndexColumn(hide_pages_jaunes, false),
			"view_commerciale": getIndexColumn(hide_commerciale, false),
			"view_synthethique": getIndexColumn(hide_synthethique, false),
        };
		showAllColumn2();
        table.columns(columnHidden[software]).visible(false, false);
        table.columns.adjust().draw();
		
		if(software == "manual sending"){			
			window.groupheader = getIndexDrawedColumn(header_manual_sending, true);
		}
		if(software == "max bulk"){			
			hideGroupHeader(["h-manual"]);
			window.groupheader = getIndexDrawedColumn(header_max_bulk, true);
		}
		if(software == "openemm"){			
			window.groupheader = getIndexDrawedColumn(header_openemm, true);
		}
		if(software == "pages jaunes"){			
			window.groupheader = getIndexDrawedColumn(header_pages_jaunes, true);
			hideGroupHeader(["h-segment", "h-delivreelle","h-delivtest","h-manual"]);
		}		
		if(software == "view_synthethique"){
			hideGroupHeader(["h-corps","h-delivtest","h-technical"]);			
			window.groupheader = getIndexDrawedColumn(header_synthethique, true);
		}
		if(software == "view_commerciale"){
			hideGroupHeader(["h-delivtest","h-delivreelle","h-technical","h-manual"]);
			window.groupheader = getIndexDrawedColumn(header_commerciale, true);
		}
		
		//setTimeout(function() {
			dt_initwidth(dt_autowidth());			
			autowidthHeader(groupheader);
		//}, 100);		
    }
    function showAllColumn() {
        var table = $('#datatable').DataTable();
        table.columns().visible(true, false);
        table.columns.adjust().draw();
        $('#parentHeader').remove();
        groupedHeader();
    }
    function showAllColumn2() {
        var table = $('#datatable').DataTable();
        table.columns().visible(true, false);
        $('#parentHeader').remove();
        groupedHeader();
    }   
    /**
     * Generate input type checkbox in header column checkbox ids
     * @return {[type]} [description]
     */
    function customHeaderCheckbox() {
        var table = $('#datatable').DataTable();
        var headerCheckbox = table.column(0).header();
        $(headerCheckbox).html("<input type='checkbox' id='check-all' />");
    }
    //grouped header to be 3 groups
    function groupedHeader(){
        //grouped header
        var myElem = document.getElementById('parentHeader');
        var table = $('#datatable').DataTable();
        var header = table.table().header();
        if (myElem == null){
            $("#datatable_wrapper").before("<div id='parentHeader' class='group-header' style='clear: both'>"
			+"<div id='h-checkbox' style='background-color: #4c7036;'>&nbsp;</div>"
            +"<div id='h-parametres' style='background-color: #cdda01;'>Parametres</div>"
            +"<div id='h-info' style='background-color: #d67676;'>Info Facturation</div>"
            +"<div id='h-message' style='background-color: #89d676;'>Message</div>"
            +"<div id='h-corps' style='background-color: #76c9d6;'>Corps du Message</div>"
            +"<div id='h-segment' style='background-color: #ba6e0b;'>Segment</div>"
            +"<div id='h-envoi' style='background-color: #c376d6;'>Envoi</div>"
            +"<div id='h-delivtest' style='background-color: #7682d6;'>Delivrabilite Sur Test</div>"
            +"<div id='h-delivreelle' style='background-color: #656c77;'>Delivrabilite Reelle</div>"
            +"<div id='h-technical' style='background-color: #c327b0;'>Technical</div>"
            +"</div>")
        }
    }   
    function autowidthHeader(groupheader){
        var filterSoftware = $('#filter_input_software_name').val().trim();
        var exceptColumns = [];
        if(filterSoftware.length > 2) {
            var MANUAL_SENDING_TEXT = "manual sending";
            var MAX_BULK_TEXT = "max bulk";
            var PAGES_JAUNES_TEXT = "pages jaunes";
            var OPENEMM_TEXT = "openemm";
            if(MANUAL_SENDING_TEXT.search(filterSoftware) > -1) {
				groupheader = getIndexDrawedColumn(header_manual_sending, true);
            } else if(MAX_BULK_TEXT.search(filterSoftware) > -1) {				
				groupheader = getIndexDrawedColumn(header_max_bulk, true);
            } else if(PAGES_JAUNES_TEXT.search(filterSoftware) > -1) {
				header_pages_jaunes = [
							'software',
							'client',
							'message_id',
							'message_famille',
							'date_envoi',
							'operateur_qui_envoie',
							'copy_mail'
						];
				groupheader = getIndexDrawedColumn(header_pages_jaunes, true);
            } else if(OPENEMM_TEXT.search(filterSoftware) > -1) {	
				groupheader = getIndexDrawedColumn(header_openemm, true);
            } else {
				groupheader = getIndexDrawedColumn(default_header, true);
            }
        }
        for(z=0;z<=groupheader.length;z++){
            colwidth = 0;
            if(z == 0)
                startcolumn = 0;
            else
                startcolumn = groupheader[z-1]+1;
            endcolumn = groupheader[z];
            //console.log(startcolumn+' to '+endcolumn);
            border_width = 0;
            for(i=startcolumn;i<=endcolumn;i++){
                dt_width = $('#datatable tr:first-child td:nth-child('+i+')').width();
                colwidth = colwidth + dt_width;
                border_width++;
            }
            adjuster = 1;
            if(z==0)
                adjuster = 2;
            $('.group-header div:nth-child('+(z+1)+')').width(colwidth+border_width-adjuster);
        }
    }
    function hideGroupHeader(headers){
        for(var i=0; i < headers.length; i ++) {
            $('#' + headers[i]).remove();
        }
    }   
</script>
<style>
    .group-header{
        width: 10000px;
    }
    .group-header div{
        color: white; 
        text-align: center; 
        height: 20px;
        font-weight: bold;
        float: left;
        overflow: hidden;
        font-size: 11px;
        line-height: 18px;
        border-right: 1px solid #ccc;
    }
</style>