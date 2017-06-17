<style type="text/css">
	#filter-row {
		padding: 10px;
	}
	#filter_rangedate {
	    float: right;
	    width: auto;	 
	    margin-left: auto;	    
	}
</style>

<div class="row custom-toolbar" id="filter-row">
	<form class="form-inline">
		<div class="form-group">
			<label>Commercial</label>
			<select name="filter_commercial" class="form-control filter_type" id="filter_commercial" multiple="multiple">
				<?php foreach($list_commercial as $commercial):?>
				<option value="<?php echo $commercial->utl_id;?>"><?php echo $commercial->utl_login;?></option>
				<?php endforeach;?>
			</select>	
		</div>	
		<div class="form-group" style="margin-left: 25px;">
			<label style="margin-top: 6px;margin-right: 5px;">Date</label>			
			<select name="filter_rangedate" class="filter_type form-control" id="filter_rangedate">
				<option value="">(None Selected)</option>
				<?php foreach($list_date as $value => $text):?>
				<option value="<?php echo $value;?>"><?php echo $text;?></option>
				<?php endforeach;?>								
			</select>				               			
		</div>		
		<div class="form-group" id="filter-date-content">
			<input type="text" class="filter_type form-control" id="filter_type_ctc_date_creation" />
			<input type="hidden" id="filter_input_ctc_date_creation" class="filter_input">
		</div>
	</form>
</div>