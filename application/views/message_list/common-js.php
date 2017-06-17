<script>
	$(document).ready(function(){
	
	});

	//grouped header to be 3 groups
	function groupedHeader(){
		//grouped header
		var myElem = document.getElementById('trParentHeader');
        if (myElem == null){
            $("#grid").find("th.k-header").parent().before("<tr id='trParentHeader'><th style='text-align:center' colspan='6' class='k-header'><strong>Mail</strong></th> <th style='text-align:center' colspan='5' class='k-header'><strong>Mail Body</strong></th>  <th style='text-align:center' colspan='8' class='k-header'><strong>Mail Database</strong></th></tr>");
        }
	}	

	//this function will override afterLoaded in kendogrid_js
	//function will be called after data is successfully rendered
	var originAfterLoaded = afterLoaded;
	afterLoaded = function(arg) {
	     groupedHeader();
	     return originAfterLoaded(arg);
	}

</script>