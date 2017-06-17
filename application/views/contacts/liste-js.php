<script>
    $(document).ready(function () {

        // Redéfinition de quand les boutons sont activés dans la barre action
        actionMenuBar.datatable.buttonStatus = function(id) {
            var data = actionMenuBar.datatable.data(id);
            var email = data.get('ctc_email');

            // Fonction de status de bouton
            return function(button) {
                switch (button.id) {
                    case 'email_type':
                        return (email != '');
                }
                // Tous les autres boutons sont activés par défaut
                return true;
            }
        };
    });
	$('#datatable tbody').on('click', 'tr', function(e) {
		var thisRow = DT.row( this );
		var id = thisRow.id();
		$('#template-modal-detail').attr("data-id", id);
	});
</script>