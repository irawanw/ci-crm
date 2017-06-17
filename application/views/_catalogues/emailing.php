<style>
	.filter-label{
		width: 65px;
		float: left;
		margin-right: 3px;
		font-size: 11px;
		padding-top: 5px;
	}
	.filter-input{
		width: 65px;
		float: left;
		margin-right: 15px;
		margin-bottom: 3px;
		font-size: 11px;
	}
	#filter-form{
		width: 100%;
		margin: auto;
	}
	#filter-form input,
	#filter-form select{
		width: 60px;
		font-size: 11px;
		margin-bottom: 3px;
	}
</style>
<div class="text-center">	
	<div id="filter-form">
		<form id="filter" action="" method="post">
			<div class="filter-label">
				Name<br>
				<select name="th_filter_name_filtertype" id="th_filter_name_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
				</select>
				<input name="th_filter_name_input" id="th_filter_name_input">
			</div>
			<div class="filter-label">
				Address<br>
				<select name="th_filter_address_filtertype" id="th_filter_address_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
				</select>
				<input name="th_filter_address_input" id="th_filter_address_input">
			</div>
			<div class="filter-label">
				Postal code<br>
				<select name="th_filter_postal_code_filtertype" id="th_filter_postal_code_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
					<option value="btw">between</option>
				</select>		
				<input name="th_filter_postal_code_input" id="th_filter_postal_code_input">
			</div>	
			<div class="filter-label">
				City<br>
				<select name="th_filter_city_filtertype" id="th_filter_city_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
				</select>		
				<input name="th_filter_city_input" id="th_filter_city_input">
			</div>	
			<div class="filter-label">
				Region<br>
				<select name="th_filter_region_filtertype" id="th_filter_region_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
				</select>		
				<input name="th_filter_region_input" id="th_filter_region_input">
			</div>	
			<div class="filter-label">
				Country<br>
				<select name="th_filter_country_filtertype" id="th_filter_country_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
				</select>		
				<input name="th_filter_country_input" id="th_filter_country_input">
			</div>	
			<div class="filter-label">
				Tel<br>
				<select name="th_filter_tel_filtertype" id="th_filter_tel_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
					<option value="btw">between</option>
				</select>		
				<input name="th_filter_tel_input" id="th_filter_tel_input">
			</div>	
			<div class="filter-label">
				Fax<br>
				<select name="th_filter_fax_filtertype" id="th_filter_fax_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
					<option value="btw">between</option>
				</select>		
				<input name="th_filter_fax_input" id="th_filter_fax_input">
			</div>	
			<div class="filter-label">
				Mobile<br>
				<select name="th_filter_mobile_filtertype" id="th_filter_mobile_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
					<option value="btw">between</option>
				</select>		
				<input name="th_filter_mobile_input" id="th_filter_mobile_input">
			</div>	
			<div class="filter-label">
				Type<br>
				<select name="th_filter_type_filtertype" id="th_filter_type_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
				</select>		
				<input name="th_filter_type_input" id="th_filter_type_input">
			</div>	
			<div class="filter-label">
				Activity<br>
				<select name="th_filter_activity_filtertype" id="th_filter_activity_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
				</select>		
				<input name="th_filter_activity_input" id="th_filter_activity_input">
			</div>	
			<div class="filter-label">
				Email<br>
				<select name="th_filter_email_filtertype" id="th_filter_email_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
					<option value="dmnlike">domain like</option>
					<option value="notdmnlike">domain not like</option>			
					<option value="isempty">is empty</option>
					<option value="isnotempty">is not empty</option>			
				</select>		
				<input name="th_filter_email_input" id="th_filter_email_input">
			</div>	
			<div class="filter-label">
				Email status<br>
				<select name="th_filter_email_status_filtertype" id="th_filter_email_status_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
					<option value="isempty">is empty</option>
					<option value="isnotempty">is not empty</option>			
				</select>		
				<input name="th_filter_email_status_input" id="th_filter_email_status_input">
			</div>	
			<div class="filter-label">
				Site<br>
				<select name="th_filter_site_filtertype" id="th_filter_site_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="isempty">is empty</option>
					<option value="isnotempty">is not empty</option>
				</select>		
				<input name="th_filter_site_input" id="th_filter_site_input">
			</div>	
			<div class="filter-label">
				<div style="font-size: 9px; margin-bottom: 3px">Mrkting stat.</div>
				<select name="th_filter_marketing_status_filtertype" id="th_filter_marketing_status_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
					<option value="isempty">is empty</option>
					<option value="isnotempty">is not empty</option>			
				</select>		
				<input name="th_filter_marketing_status_input" id="th_filter_marketing_status_input">
			</div>	
			<div class="filter-label">
				Origin<br>
				<select name="th_filter_import_file_origin_filtertype" id="th_filter_import_file_origin_filtertype">
					<option value="">[select]</option>
					<option value="eq">is equal to</option>
					<option value="noteq">is different than</option>
					<option value="cont">contains</option>
					<option value="notcont">does not contain</option>
					<option value="st">starts with</option>
					<option value="notst">does not start with</option>
					<option value="isempty">is empty</option>
					<option value="isnotempty">is not empty</option>
				</select>		
				<input name="th_filter_import_file_origin_input" id="th_filter_import_file_origin_input">
			</div>	
			<div style="clear: both"></div>
		</form>
		<br>
		<button id="comptage" value="comptage" onclick="comptage()" style="margin-bottom: 5px">Comptage</button><br>
		<span id="count-result"></span>
		<br><br>
	</div>
	
    <div id="grid_E"></div>
    <p class="text-center"><button type="submit" disabled="disabled" class="btn btn-primary" id="form_E">Ajouter</button>
    <button type="button" class="btn btn-default" id="form_E_close">Fermer</button></p>
</div>