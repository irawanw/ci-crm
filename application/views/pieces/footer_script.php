<script>
// initialisations javascript


    // culture
    kendo.culture("fr-FR");

    // initialisation des tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // notifications
    var notificationElement = $("#notification");
    notificationElement.kendoNotification({
        position: {
            // notification popup will scroll together with the other content
            pinned: false,
            // the first notification popup will appear 30px from the viewport's top and right edge
            top: 60,
            right: 5
        },
        // new notifications will appear below old ones
        stacking: "down",
        // prevent accidental hiding for 1 second
        allowHideAfter: 1000
    });
    var notificationWidget = notificationElement.data("kendoNotification");
    <?php $alertes = array('success'=>'success','info'=>'info','warning'=>'warning','error'=>'danger');
    foreach ($alertes as $t=>$a) {
        $flash = $this->session->flashdata($a);
        if (isset($flash)) {?>
    notificationWidget.show("<?php echo $flash?>", "<?php echo $t?>");
    <?php }
    }?>

    // validations
    var validationElement = $("#validation_errors");
    validationElement.kendoNotification({
        position: {
            // notification popup will scroll together with the other content
            pinned: false,
            // the first notification popup will appear 30px from the viewport's top and right edge
            top: 60,
            left: 5
        },
        // new notifications will appear below old ones
        stacking: "down",
        // no automatic hiding
        autoHideAfter: 0,
        // prevent accidental hiding for 1 second
        allowHideAfter: 1000,
        // show a hide button
        button: true,
        // prevent hiding by clicking on the notification content
        hideOnClick: false
    });
    var validationWidget = validationElement.data("kendoNotification");
    <?php if (function_exists('validation_errors') AND validation_errors()!= '') {?>
    validationWidget.show("<?php echo str_replace("\n",'',validation_errors())?>", "error");
    <?php }?>

$(document).ready(function() {

    var etat_clignotement;
	

    // masquage du bandeau d'alerte sur clic
    $("#bandeau_alerte").click(function() {
        $(this).addClass('hidden');
        etat_clignotement = 9;
    });
    $("#ctc_type").change(function() {
		var ctype=$(this).val();
		ajaxctype(ctype);		
       // $(this).addClass('hidden');
       // etat_clignotement = 9;
    });
	
	function ajaxctype(id){
		 $("#type_selected").html('');
		//alert("<?php echo site_url("newvigik/ajaxctype")?>");
		$.ajax({
            method: "POST",
            url: "<?php echo site_url("newvigik/ajaxctype")?>",
			data:"id="+id,
            success: function (data) {
               $("#type_selected").html(data);
            },
            error: function () {
                notificationWidget.show("Erreur lors de la vérification des rappels", "error");
            }
        });
	}
	

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
function adrajaxclient(){
        var id= $("#adr_client").val();		
		 $("#type_data").html('');
		$.ajax({
            method: "POST",
            url: "<?php echo site_url("newadresse/ajaxclient")?>",
			data:"id="+id,
            success: function (data) {
               $("#type_data").html(data);
            },
            error: function () {
                notificationWidget.show("Erreur lors de la vérification des rappels", "error");
            }
        });
	}
function ajaxtournee(){
        var id= $("#tr_nom").val();		
		 $("#type_data").html('');
		//alert("<?php echo site_url("newvigik/ajaxctype")?>");
		$.ajax({
            method: "POST",
            url: "<?php echo site_url("newtournee_journalieres/ajaxtournee")?>",
			data:"id="+id,
            success: function (data) {
               $("#type_data").html(data);
            },
            error: function () {
                notificationWidget.show("Erreur lors de la vérification des rappels", "error");
            }
        });
	}	
function ajaxclient(){
        var id= $("#ctc_client").val();		
		 $("#type_data").html('');
		//alert("<?php echo site_url("newvigik/ajaxctype")?>");
		$.ajax({
            method: "POST",
            url: "<?php echo site_url("newvigik/ajaxclient")?>",
			data:"id="+id,
            success: function (data) {
               $("#type_data").html(data);
            },
            error: function () {
                notificationWidget.show("Erreur lors de la vérification des rappels", "error");
            }
        });
	}
	function ajaxlv_type(){
        var id= $("#lv_depot").val();
		
		 $("#type_depot").html('');
		//alert("<?php echo site_url("newvigik/ajaxctype")?>");
		$.ajax({
            method: "POST",
            url: "<?php echo site_url("livraisonsvigik/ajaxlv_vigik")?>",
			data:"id="+id,
            success: function (data) {
               $("#type_depot").html(data);
            },
            error: function () {
                notificationWidget.show("Erreur lors de la vérification des rappels", "error");
            }
        });
	}
(function($){
    $(document).ready(function(){
        $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
            event.preventDefault(); 
            event.stopPropagation(); 
            $(this).parent().siblings().removeClass('open');
            $(this).parent().toggleClass('open');
        });
    });
})(jQuery);
</script>
<?php
if(isset($controleur)){
 if($controleur=="newtournee_journalieres" && $methode=="listnew"){
?>
<script>
$('#datatable').on( 'click', 'tr', function () {
    var id = $('#datatable').DataTable().row( this ).id();
	$.ajax({
            method: "POST",
            url: "<?php echo site_url("newtournee_journalieres/sel_adresse")?>",
			data:"id="+id,
            success: function (data) {
               $("#modify_adresse").html(data);
            },
            error: function () {
                notificationWidget.show("Erreur lors de la vérification des rappels", "error");
            }
        });
    });
</script>
<?php 
}
}
?>