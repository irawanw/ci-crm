<?php
	if(!isset($mass_action_toolbar)) 	$mass_action_toolbar 	= '';
	if(!isset($mass_action_checkbox)) 	$mass_action_checkbox 	= '';	
	if(!isset($recherche_toolbar)) 		$recherche_toolbar 		= true;
	if(!isset($view_toolbar)) 			$view_toolbar 			= '';	
	if(!isset($custom_toolbar)) 		$custom_toolbar 		= '';
	if(!isset($external_toolbar)) 		$external_toolbar 		= '';
?>
<style>
	textarea.message{
		width: 95%;
		height: 350px;
		margin-bottom: 5px;
		padding: 10px;
	}
</style>
<!-- toolbar de la grille -->
<script id="li_toolbar" type="text/x-kendo-template">
    <form class="form-inline text-left">
	<?php
	//toolbar for mass action
	if($mass_action_toolbar){
	?>
	    <div class="form-group">
            <label for="action">Actions de masse</label>
            <select class="form-control input-sm" id="sel_action_all">
                <option value="archiver">Archiver</option>
                <option value="remove">Supprimer</option>
            </select>
			<button type="button" class="btn btn-default btn-xs" id="btn_action_all">Ok</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
	<?php } ?>

	<?php if($recherche_toolbar){ ?>
        <div class="form-group">
            <label for="recherche">Recherche</label>
            <input type="search" class="form-control input-sm" id="recherche" placeholder="Chercher..." data-bind="events: {keypress: searchGrid}">
        </div>
	<?php } ?>

		<?php
		//toolbar for different view mode
		if($view_toolbar){
		?>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<div class="form-group">
            <label for="action">Vue</label>
            <select class="form-control input-sm" id="sel_view">
				<option value="">[Select]</option>
				<option <?php if(uri_string()==$controleur) echo "selected"; ?>
					value="<?php echo site_url($controleur); ?>">En cours</option>
                <option <?php if(uri_string()==$controleur.'/archiver') echo "selected"; ?>
					value="<?php echo site_url($controleur.'/archiver'); ?>">Archivées</option>
                <option <?php if(uri_string()==$controleur.'/supprimees') echo "selected"; ?>
					value="<?php echo site_url($controleur.'/supprimees'); ?>">Supprimées</option>
				<option <?php if(uri_string()==$controleur.'/all') echo "selected"; ?>
					value="<?php echo site_url($controleur.'/all'); ?>">Tout</option>
            </select>
        </div>
		<?php } ?>

		<?php
		if($custom_toolbar){
			echo $custom_toolbar;			
		} 
		
		//load external toolbar view
		if($external_toolbar){
			$this->view($controleur.'/'.$external_toolbar, $external_toolbar_data);
		}
		?>

    </form>
</script>

<!-- Initialisation de la grille -->
<script>
    var datasource = new kendo.data.DataSource({
        transport: {
            read:  {
                url: "<?php echo site_url($descripteur['datasource']."_json/$id")?>",
                dataType: "json"
            }
        },
        pageSize: 10,
        schema: {
            model: {
                fields: {
                    actions: {}<?php
$id = '';
$sep = ",\n";
foreach($descripteur['champs'] as $c) {
    if (substr($c[0],0,1) == '_') continue;
    switch ($c[1]) {
        case 'id':
            $id = $c[0];
            $format = 'type: "number"';
            break;
        case 'date':
        case 'datetime':
            $format = 'type: "date"';
            break;
        case 'number':
            $format = 'type: "number"';
            break;
        default:
            $format = '';
    }
    echo $sep;
?>
                    <?php echo $c[0]?>: {<?php echo $format?>}<?php }
?>

                },
                id: "<?php echo $id?>"
            }
        },


	<?php
		//aggregate specific column
		if(isset($aggregate)){
	?>
		aggregate: 	[
						{
							field: "<?php echo $aggregate['field']; ?>",
							aggregate: "<?php echo $aggregate['mode']; ?>"
						}
					]
	<?php } ?>

    });
    var ListValuesServiceBaseUrl = "<?php echo site_url("listes_valeurs/get")?>";

<?php foreach($descripteur['champs'] as $c) {
    if (substr($c[0],0,1) == '_') continue;
    if ($c[1] == 'ref' AND substr($c[3],0,2) == 'v_') {?>
    var <?php echo $c[0]?>Datasource = new kendo.data.DataSource({
        transport: {
            read:  {
                url: ListValuesServiceBaseUrl + '/<?php echo $c[3]?>',
                dataType: "json"
            }
        },
        schema: {
            model: {
                fields: {
                    id: {},
                    valeur: {}
                },
            }
        }
    });

<?php }
}?>
</script>

<script>
    $(document).ready(function(){

        // Dans la grille de données :
        // Pour les liens vers des pages detail
        $("#grid").on('click', "a.view-detail, a.open-form", function(ev){
            ev.preventDefault();
            actionMenuBar.loadInModal(this, '#template-modal-detail');
        });

        $("#grid").kendoGrid({
			toolbar: kendo.template($("#li_toolbar").html()),
			columns: [
				<?php if($mass_action_toolbar || $mass_action_checkbox){ ?>
                {
                    headerTemplate: '<input type="checkbox" id="check-all" />',
					template: '<input type="checkbox" class="checkbox" name="ids[]" value="#: <?=$descripteur['detail'][1]?> #"/>',
                    filterable: false,
                    title: "(sélecteur)"
                },
				<?php } ?>
<?php
$sep = '';
$filter = '';
foreach($descripteur['champs'] as $c) {
    if (substr($c[0],0,1) == '_') continue;
    $template = '';
    switch ($c[1]) {
        case 'date':
            $format = 'format: "{0: dd/MM/yyyy}",';
            break;
        case 'datetime':
            $format = 'format: "{0: dd/MM/yyyy HH:mm}",';
            break;
        default:
            $format = '';
    }

    echo $sep;
    $sep = ",\n";
    $encodage = false;
    if ($c[1] == 'ref') {
        if (count($c) == 6) {
            $template = '#if ('.$c[4].' == 0) {# &nbsp; #} else {# <a href="'.site_url($c[3]).'/detail/#: '.$c[4].' #" class="view-detail">#: '.$c[5].' #</a>#}#';
        }
    }

	//if type of data is PICTURE then show this template
	//when empty it can upload directy to certain data id
	//when there is value then just link it to the data
	if ($c[1] == 'picture') {
        if (count($c) == 7) {
			$filter = 'filterable: false,';
            $template = '#if ('.$c[3].' == 0) {# <a href="javascript:void(0)" onclick="'.$c[6].'(#:'.$c[5].'#)">Telecharger</a> #} else {# <a target="_blank" href="'.base_url().'fichiers/'.$c[4].'/#:'.$c[3].'#">#: '.$c[3].' # </a>#}#';
        }
    }

	//if type of data is TEXTAREA then show this template
	//the data will placed in a hidden div
	//it will pop up when "voir message" is clicked
	if ($c[1] == 'textarea') {
		$filter = 'filterable: false,';
		$template = '<div class="textarea-popup-#:'.$c[3].'#"><form action="'.site_url($controleur).'/update_value" method="post"><a href="javascript:void(0)" class="copy-btn btn btn-default">Copy</a><br><br><input name="id" value="#:'.$c[3].'#" type="hidden"><textarea name="'.$c[0].'" class="message">#:'.$c[4].'#</textarea><br><input type="submit" value="Update" class="btn btn-primary">&nbsp;&nbsp;&nbsp;<a href="javascript:closeTextarea(#:'.$c[3].'#)" class="btn btn-danger">Annuler</a></form></div><a href="javascript:showTextarea(#:'.$c[3].'#)">'.$c[2].'</a>';
	}

    if ($c[1] == 'fichier') {
        $encodage = true;
    }?>
                {
                    field: "<?php echo $c[0]?>",
                    title: "<?php echo $c[2]?>",

			<?php
				//aggregate spesific column
				if (isset($aggregate) && $c[0] == $aggregate['field']) {
					echo 'footerTemplate: "Total : #='.$aggregate['mode'].'#",';
				}
			?>

<?php if($filter != ''){
					echo $filter;
} ?>

<?php if ($template != '') {?>
                    template : '<?php echo $template?>',
<?php }?>

<?php if (isset($descripteur['detail'])) {
    $detail = $descripteur['detail'];
    if (count($detail == 3) AND $c[0] == $detail[2]) {?>
                    template: '#if (<?php echo $detail[1]?> == 0) {# &nbsp; #} else {# <a href="<?php echo site_url($detail[0])?>/#: <?php echo $detail[1]?> #" class="view-detail">#: <?php echo $detail[2]?> # </a>#}#',
<?php }
} ?>

<?php
		//this part for drawing archive button
		if (isset($descripteur['archive'])) {
			$detail = $descripteur['archive'];
			if (count($detail == 3) AND $c[0] == $detail[2]) { ?>
				template: '#if (<?php echo $detail[1]?> == 0) {# &nbsp; #} else {# <a style="font-size: 11px; padding: 3px" class="btn btn-danger" onclick="return confirm(\'Êtes-vous sûr de vouloir archiver ces données?\')" href="<?php echo site_url($detail[0])?>/#: <?php echo $detail[1]?>#">archiver</a>#}#',
<?php } } ?>

<?php if ($format != '') echo "                    $format\n"
?>
<?php if ($c[1] == 'ref') {
        if (substr($c[3],0,2) == 'v_') {?>
                    filterable: {cell: {template: <?php echo $c[0]?>_filtre, showOperators: false}}<?php
        }
        else {?>
                    filterable: { cell: { operator: "contains" } }<?php
        }
    } elseif($filter !=''){
					echo $filter;
	}
	else {?>
                    filterable: { cell: { operator: "contains" } }<?php
    }

    if ($encodage) {?>,
                    encoded: false
<?php }
else {?>

<?php   }?>
                }<?php
}?>

            ],
            dataSource: datasource,
            scrollable: false,
            pageable: true,
            sortable: true,
            filterable: {
                mode: "row"
            },
            selectable: true,
            reorderable: true,
            resizable: true,
            pdf: {
                allPages: true,
                fileName: "<?php echo "$controleur-$methode"?>.pdf"
            },
            excel: {
                allPages: true,
                fileName: "<?php echo "$controleur-$methode"?>.xlsx"
            },
			dataBound: function(e) {
				afterLoaded();
			<?php
            if (isset($descripteur['en_avant'])){?>

                var grid = this;
                grid.tbody.find('>tr').each(function() {
                    var dataItem = grid.dataItem(this);
                    <?php foreach ($descripteur['en_avant'] as $en_avant) {?>
                    if(dataItem.<?php echo $en_avant[0]?>) {
                        $(this).attr("style","<?php echo $en_avant[1]?>");
                    }
                    <?php }?>
                });
            <?php }?>
			}
        });
        var grid = $("#grid").data("kendoGrid");

        // recherche intégrale
        $("#recherche").on("keyup change paste", searchGrid);
        function searchGrid(){
            var searchValue = $.trim($(this).val());
            grid.dataSource.query({
                page:1,
                pageSize:10,
                filter:{
                    logic:"or",
                    filters:[
<?php $sep = '';
foreach($descripteur['champs'] as $c) {
    if (substr($c[0],0,1) == '_') continue;
    switch ($c[1]) {
        case 'date':
        case 'datetime':
        case 'id':
        case 'number':
            break;
        default:
        echo $sep;
        $sep = ",\n";?>
                        {
                            field: "<?php echo $c[0]?>",
                            operator: "contains",
                            value: searchValue
                        }<?php
        }
}?>
                    ]
                }
            });
            $(".k-filter-row .k-input").val('');
        }


    <?php foreach($descripteur['champs'] as $c) {
    if (substr($c[0],0,1) == '_') continue;
    if ($c[1] == 'ref' AND substr($c[3],0,2) == 'v_') {?>
        function <?php echo $c[0]?>_filtre(args) {
            args.element.kendoDropDownList({
                dataSource: <?php echo $c[0]?>Datasource,
                dataTextField: "id",
                change: function(e){},
                valuePrimitive: true,
                dataValueField: "valeur",
                optionLabel: "(sélectionner)"
            });
        };
<?php }
}?>

        $("#exportExcel").click(function(e) {
            grid.saveAsExcel();
        });
        $("#exportPdf").click(function(e) {
            grid.saveAsPDF();
        });

        // réglage de l'ordre et de la visibilité des colonnes
        $("#rule_list").click(function(e) {
            e.preventDefault();
            var colonnes = grid.columns;
            var html = '';
            for (var i=0;i<colonnes.length;i++) {
                var checked = ' checked';
                if (colonnes[i].hidden == true) {
                    checked = '';
                }
                html += '<li><div class="checkbox"><label><input type="checkbox" value="'+i+'"'+checked+'>'+colonnes[i].title+'</label></div></li>';
            }
            $("#liste_colonnes").html(html);
            $("#liste_colonnes").kendoSortable({
                ignore: "input",
                cursor: "move",
                hint: function(element) {
                    return $("<span></span>")
                        .text(element.text());
                }
            });
            $('#popup_reglage').modal('show');
        });
        $("#toutes_colonnes").click(function(e) {
            var etat = $(this).prop('checked');
            var champs = $("#liste_colonnes").data("kendoSortable");
            var items = champs.items();
            for (var i=0;i<items.length;i++) {
                var item = items[i].children[0].children[0].children[0];
                item.checked=etat;
            }
        });
        $("#popup_reglage_sauver").click(function(e) {
            var colonnes = grid.columns;
            var champs = $("#liste_colonnes").data("kendoSortable");
            var items = champs.items();
            for (var i=0;i<items.length;i++) {
                var item = items[i].children[0].children[0].children[0];
                var rang = parseInt(item.value);
                if (item.checked) {
                    grid.showColumn(rang);
                }
                else {
                    grid.hideColumn(rang);
                }
            }
            for (var i=0;i<items.length;i++) {
                var item = items[i].children[0].children[0].children[0];
                var rang = parseInt(item.value);
                if (rang > i) {
                    grid.reorderColumn(i, colonnes[rang]);
                }
            }
        });

        // sauvegarde de la vue courante
        $("#save_list").click(function(e) {
            e.preventDefault();
            var vue = prompt("Nom de la vue", "ma vue");
            var controleur = '<?php echo $controleur?>';
            var data = kendo.stringify(grid.getOptions());
            $.post( "<?php echo site_url("vues/nouvelle")?>", {vue:vue, data:data, ctrl:controleur}, function( data ) {
                if (data) {
                    $('#liste_vues').append('<li><a href="#'+data+'">'+vue+'</a></li>');
                    notificationWidget.show("La vue a été sauvegardée","success");
                }
                else {
                    notificationWidget.show("Un problème technique a empéché la sauvegarde de la vue","error");
                }
            });
        });

        // liste des vues enregistrées
        $("#liste_vues").click(function(e) {
            e.preventDefault();
            var hash = e.target.hash;
            var id = hash.substr(1);
            $.post( "<?php echo site_url("vues/reglages")?>", {id_vue:id}, function( data ) {
                if (data) {
                    grid.setOptions(JSON.parse(data));
                }
            });
        });

		//mass action
		$("#btn_action_all").click(function(e) {
			var action = $("#sel_action_all" ).val();
			theid = {};
			$("tbody[role='rowgroup'] input:checkbox:checked").each(function(i){
				theid[i] = $(this).val();
				$(this).parent().parent().fadeOut();
			});
			$.ajax({
				type: "POST",
				url: "<?php echo site_url($controleur); ?>/mass_"+action,
				data: {ids: JSON.stringify(theid)},
			});
		})

		//check/uncheck all boxes
		$("#check-all").click(function(e) {
            $("tbody[role='rowgroup'] input:checkbox").not(this).prop('checked', this.checked);
        });

		//change list view
		$('#sel_view').change(function(){
			window.location = ($('#sel_view option:selected').val());
		})

<?php if ($toolbar != '') {
    $this->load->view($toolbar.'_toolbar2');
} ?>
    });
</script>

<?php
/************************************
*	Sage Bar Action Script Section
*************************************/
?>
<script>
    $(document).ready(function() {
		$(".export-file-excel, .export-file-pdf").kendoWindow({
			modal: true,
			visible: false
		});
		$(".export-file-excel").data("kendoWindow")
			.title("Export liste to xlsx");

		$(".export-file-pdf").data("kendoWindow")
			.title("Export liste to pdf");

        // gestion de la barre d'actions

        function demasque_js(action, script) {
            var id_action = '#' + action;
            $(id_action + ' a').attr('href', 'javascript:'+script);
            $(id_action).removeClass('disabled');
        }

        // sélection d'un contact
        function row_select() {
            var grid = $("#grid").data("kendoGrid");
            var rows = grid.select();
            var record = grid.dataItem(rows[0]);
            <?php if (isset($champ_id) && is_string($champ_id)) {?>
            var id = record.<?php echo $champ_id; ?>;
            <?php } else { ?>
            var id = record.<?php echo $controleur; ?>_id;
            <?php }?>

            // démasquage des boutons de la barre action
            //actionMenuBar.enable('#<?php echo $controleur; ?>_detail', id);
            //actionMenuBar.enable('#<?php echo $controleur; ?>_modification', id);
            //actionMenuBar.enable('#<?php echo $controleur; ?>_supprimer', id);
            actionMenuBar.enable('.action-bar .action-modify, .action-bar .action-confirm-modify, .action-bar .action-confirm', id);
        }

        // liaison avec la grille
        var grid = $("#grid").data("kendoGrid");
        grid.bind("change", row_select);

		//view all data in one page
		$('#<?php echo $controleur; ?>_voir_liste').click(function(e){
			e.preventDefault();
			viewAll();
		})

		//exporting to xls and set the filename
		$('#export_xls').click(function(e){
			e.preventDefault();
			$(".export-file-excel").data("kendoWindow").open().center();
		})

		//exporting to pdf and set the filename
		$('#export_pdf').click(function(e){
			e.preventDefault();
			$(".export-file-pdf").data("kendoWindow").open().center();
		})
    });

	//this function will called after dataSource is finish loaded
	function afterLoaded(){
		//give text color if value Pas de Commande
		$('td[role="gridcell"]:contains("Pas de Commande")').css('color', 'red');

		//kendo Window for textarea type data
		if($("div[class^=textarea-popup]").length){
			$("div[class^=textarea-popup]").kendoWindow({
				visible: false,
				modal: true,
				width: "75%",
				height: "75%"
			});
		}

		$('.copy-btn').click(function(){
			textarea = $(this).parent().find('textarea');
			textarea.select();
			document.execCommand('copy');
		})
	}

	//show Kendo Window textarea
	function showTextarea(id){
		$(".textarea-popup-"+id).data("kendoWindow").open().center();
	}
	function closeTextarea(id){
		$(".textarea-popup-"+id).data("kendoWindow").close();
	}

	var view_all_toggle = 0;
	function viewAll() {
		if(view_all_toggle == 0){
			$("#grid").data("kendoGrid").dataSource.pageSize(1000);
			$('#<?php echo $controleur; ?>_voir_liste span:nth-child(2)').html('<br>Vue par Page');
			view_all_toggle = 1;
		}
		else{
			$("#grid").data("kendoGrid").dataSource.pageSize(10);
			$('#<?php echo $controleur; ?>_voir_liste span:nth-child(2)').html('<br>Voir la Liste');
			view_all_toggle = 0;
		}
	}

	function saveAsExcel(filename){
		var grid = $("#grid").data("kendoGrid");
		grid.options.excel.fileName = filename;  //set filename
		grid.saveAsExcel();
		$(".export-file-excel").data("kendoWindow").close();
	}

	function saveAsPDF(filename){
		//show all data in one page
		if(view_all_toggle == 0)
			viewAll()
		var grid = $("#grid").data("kendoGrid");
		grid.options.pdf.fileName = filename; //set filename
		grid.saveAsPDF();
		$(".export-file-pdf").data("kendoWindow").close();
	}
</script>
<div class="export-file-excel" style="display: none">
	<input 	class="form-control input-sm"
			style="width: 200px" name="filename" id="filename-excel" id="filename"
			value="<?php echo $controleur; ?>-<?php echo date('Y-m-d'); ?>.xlsx"><br>
	<button class="btn btn-primary" onclick="saveAsExcel($('#filename-excel').val())" value="Export">Export</button>
</div>
<div class="export-file-pdf" style="display: none">
	<input 	class="form-control input-sm"
			style="width: 200px" name="filename" id="filename-pdf" id="filename"
			value="<?php echo $controleur; ?>-<?php echo date('Y-m-d'); ?>.pdf"><br>
	<button class="btn btn-primary" onclick="saveAsPDF($('#filename-pdf').val())" value="Export">Export</button>
</div>
<?php
/****************************************
*	End of Sage Bar Action Script Section
*****************************************/
?>
