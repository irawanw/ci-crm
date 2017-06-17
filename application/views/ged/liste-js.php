<?php 
$this->load->view('templates/remove_confirmation_js.php');
$this->load->view('templates/remove_confirmation.php'); 

?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.fancybox.pack.js"></script>
<script>
	$(document).ready(function() {

		$('#datatable tbody').on('click', 'tr', function(e) {
            var table = $('#datatable').DataTable();
            var data = table.row(this).data();

            var link_url = data.link.replace(/files\//,'');
            var link_preview = link_url.replace(/\s/g,'%20');
            var name_preview = data.name.replace(/\s/g, '%20');

            $('#btn-remove').prop("href", "<?php echo site_url('ged/supprimer');?>/"+data.storage+"/?path=" + link_preview);
            $('#ged_download > a').prop("href", "<?php echo site_url('ged/download');?>/"+data.storage+"/?path=" + link_url);

            $('#ged_preview > a').prop("href", "<?php echo site_url('ged/get_preview');?>/"+data.storage+"/?path=" + link_preview+"&format="+data.format_file+"&name="+ name_preview);
            $('#ged_preview > a').attr("data-fancybox-type", "ajax");
            $('#ged_preview > a').attr("class", "preview-image");

            $('#ged_supprimer').removeClass("disabled");
            $('#ged_preview').removeClass("disabled");
            $('#ged_download').removeClass("disabled");

           /* console.log('data.link= '+data.link);
            console.log('link_url= '+link_url);
            console.log('link_preview= '+link_preview);*/
        });
        $('#ged_supprimer').click(function(e) {
            e.preventDefault();
            $('#modal-form-remove').modal('show');
        });

        $('#filter-document-type').change(function(e) {
            var documentType = $(this).val();
            if(documentType != "") {
                window.location = "<?php echo site_url('ged/document');?>/" + documentType;
            } else {
                window.location = "<?php echo site_url('ged');?>";
            }
        });

        $(".preview-image").fancybox({
            openEffect: 'fade'
        });
	});
</script>