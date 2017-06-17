<script>	
    $(document).ready(function() {
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
            }
        });
		

        $('#sel_view').change(function(e) {
            var view = $(this).val();
            window.location = view;
        });        

		$('#template-modal-detail').on('shown.bs.modal', function (e) {						
			
			var formDropzone = new Dropzone("#template-modal-detail .dropzone",{
				url: "<?php echo site_url('factures_compta/do_upload_multiple'); ?>",      
				method:"post",
				acceptedFiles:"*",
				paramName:"files",
				dictInvalidFileType:"",
				addRemoveLinks:true,        
				init: function() {
					var fieldName = "facture";					
					this.on("success", function(file, responseText) {        		
						var response = JSON.parse(responseText);
						var id = response.id;    
						var files = $("#" + fieldName).val();

						if(response.status == true) {
							if(files != "") {
								files += "," + id;
							} else {
								files += id;
							}                    

							$('.dz-preview:last-child a').attr('data-id', id);
						}

						$("#" + fieldName).val(files);
					});

					this.on("removedfile", function(file) {                
						var files = $("#" + fieldName).val();
						var btn = file._removeLink;
						var selectedId = $(btn).attr('data-id');

						var urlRemove = '<?php echo site_url('files/remove');?>/' + selectedId;
						$.get(urlRemove, function(e) {
							var filesArr = files.split(","); 
							
							if(filesArr.indexOf(selectedId.toString()) > -1) {
							  var index = filesArr.indexOf(selectedId.toString());            
							  filesArr.splice(index, 1);
							}

							var newFiles = filesArr.join();
							$('#' + fieldName).val(newFiles);
						});               
					});
				}
			});			
		})
    });
</script>
<?php $this->load->view('templates/remove_confirmation_js.php'); ?>
<?php $this->load->view('templates/modal_upload_files-js.php', array('url_upload' => $url_upload)); ?>