<script>

    $(document).ready(function() {

        $("#template-modal-detail").on("change", "form.modal-form select[name='avr_client']", function() {
            var contact = $(this).val();
            var form = this.form.id;
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('correspondants/correspondants_contact_json') ?>/" + contact,
                dataType: "json",
                data: {
                    order: [{column: 0, dir: "asc"}],
                    columns: [{data: "cor_nom"}]
                }
            }).done(
                function(data) {
                    var select = $("#" + form).find("select[name='avr_correspondant']");
                    $(select).find("option:gt(0)").remove();
                    $.each(data.data, function (key, value) {
                        var nom = value.cor_nom.replace(/<[^>]*>/g, "");
                        var prenom = value.cor_prenom.replace(/<[^>]*>/g, "");
                        $(select).append($("<option>").attr("value", value.cor_id).text(nom + ", " + prenom));
                    })
                    if (data.data.length == 1) {
                        $(select).val(data.data[0].cor_id);
                    }
                }
            );
        })

    });
</script>
