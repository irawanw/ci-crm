<script type="text/javascript">

$(document).ready(function(){
	$("#datatable").on("click",".download-file", function(e) {
		e.preventDefault();
		var id = $(this).attr('data-id');
		var url = "<?php echo $url_download;?>";

        $.ajax({
            type: "POST",
            url: url,
            data: {id: id},
            dataType: "json",
            success: function(response) {
            	if(response.success == true) {
            		var event = response.data.event;
            		notificationWidget.show(response.message, response.notif);
            		
            		var redirect = event.redirect;

            		if(redirect) {
            			window.open(redirect);
            		}
            	} else {
            		notificationWidget.show(response.message, response.notif);
            	}
            }
        });
	});
});
</script>