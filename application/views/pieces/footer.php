</div> <!-- container -->

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo base_url('assets/js/bootstrap.min.js')?>"></script>

<!-- Kendo UI combined JavaScript -->
<script src="<?php echo base_url('assets/js/jszip.min.js')?>"></script>
<script src="<?php echo base_url('assets/js/kendo.all.min.js')?>"></script>
<script src="<?php echo base_url('assets/js/kendo.messages.fr-FR.min.js')?>"></script>
<script src="<?php echo base_url('assets/js/kendo.culture.fr-FR.min.js')?>"></script>

<!-- Printing helper library -->
<script type="text/javascript" src="<?php echo base_url('assets/js/print.min.js')?>"></script>

<!-- Datetime picker jquery plugin -->
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.datetimepicker.full.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.periodpicker.full.min.js')?>"></script>
<!-- <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.timepicker.min.js')?>"></script> -->

<!-- Script de la barre d'actions -->
<?php include "application/views/templates/barre_action-js.php"?>
<?php include "application/views/pieces/modal-js.php"?>

<!-- Notifications, erreurs de validation et alertes-->
<div id="notification"></div>
<div id="validation_errors"></div>

<!-- Scripts des contrôles riches -->
<?php if (isset($scripts)) {
    foreach ($scripts as $s) {
        echo $s;
    }
}?>

<!-- Script de bas de page -->
<?php include "application/views/pieces/footer_script.php"?>

</body>
</html>
