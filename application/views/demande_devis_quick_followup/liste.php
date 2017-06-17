<link href="<?php echo base_url();?>/assets/css/jquery.periodpicker.min.css" rel="stylesheet" />
<style>
.loader 
{
	margin: 0 auto;
    border: 8px solid #f3f3f3; /* Light grey */
    border-top: 8px solid black; /* #3498db Blue */
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 2s linear infinite;
}

@keyframes spin 
{
    0% { transform: rotate(0deg);}
    100% { transform: rotate(360deg);}
}
</style>

<div class="row" style="margin-top:30px;">
	<!-- <div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Filter</h3>
			</div>
			<div class="panel-body">
				<form class="form-horizontal">
					<div class="form-group">
						<label class="control-label col-sm-2" for="date">Date</label>
					    <div class="col-sm-4">
					      <input type="text" class="form-control" id="date" placeholder="">
					    </div>						
					</div>
				</form>
			</div>
		</div>
	</div> -->
	<div class="col-md-12" id="filtre">
		
	</div>
</div>

<div class="modal mloader" tabindex="-1" role="dialog" style="top:200px" data-keyboard="false" data-backdrop="static">
  	<div class="modal-dialog modal-sm" role="document">
    	<div class="modal-content">
			<div class="modal-body text-center">
				<div class="loader"></div>
				<div>Loading...</div>
			</div>
		</div>
	</div>
</div>