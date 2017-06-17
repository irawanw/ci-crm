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
<div class="container" style="margin-top:30px;margin-bottom:10px;">
	<div class="col-lg-3">
		<div class="panel panel-default" style="margin-top:0px;">
			<div class="panel-body">			
				<div class="col-lg-12" style="max-height: 250px;overflow: auto;">
					<h4 class="text-center">Commercial</h4>
					<div class="row">
						<ul id="check-list-box" class="list-group checked-list-box">
							<?php foreach($list_commercial as $commercial):?>
							<li class="list-group-item" data-id="<?php echo $commercial->utl_id?>"><?php echo ' '.ucfirst(strtolower($commercial->utl_login));?></li>
							<?php endforeach;?>
						</ul>
					</div>
				</div>
				<div class="col-lg-12">
					<h4 class="text-center">Periods</h4>
					<div class="form-group">
						<div class="input-group" style="margin-bottom:5px">
							<span class="input-group-addon">
								<input type="radio" name="r_periods" id="r_periods" value="1" aria-label="..." checked>
							</span>
							<select class="form-control" name="periods_select" id="periods_select">
							<option value="all">Tous</option>
							<option value="week">Semaine</option>
							<option value="month">Mois En Cours</option>
							<option value="day30">30 Jours</option>
							<option value="day90">90 Jours</option>
							<option value="month6">6 Mois</option>
							<option value="year">1 An</option>
							</select>
						</div>
						<div class="input-group">
							<span class="input-group-addon">
								<input type="radio" name="r_periods" id="r_periods" value="2" aria-label="...">
							</span>
							<input class="form-control" id="periods_manual" type="text" name="datefilter" placeholder="Filter by date" style="border-radius: 4px;" disabled>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-9">
		<div class="row">
			<div class="panel panel-default" style="margin-top:0px;">
				<div class="panel-body" id="filtre"></div>
			</div>
		</div>
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
<input type="hidden" name="commercial" id="commercial">
<input type="hidden" name="periods" id="periods">