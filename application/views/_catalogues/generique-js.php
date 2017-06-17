<script>
    $(document).ready(function(){
        $('#form_G').click(function() {
            var data = {
                id: 1, // l'article 1 doit être initialisé dans la base
                code: 'G',
                description: $('#form_G_intitule').val(),
                info:"",
                prix: $('#form_G_prix').val(),
                quantite:1
            };
            nouvel_article(data);
            $('#form_G_intitule').val('');
            $('#form_G_prix').val(0);
        });

        $("#form_G_close").click(function(e){
            e.preventDefault() ;
            var win = $("#popup-G").data("kendoWindow");
            win.close() ;
        })
    });
</script>
