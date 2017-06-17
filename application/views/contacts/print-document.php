<!DOCTYPE html>
<html>
<head>
	<title>Print Document Contacts</title>

	<style type="text/css">
		.break { page-break-before: always; }
		@media print
		{    
		    .no-print, .no-print *
		    {
		        display: none !important;
		    }
		}
	</style>
</head>
<body onload="window.print()">
	<?php include FCPATH.$path_file; ?>
</body>
</html>