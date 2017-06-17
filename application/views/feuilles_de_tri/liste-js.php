<script>
    $(document).ready(function() {			
        /** action remove data */
        $('#datatable tbody').on('click', 'tr', function(e) {
            var table = $('#datatable').DataTable();
            var data = table.row(this).data();

            $('#btn-remove').prop("href", "<?php echo site_url('feuilles_de_tri/remove');?>/" + data.RowID);

            $('#feuilles_de_tri_supprimer').removeClass("disabled");
        });

        $('#feuilles_de_tri_supprimer').click(function(e) {
            e.preventDefault();
            $('#modal-form-remove').modal('show');
        });       
    });
</script>
<?php $this->load->view('templates/remove_confirmation_js.php'); ?>