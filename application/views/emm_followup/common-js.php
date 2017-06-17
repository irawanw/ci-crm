<script type="text/javascript">
	$(document).ready(function() {
		//default view hide deliverance and technical column
		deliveranceColumn('hide');
		technicalColumn('hide');

		//action toggle button to show/hide deliverance column
		$('#emm_followup_show_deliverance a').click(function(e) {
			//console.log("show deliverance");
			e.preventDefault();
			var textHtml = $(this).html();
			var hide = $(this).attr('data-hide') ? $(this).attr('data-hide') : false;

			if(hide) {
				textHtml = textHtml.replace("Hide", "Show");
				$(this).html(textHtml);
				$(this).removeAttr('data-hide');

				deliveranceColumn('hide');
			} else {
				textHtml = textHtml.replace("Show", "Hide");
				$(this).html(textHtml);
				$(this).attr('data-hide', true);
				deliveranceColumn('show');
			}
		});

		//action toggle button to show/hide techincal column
		$('#emm_followup_show_technical a').click(function(e) {
			//console.log("show technical");
			e.preventDefault();
			var textHtml = $(this).html();
			var hide = $(this).attr('data-hide') ? $(this).attr('data-hide') : false;

			if(hide) {
				textHtml = textHtml.replace("Hide", "Show");
				$(this).html(textHtml);
				$(this).removeAttr('data-hide');

				technicalColumn('hide');
			} else {
				textHtml = textHtml.replace("Show", "Hide");
				$(this).html(textHtml);
				$(this).attr('data-hide', true);
				technicalColumn('show');
			}
		});		
	});

	function deliveranceColumn(action) {
		var deliveranceColumn = ['deliverance','percentage_delivery','percentage_spam','percentage_not_delivered','ip_blacklist','message_blacklist','domain_blacklist','sender_blacklist'];
		var grid = $('#grid').data('kendoGrid');

		deliveranceColumn.map(function(obj) {
			if(action === 'hide') {
				grid.hideColumn(obj);
			} else {
				grid.showColumn(obj);
			}
		});
	}

	function technicalColumn(action) {
		var deliveranceColumn = ['server', 'smtp', 'rotation'];
		var grid = $('#grid').data('kendoGrid');
		deliveranceColumn.map(function(obj) {
			if(action === 'hide') {
				grid.hideColumn(obj);
			} else {
				grid.showColumn(obj);
			}
		});
	}
	
	function afterLoaded(){
		for(i=25; i<=27; i++){
			$('#grid th:nth-child('+i+')').css('background-color', 'lavender');
			$('#grid tr td:nth-child('+i+')').css('background-color', 'lavender');
		}
		for(i=17; i<=24; i++){
			$('#grid th:nth-child('+i+')').css('background-color', 'lemonchiffon');
			$('#grid tr td:nth-child('+i+')').css('background-color', 'lemonchiffon');
		}
	}
</script>

<div class="upload" style="display: none">
	<form enctype="multipart/form-data" method="post" action="'.site_url('emm_followup').'/upload_message">
		<input type="file" name="message" id="message">
		<input type="hidden" name="id" id="id" value="0">
		<br>
		<input type="submit" value="Submit">
	</form>
</div>
<script>
	$(document).ready(function(){
		$(".upload").kendoWindow({
			visible: false
		});
		$(".upload").data("kendoWindow").title("Upload a File");
	})
	function uploadfile(id){
		$(".upload").data("kendoWindow").open().center();
		$("#id").val(id);
	}
</script>