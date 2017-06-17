        </div> <!-- corps -->
    </div> <!-- menu latéral -->
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

<!-- Notifications et erreurs de validation-->
<div id="notification"></div>
<div id="validation_errors"></div>

<!-- Script de bas de page -->
<?php include "application/views/pieces/footer_script.php"?>

<!-- Scripts des contrôles riches -->
<?php if (isset($scripts)) {
    foreach ($scripts as $s) {
        echo $s;
    }
}?>

</body>
</html>
