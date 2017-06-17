<script>
    $(document).ready(function(){
        //pull commande data when client select is changed
        $("#client").change(function(){
            $.get("<?php echo site_url('emm_followup'); ?>/commande_option/"+$("#client").val(), function(data){
                $("#commande").html(data);
            });
        })

        $("#commande").change(function(){
            passe_cmd_change();
        })
		
        $("#mailing").change(function(){
            $.getJSON("<?php echo site_url('emm_followup'); ?>/openemm/"+$("#mailing").val(), function(data){
				$("#open_rate").val(data.open_rate);
				$("#bounce_rate").val(data.bounce_rate);
				$("#hard_bounce_rate").val(data.hard_bounce_rate);
				$("#soft_bounce_rate").val(data.soft_bounce_rate);
				$("#click_rate").val(data.click_rate);
				$("#number_of_clicks").val(data.number_of_clicks);
				$('#number_of_opens').val(data.opened);
				$('#quantite_totale_a_envoyer').val(data.total);
				$('#quantite_envoyee').val(data.current);

            });
        })		

        passe_cmd_change();
		
		var Columns = 	[	
			'quantite_totale_a_envoyer', 
			'quantite_envoyee',
			'open_rate',
			'bounce_rate',
			'hard_bounce_rate',
			'soft_bounce_rate',
			'click_rate',
			'number_of_clicks',
			'number_of_opens',
			'deliverance',
			'percentage_delivery',
			'percentage_spam',
			'percentage_not_delivered',
			'ip_blacklist',
			'message_blacklist',
			'domain_blacklist',
			'sender_blacklist'
		];
								
		Columns.map(function(obj) {
			$("#"+obj).attr('readonly','readonly');
		});
    })

    function passe_cmd_change(){
        if($("#commande option:selected").val()=='-1'){
            $("#commande").css('color', 'red');
            $("#commande").css('font-weight', 'bold');
        } else {
            $("#commande").css('color', '#555');
            $("#commande").css('font-weight', 'normal');
        }
    }
</script>
