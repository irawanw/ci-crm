<script>
$(document).ready(function () {
    // Passage en AJAX des actions qui affichent des informations dans la modale
    $(".action-bar .action-view a").click(function(ev){
        ev.preventDefault() ;
        actionMenuBar.view(this, '#template-modal-detail');
    })

    // Pour les barre d'actions qui apparaissent dans la modale,
    // passage en AJAX des actions qui affichent elles-mêmes des informations dans la modale
    $("#template-modal-detail").on('click', ".action-bar .action-view a", function(ev){
        ev.preventDefault() ;
        actionMenuBar.view(this, '#template-modal-detail');
    })

    // Pour les barre d'actions qui apparaissent dans la modale,
    // passage en AJAX des actions qui lancent un processus
    $("#template-modal-detail").on('click', ".action-bar .action-launch-process a", function(ev){
        ev.preventDefault();
        actionMenuBar.action(this);
    })

    // Pour les barre d'actions qui apparaissent dans la modale,
    // passage en AJAX des actions qui demandent un téléchargement
    $("#template-modal-detail").on('click', ".action-bar .action-download a", function(ev){
        ev.preventDefault();
        actionMenuBar.download(this);
    })

    // Actions that need to submit as POST
    $("#template-modal-detail").on('click', ".action-bar .action-method-post a", function(ev) {
        ev.preventDefault();
        actionMenuBar.post(this);
    })

    // Pour les barre d'actions qui apparaissent dans la modale,
    // passage en AJAX des actions qui demandent une impression de document PDF
    $("#template-modal-detail").on('click', ".action-bar .action-print-pdf a", function(ev){
        ev.preventDefault();
        actionMenuBar.print(this, 'pdf');
    })

    // Pour les barre d'actions qui apparaissent dans la modale,
    // passage en AJAX des actions  qui ont besoin d'une confirmation
    // et qui lancent un processus
    $("#template-modal-detail").on('click', ".action-bar .action-confirm-launch-process a", function(ev){
        ev.preventDefault();
        actionMenuBar.confirmAction(this);
    })

    // Pour les barre d'actions qui apparaissent dans la modale,
    // passage en AJAX des actions qui modifient les informations sur la ligne
    // (c.à.d des actions qui nécessitent que la page soit mise à jour)
    $("#template-modal-detail").on('click', ".action-bar .action-modify a", function(ev){
        ev.preventDefault();
        actionMenuBar.modify(this);
    })

    // Pour les barre d'actions qui apparaissent dans la modale,
    // passage en AJAX des actions qui ont besoin d'une confirmation
    // et qui modifient les informations dans la liste
    // (c.à.d des actions qui nécessitent que la page soit mise à jour)
    $("#template-modal-detail").on('click', ".action-bar .action-confirm-modify a", function(ev){
        ev.preventDefault();
        actionMenuBar.confirmModify(this);
    })

    // Pour les barre d'actions qui apparaissent dans la modale,
    // passage en AJAX des actions qui ont besoin d'une confirmation
    // et qui suppriment les informations dans la liste
    // (c.à.d des actions qui nécessitent que la page soit mise à jour)
    $("#template-modal-detail").on('click', ".action-bar .action-confirm-delete a", function(ev){
        ev.preventDefault();
        actionMenuBar.confirmModify(this, function() {
            actionMenuBar.hideModal("#template-modal-detail");
        });
    })

    // Dans la fenêtre modale :
    // Pour les liens vers des pages detail
    $("#template-modal-detail").on('click', "a.view-detail, a.open-form", function(ev){
        ev.preventDefault();
        actionMenuBar.loadInModal(this, '#template-modal-detail');
    })

    // Fermeture de la consultation
    $("#template-modal-detail").on("click", "#btn-return-detail-form",  function(ev) {
        ev.preventDefault();
        actionMenuBar.hideModal("#template-modal-detail");
    })

    // Passage en AJAX du submit de formulaire
    $("#template-modal-detail").on("submit", "form.modal-form",  function(ev) {
        ev.preventDefault() ;
        var url = $(this).attr("action");
        var data = $(this).serialize();
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: "json"
        }).done(
            actionMenuBar.displayNotification,
            actionMenuBar.hideModalOnSuccess("#template-modal-detail"),
            actionMenuBar.updateOnSuccess,
            actionMenuBar.broadcastOnSuccess(),
            actionMenuBar.redirectOnSuccess("_blank")
        );
    })

    // Initialisation des champs date de formulaire dans la fenêtre modale
    $("#template-modal-detail").on("focus", "input.form-date-field",  function() {
        $(this).datetimepicker({
            format:'d/m/Y',
            formatDate:'d/m/Y',
            timepicker: false,
            todayButton: true,
            allowBlank: !$(this).attr('required')
        });
    })

})

</script>