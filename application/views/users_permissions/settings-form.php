<div class="container well" style="margin-top:30px;margin-bottom:10px;">
	<div class="col-lg-3">				
		<div class="form-group">
			<form method="POST" action="<?php echo site_url('users_permissions_v2/post'); ?>" accept-charset="utf-8" id="form-users_permissions">
				Utilisateurs 
				<select class="form-control" name="usp_utilisateurs" id="usp_utilisateurs" style="width: 200px">
					<option value="0" selected="selected">(choisissez)</option>
					<?php 
						foreach ($usp_utilisateurs as $value) {						
					 ?>
					 <option value="<?php echo $value->id;?>"><?php echo $value->value; ?></option>
					 <?php 
						}
					 ?>
				</select><br>				
				Menu
				<select class="form-control" name="usp_table" id="usp_table" style="width: 200px">
					<option value="0" selected="selected">(choisissez)</option>
					<?php 
						foreach ($usp_table as $value) {						
					 ?>
					 <option value="<?php echo $value->id;?>"><?php echo $value->value; ?></option>
					 <?php 
						}
					 ?>
				</select> 
				<br>
				<input type="button" value="Add New Permission" 
								id="permission-add" style="height: 32px;"><br/>
			</form>
		</div>
	</div>
	<div class="col-md-6" id="table-settings"></div>
</div>