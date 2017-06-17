</div> <!-- container -->
<script type="text/javascript" src="<?php echo base_url('')?>min/?g=jquery-js,datatable-js,buttons-js"></script>
<!-- Datatables -->
<!--
<script type="text/javascript" src="<?php echo base_url('')?>min/?g=datatable-js"></script>
<script type="text/javascript" src="<?php echo base_url('')?>min/?g=buttons-js"></script>-->
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
<?php include "application/views/global_list/footer_script.php";?>
</body>
</html>