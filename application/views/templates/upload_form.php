<div class="upload" style="display: none">
	<form enctype="multipart/form-data" method="post" action="<?php echo site_url($controleur).'/upload_'.$field; ?>">
		<input type="file" name="<?php echo $field; ?>" id="<?php echo $field; ?>">
		<input type="hidden" name="id" id="id" value="0">
		<br>
		<input type="submit" value="Submit">
	</form>
</div>
<script>
	$(document).ready(function(){
		$(".upload").kendoWindow({
			visible: false,
			modal: true
		});
		$(".upload").data("kendoWindow").title("<?php echo $title; ?>");
	})
	function uploadfile(id){
		$(".upload").data("kendoWindow").open().center();
		$("#id").val(id);
	}
</script>