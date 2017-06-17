</div> <!-- container -->

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo base_url('assets/js/bootstrap.min.js')?>"></script>
<script src="<?php echo base_url('assets/js/bootstrap-multiselect.js')?>"></script>

<!-- Kendo UI combined JavaScript -->
<script src="<?php echo base_url('assets/js/jszip.min.js')?>"></script>
<!-- <script src="<?php echo base_url('assets/js/kendo.all.min.js')?>"></script> -->
<script src="<?php echo base_url('assets/js/kendo.core.min.js')?>"></script>
<script src="<?php echo base_url('assets/js/kendo.popup.min.js')?>"></script>
<script src="<?php echo base_url('assets/js/kendo.notification.min.js')?>"></script>
<script src="<?php echo base_url('assets/js/kendo.window.min.js')?>"></script>
<script src="<?php echo base_url('assets/js/kendo.messages.fr-FR.min.js')?>"></script>
<script src="<?php echo base_url('assets/js/kendo.culture.fr-FR.min.js')?>"></script>
<!-- rem to avoid conflict with DataTables UI plugin
--> 

<!-- Datatables -->
<!-- <script src="//cdn.datatables.net/1.10.11/js/jquery.dataTables.js" type="text/javascript" charset="utf8" ></script> 
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/cr-1.3.2/datatables.min.js"></script>
<!-- DEGUB VERSION <script src="<?php echo base_url('assets/js/colResizable-1.6.js')?>"></script> -->
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/cr-1.3.2/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/scroller/1.4.2/js/dataTables.scroller.min.js"></script>

<script type="text/javascript" src="https://cdn.datatables.net/select/1.2.1/js/dataTables.select.min.js"></script>

<script type="text/javascript" src="<?php echo base_url('assets/js/colResizable-1.6.js')?>"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>

<!-- Datatables Print -->
<script type="text/javascript" src="<?php echo base_url('assets/js/colResizable-1.6.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery-ui.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/dataTables.buttons.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/buttons.flash.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jszip.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/pdfmake.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/vfs_fonts.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/buttons.html5.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/buttons.print.min.js')?>"></script>

<!-- Datetime picker jquery plugin -->
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.datetimepicker.full.min.js')?>"></script> 
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.periodpicker.full.min.js')?>"></script>
<!-- <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.timepicker.min.js')?>"></script> -->

<!-- Printing helper library -->
<script type="text/javascript" src="<?php echo base_url('assets/js/print.min.js')?>"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.3/summernote.js"></script>
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=6snowsig3xxjaos4tj97gm8w0h00zfqveq2qor987dxph1f3"></script>
<!-- Script de la barre d'actions -->
<?php include "application/views/templates/barre_action-js.php";?>
<?php include "application/views/pieces/modal-js.php";?>

<!-- Notifications, erreurs de validation et alertes -->
<div id="notification"></div>
<div id="validation_errors"></div>

<!-- Scripts des contrôles riches -->
<?php if (isset($scripts)) {
    foreach ($scripts as $s) {
        echo $s;
    }
}?>

<!-- Script de bas de page -->
<?php include "application/views/pieces/footer_script.php";?>

</body>
</html>
