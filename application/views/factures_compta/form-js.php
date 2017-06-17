<script type="text/javascript">
    var fieldName = "<?php echo $field_name;?>"; 
    $('#template-modal-detail').on('shown.bs.modal', function (e) {
        $('.files').remove();

        $('#' + fieldName).before('<div class="files"></div>');
        var id = $(this).attr('data-id');
        $.get("<?php echo site_url('factures_compta/get_facture_files/');?>" + '/' + id, function(data){
             $('.files').html(data);
        })

    	//action to upload multiples file
    	Dropzone.autoDiscover = false;

        var fileUpload= new Dropzone(".dropzone",{
            url: "<?php echo $url_upload;?>",      
            method:"post",
            acceptedFiles:"*",
            paramName:"files",
            dictInvalidFileType:"",
            addRemoveLinks:true,        
            init: function() {
            	var fieldName = "<?php echo $field_name;?>";            

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
    });

	$(document).ready(function(e) {
		$('#template-modal-detail').on("click","#btn-remove-file-ok", function(e) {
            e.preventDefault();
            var fileId = $(this).attr('data-id');

            if(fileId != "") {
                var urlRemove = "<?php echo site_url('files/remove');?>/" + fileId;
                $.get(urlRemove, function(e) {
                    $('#file-container-' + fileId).remove();
                    $('#confirm-remove-file').hide();
                });
            }
        });
	});	

    function showConfirmRemoveFile(fileId) {
        if(fileId != "") {
            console.log(fileId);
            $('#btn-remove-file-ok').attr('data-id', fileId);
            $('#confirm-remove-file').show();
        }
    }

    function hideConfirmRemoveFile() {
        $('#confirm-remove-file').hide();
    }
</script>