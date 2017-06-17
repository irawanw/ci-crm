<script>
// initialisations javascript


    // initialisation des tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // notifications
    var notificationElement = $("#notification");
  
    var notificationWidget = notificationElement.data("kendoNotification");
    <?php $alertes = array('success'=>'success','info'=>'info','warning'=>'warning','error'=>'danger');
    foreach ($alertes as $t=>$a) {
        $flash = $this->session->flashdata($a);
        if (isset($flash)) {?>
    notificationWidget.show("<?php echo $flash?>", "<?php echo $t?>");
    <?php }
    }?>


$(document).ready(function() {
    var etat_clignotement;

    // masquage du bandeau d'alerte sur clic
    $("#bandeau_alerte").click(function() {
        $(this).addClass('hidden');
        etat_clignotement = 9;
    });

    // fonction de vérification des rappels
    var verif = function () {
        $.ajax({
            method: "POST",
            url: "<?php echo site_url("alertes/verification")?>",
            success: function (reponse) {
                if (reponse != false) {
                    var texte = '';
                    for(var i=0;i<reponse.length;i++) {
                        var data = reponse[i].tac_description.split('|');
                        if (data.length < 3) return;
                        texte = texte + data[2] + ' : ' + reponse[i].tac_info + '<br />';
                        if (data[0] == 'D' && typeof mise_evidence_devis == 'function') {
                            mise_evidence_devis(data[1]);
                        }
                        else if(data[0] == 'F' && typeof mise_evidence_facture == 'function') {
                            mise_evidence_facture(data[1]);
                        }
                    }
                    $("#texte_alerte").html(texte);
                    $("#bandeau_alerte").removeClass('hidden');
                    var audio = new Audio("<?php echo base_url('assets/sounds/13835.mp3')?>");
                    audio.play();
                    etat_clignotement = 1;
                    setTimeout(clignotement, 1000);
                }
            },
            error: function () {
                notificationWidget.show("Erreur lors de la vérification des rappels", "error");
            }
        });
        //setTimeout(verif, 5000);
    };

    // fonction de clignotement
    var clignotement = function () {
        if (etat_clignotement == 0) {
            $(texte_alerte).removeClass('invisible');
            etat_clignotement = 1;
            setTimeout(clignotement, 1000);
        }
        else if (etat_clignotement == 1) {
            $(texte_alerte).addClass('invisible');
            etat_clignotement = 0;
            setTimeout(clignotement, 200);
        }
    }

    verif();

});
</script>