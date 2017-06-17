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
<div class="container well" style="margin-top:30px;margin-bottom:10px;">
	<div class="col-lg-12">
		<div class="col-lg-2">
			<div class="form-group">
			<label class="form-control-static">Commercial</label>
			<select class="form-control" name="commercial" id="commercial">
				<option value="0">Tous</option>
				<?php foreach($list_commercial as $commercial):?>
				<option value="<?php echo $commercial->utl_id;?>"><?php echo $commercial->utl_login;?></option>
				<?php endforeach;?>
			</select>
			</div>
		</div>
		<div class="col-lg-2">
			<div class="form-group">
				<label class="form-control-static">Periods</label>
				<select class="form-control" name="periods" id="periods">
				<option value="all">Tous</option>
				<option value="week">Semaine</option>
				<option value="month">Mois En Cours</option>
				<option value="day30">30 Jours</option>
				<option value="day90">90 Jours</option>
				<option value="month6">6 Mois</option>
				<option value="year">1 An</option>
				</select>
			</div>
		</div>
		<div class="col-lg-2">
			<div class="form-group">
				<label class="form-control-static">Enseignes</label>
				<select class="form-control" name="enseignes" id="enseignes">
					<option value="0">Tous</option>
					<?php foreach($list_enseignes as $enseignes):?>
					<option value="<?php echo $enseignes->id;?>"><?php echo $enseignes->value;?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>
	</div>
	<div class="col-lg-12">
		<div class="col-lg-2">
			<div class="form-group">
				<label class="form-control-static">Origine par type</label>
				<select class="form-control" name="generale" id="generale">
					<option value="0">Tous</option>
					<?php foreach($list_generale as $generale):?>
					<option value="<?php echo $generale->id;?>"><?php echo $generale->value;?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>	
		<div class="col-lg-2">
			<div class="form-group">
				<label class="form-control-static">Origine E-mailing détail</label>
				<select class="form-control" name="origine" id="Oemailing">
					<option value="0">Tous</option>
					<?php foreach($list_origine[1] as $origine):?>
					<option value="<?php echo $origine->id;?>"><?php echo $origine->value;?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>
		<div class="col-lg-2">
			<div class="form-group">
				<label class="form-control-static">Origine Adwords détail</label>
				<select class="form-control" name="origine" id="Oadwords">
					<option value="0">Tous</option>
					<?php foreach($list_origine[2] as $origine):?>					
					<option value="<?php echo $origine->id;?>"><?php echo $origine->value;?></option>					
					<?php endforeach;?>
				</select>
			</div>
		</div>
		<div class="col-lg-2">
			<div class="form-group">
				<label class="form-control-static">Origine Flyers détail</label>
				<select class="form-control" name="origine" id="Oflyers">
					<option value="0">Tous</option>
					<?php foreach($list_origine[4] as $origine):?>					
					<option value="<?php echo $origine->id;?>"><?php echo $origine->value;?></option>					
					<?php endforeach;?>
				</select>
			</div>
		</div>
		<div class="col-lg-2">
			<div class="form-group">
				<label class="form-control-static">Origine Propection détail</label>
				<select class="form-control" name="origine" id="Oprospection">
					<option value="0">Tous</option>
					<?php foreach($list_origine[3] as $origine):?>					
					<option value="<?php echo $origine->id;?>"><?php echo $origine->value;?></option>					
					<?php endforeach;?>
				</select>
			</div>
		</div>
		<div class="col-lg-2">
			<div class="form-group">
				<label class="form-control-static">Other Origine détail</label>
				<select class="form-control" name="origine" id="Oother">
					<option value="0">Tous</option>					
					<?php 
					$otherID = array(5,6,7,8);
					foreach($otherID as $i):
						foreach($list_origine[$i] as $origine):?>
					<option value="<?php echo $origine->id;?>"><?php echo $origine->value;?></option>
					<?php endforeach;
					endforeach;
					?>
				</select>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-default" style="margin-top:0px;">
	<div class="panel-body" id="filtre"></div>
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