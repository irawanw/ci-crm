<script>
	$(document).ready(function() {
		$('#feuille_de_route_creer').click(function(e) {
			e.preventDefault();
			window.location = '<?php echo site_url("feuilles_de_tri"); ?>';
		});  

		/** action remove data */
        $('#datatable tbody').on('click', 'tr', function(e) {
            var table = $('#datatable').DataTable();
            var data = table.row(this).data();

            $('#btn-remove').prop("href", "<?php echo site_url('feuille_de_route/remove');?>/" + data.RowID);

            $('#feuille_de_route_supprimer').removeClass("disabled");
        });

        $('#feuille_de_route_supprimer').click(function(e) {
            e.preventDefault();
            $('#modal-form-remove').modal('show');
        });       

	});

</script>
<?php $this->load->view('templates/remove_confirmation_js.php'); ?>