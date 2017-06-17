<script>
$('#datatable tbody').on('click', 'tr', function(e) {
		var thisRow = DT.row( this );
		var id = thisRow.id();
		$('#template-modal-detail').attr("data-id", id);
	});
</script>