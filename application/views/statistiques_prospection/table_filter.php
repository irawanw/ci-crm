<?php
$origine_total = $list_origine['resultTotal'];
$list_origine_detail = $list_origine['resultDetail'];

$generale_selected = $this->input->post('generale'); 	
$origine_selected = $this->input->post('origine'); 	
?>

<div class="row">	
	<div class="col-md-12">
		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">		
		  <?php if($generale_selected == 0 && $origine_selected == 0):?>
		  <div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Total</h4>
			</div>
			<div class="panel-body">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th width="40%"></th>
							<?php foreach($header as $key => $val):?>
								<th width="10%"><?php echo strtoupper($val)?></th>
							<?php endforeach;?>
						</tr>
					</thead>
					<tbody>
						<?php foreach($data as $key =>$val):?>
						<tr>
							<td><b><?php echo strtoupper(str_replace('_',' ',$key));?></b></td>
							<?php foreach($val as $value):?>
							<td><?php echo $value;?></td>
							<?php endforeach;?>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</div> 
		<?php endif; ?>

		<?php 
		foreach($list_origine_detail as $group_id => $list): 
			$generale_filter = strtoupper($this->input->post('generale')); 						
			if(($generale_filter == $group_id || $generale_filter == 0)):
		?>
		  <div class="panel panel-default">
		  	<div class="panel-heading" role="tab" id="headingOne">
		      	<h4 class="panel-title">
			        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?php echo $group_id;?>" aria-expanded="true" aria-controls="collapse-<?php echo $key?>">		          
			        	<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
			        	Total <?php echo $list[0][$group_id];?>
			        </a>		    
		      	</h4>
		    </div>
		  	<div class="panel-body">
		  		<table class="table table-condensed">
		        	<thead>
						<tr>
							<th width="40%"></th>
							<?php foreach($header as $key => $val):?>
								<th width="10%"><?php echo strtoupper($val)?></th>
							<?php endforeach;?>
						</tr>
					</thead>
					<tbody>
				  		<?php 				
						
							foreach($origine_total[$group_id] as $k => $li):
						?>					
						<tr>
							<td><b><?php echo strtoupper($k);?></b></td>
							<?php foreach($li as $per):?>
								<td><?php echo $per?></td>
							<?php endforeach;?>
						</tr>					
						<?php 
							endforeach;
						?>
					</tbody>
				</table>
		  	</div>

		    <div id="collapse-<?php echo $group_id;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
		      <div class="panel-body">
		      	<h3>Detail</h3>
		      	<hr>
		        <table class="table table-condensed">
		        	<thead>
						<tr>
							<th width="40%"></th>
							<?php foreach($header as $key => $val):?>
								<th width="10%"><?php echo strtoupper($val)?></th>
							<?php endforeach;?>
						</tr>
					</thead>
					<tbody>
				  	<?php 				
					foreach($list as $value):
						foreach($value as $k => $li):
							if(is_array($li)):
					?>					
					<tr>
						<td><b><?php echo strtoupper($k);?></b></td>
						<?php foreach($li as $per):?>
							<td><?php echo $per?></td>
						<?php endforeach;?>
					</tr>					
					<?php 
							endif;
						endforeach;
					?>
						<tr>
							<td colspan="7" style="background-color: #ccc;"></td>
						</tr>							
					<?php 
					endforeach; 
					?>
					</tbody>
				</table>
		      </div>
		    </div>
		  </div>
		  <?php 
		  	endif;
		  endforeach;?>
		</div>
	</div>
</div>