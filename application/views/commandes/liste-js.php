<script>
    $(document).ready(function(){

        // Redefines when the buttons are enabled in the toolbar
        actionMenuBar.datatable.buttonStatus = function(id) {
            var data = actionMenuBar.datatable.data(id);
            var etat = parseInt(data.get('cmd_etat'), 10);

            // Fonction de status de bouton
            return function(button) {
                switch (button.id) {
                    case 'commandes_lancer':
                        return (etat == 1);

                    case 'commandes_facturer':
                        return (etat < 5);

                    case 'commandes_annuler':
                        return (etat < 4);

                    case 'op_commande':
                        return (etat > 1);
                }
                return true;
            }
        }

    });
</script>