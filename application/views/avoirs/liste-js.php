<script>

    function prepareRowCustomStyling(row, row_data) {
        var etat = parseInt(row_data.get('avr_etat'), 10);

        if (etat > 2) {
            $(row.node()).addClass('avoir-etat-utilise');
        }
    }

    $(document).ready(function() {

        // Redefines when the buttons are enabled in the toolbar
        actionMenuBar.datatable.buttonStatus = function(id) {
            var data = actionMenuBar.datatable.data(id);
            var etat = parseInt(data.get('avr_etat'), 10);

            // Fonction de status de bouton
            return function(button) {
                switch (button.id) {
                    case 'avoir_valider':
                        return (etat < 2);

                    case 'avoir_supprimer':
                        return (etat < 2);

                    case 'avoir_envoyer_email':
                        return (etat > 1);
                }
                return true;
            }
        }

        // Handles the "Nouveau" button that should not get a row ID
        // appended to its URL.
        actionMenuBar.datatable.buttonParams = function(id) {
            // Fonction de paramètre de bouton
            return function(button) {
                switch (button.id) {
                    case 'avoir_nouveau':
                        return 0;
                }
                return id;
            }
        }

        // Adds the CSS class "avoir-etat-utilise" for avoir with state "fully used"
        // that are inserted asynchronously to the datatable upon actions.
        actionMenuBar.datatable.on('loaded', function(ev) {
            var data = actionMenuBar.datatable.data(ev.id);
            var etat = parseInt(data.get('avr_etat'), 10);
            if (etat > 2) {
                actionMenuBar.datatable.$row(ev.id).addClass('avoir-etat-utilise');
            }
        });

    });

</script>