<?php $code_cat = 'E'?>
<!-- Initialisation de la grille -->
<script>
    var datasource_<?php echo $code_cat?> = new kendo.data.DataSource({
        transport: {
            read:  {
                url: "<?php echo site_url("devis/lecture_catalogue/$code_cat")?>",
                dataType: "json"
            }
        },
        pageSize: 10,
        schema: {
            model: {
                fields: {
                    art_id: {},
                    art_code: {},
                    art_description: {},
                    art_libelle: {},
					art_qty: {}
                },
                id: "art_id"
            }
        }
    });
</script>

<script>
    $(document).ready(function(){
        $("#grid_<?php echo $code_cat?>").kendoGrid({
            columns: [
                {
                    field: "art_code",
                    title: "Code",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "art_description",
                    title: "Description",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
				{
                    field: "art_qty",
                    title: "Quantity",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
				{
                    field: "art_message",
                    title: "Type Message",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
				{
                    field: "art_statistic",
                    title: "Statistics",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
				{
                    field: "art_type_sending",
                    title: "Type Sending",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                }
            ],
            dataSource: datasource_<?php echo $code_cat?>,
            filterable: {
                mode: "row"
            },
            // selection is custom made - see below
            selectable: false,
            scrollable: false,
            pageable: true
        });

        // custom made multiple select
        // manage disabled state
        $("#grid_E").delegate('tbody>tr', 'click', function(){
            $(this).toggleClass('k-state-selected');
            $("#form_E").prop('disabled', true) ;
            $("#grid_E  tbody>tr").each( function(idx, row) {
                if ($(row).hasClass('k-state-selected')) {
                    $("#form_E").prop('disabled', false) ;
                }
            })
        });


        $("#form_E").click(function(e){
            e.preventDefault ;
            var grid = $("#grid_E").data("kendoGrid");
            $("#grid_E  tbody>tr").each( function(idx, row) {
                if ($(row).hasClass('k-state-selected')) {
                    var data = grid.dataItem(row);
					
                    var article = {
                        id: data.art_id,
                        code: data.art_code,
                        description: data.art_libelle,
                        info: data.art_info,
                        prix: data.art_prix,
                        quantite: data.art_qty_insert
                    }
                    // superfragilistic
                    nouvel_article(article);
                    $(row).removeClass('k-state-selected') ;
                }
            })
            var win = $("#popup-E").data("kendoWindow");
            win.close() ;
        })

        $("#form_E_close").click(function(e){
            var win = $("#popup-E").data("kendoWindow");
            win.close() ;
        })

    });

	//this function to pull data from dimitrios list using ajax post request
	function comptage(){
		data 		= $("#filter :input")
						.filter(function(index, element) {
							return $(element).val() != "";
						})
						.serialize();		
		$('#count-result').html('Please wait filtering data in progress...');		
		$.ajax({
			type: "POST",
			url: '<?php echo site_url("devis/emailing_comptage"); ?>',
			data: data,
			success: function(result){
				redrawGrid(result);
			},
			dataType: 'json'
		});
	}
	
	function redrawGrid(data){
		var numbers = data.recordsFiltered.formatMoney(0, '.', ',');
		$('#count-result').html('<b>'+numbers+'</b> contact have been found!');					
		params	= JSON.stringify($("#filter :input")
						.filter(function(index, element) {
							return $(element).val() != "";
						})
						.serializeArray());
		//filtering the article as number result in email filtering
		datasource_E_filtered = new kendo.data.DataSource({
			transport: {
				read:  {
					url: "<?php echo site_url("devis/lecture_catalogue/$code_cat")?>?email_count="+data.recordsFiltered+'&filter_parameters='+params,
					dataType: "json"
				}
			},
			pageSize: 10,
			schema: {
				model: {
					fields: {
						art_id: {},
						art_code: {},
						art_description: {},
						art_libelle: {},
						art_qty: {}
					},
					id: "art_id"
				}
			}
		});
		
		//redraw the kendo grid with filtering data
		$("#grid_E").data("kendoGrid").setDataSource(datasource_E_filtered);
	}	
	
	Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
		c = isNaN(c = Math.abs(c)) ? 2 : c, 
		d = d == undefined ? "." : d, 
		t = t == undefined ? "," : t, 
		s = n < 0 ? "-" : "", 
		i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
		j = (j = i.length) > 3 ? j % 3 : 0;
	   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	 };	
</script>
