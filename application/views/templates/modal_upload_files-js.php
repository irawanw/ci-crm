<script type="text/javascript" src="<?php echo base_url('assets/js/dropzone.min.js') ?>"></script>
<script type="text/javascript">

    Dropzone.autoDiscover = false;

    var fileUpload= new Dropzone(".dropzone",{
        url: "<?php echo $url_upload;?>",
        maxFilesize: 2,
        method:"post",
        acceptedFiles:"*",
        paramName:"files",
        dictInvalidFileType:"",
        addRemoveLinks: true,
        init: function () {
            var dropzone = this;
            

            this.on("success", function(file, responseText) {               
                var response = JSON.parse(responseText);
                var id = response.id;
                               
                if(response.status == true) {                                     
                   file.id = id;
                }               
            });   

            this.on('reset', function() {
                if(this.files.length != 0){
                    for(i=0; i<this.files.length; i++){
                        this.files[i].previewElement.remove();
                    }
                    this.files.length = 0;
                }
            });        
        },
    });

    //Event upload
    fileUpload.on("sending",function(a,b,c){
        var id = $('#upload_id').val();
        c.append("upload_id", id);
    });

    fileUpload.on("removedfile",function(file){        
        var selectedId = file.id;                
        $.ajax({
            type:"get",                    
            url:"<?php echo site_url('files/remove') ?>/" + selectedId,
            cache:false,
            dataType: 'json',
            success: function(){

            },
            error: function(){


            }
        });
    });

   
    $(document).ready(function(e) {
        //action to open modal form upload file
        $('#datatable tbody').on('click', '.btn-upload-file', function(e) {
          e.preventDefault();
          var id = $(this).attr('data-id');
          $('#upload_id').val(id);
          $('#modal-form-upload').modal('show');          
        });

        $('body tbody').on('click', '.btn-delete-file', function(e) {   
            var fileId = $(this).attr('data-id');
            $('#file_id').val(fileId);         
            $('#modal-form-remove-upload-file').modal('show');
        });

        $('#btn-upload-ok').click(function(e) {
            e.preventDefault();            
            $('#modal-form-upload').modal('hide');
            var id = $('#upload_id').val();

            if(id) {
                var helper = actionMenuBar.datatable;
                helper.reload(id);
            }
            //location.reload();
        });

         $('#btn-remove-ok').click(function(e) {
            e.preventDefault();
            var fileId = $('#file_id').val();;

            if(fileId != "") {
                console.log(fileId);

                var urlRemove = '<?php echo site_url('files/remove');?>/' + fileId;
                $.get(urlRemove, function(e) {
                
                });
            }

            $('#modal-form-remove-upload-file').modal('hide');
            $('#datatable').DataTable().ajax.reload();
        });

        $('.clear-dropzone').click(function(e) {
            e.preventDefault();
            //location.reload();
        });
    });
</script>