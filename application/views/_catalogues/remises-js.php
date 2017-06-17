<script>
    $(document).ready(function(){
        $('#form_R').click(function() {
            var data = {
                id: 2, // l'article 2 doit être initialisé dans la base
                code: 'R',
                description: $('#form_R_intitule').val(),
                info: "",
                prix: $('#form_R_taux').val() / 100,
                quantite: 1
            };
            nouvel_article(data);
            $('#form_R_intitule').val('');
            $('#form_R_taux').val(0);
        });

        $("#form_R_close").click(function(e){
            e.preventDefault() ;
            var win = $("#popup-R").data("kendoWindow");
            win.close() ;
        })

    });
</script>
