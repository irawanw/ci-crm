<!-- MONTHLY REPORT TABLE -->
<div class="row">
	<div class="col-md-12">
		<h3>MONTHLY FOLLOWUP</h3>
	</div>
	<div class="col-md-12">
		<?php		
		/** List Generale **/
		foreach($list_generale as $key => $val):
			$name = strtoupper($val);
		?>
		<h5><strong><?php echo $name;?> QUICK monthly FOLLOWUP</strong></h5>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th></th>
					<th>CA <?php echo $name;?></th>
					<th>TAUX CONVERTION</th>
					<th>NOMBRE CONTACTS</th>
					<?php foreach($list_commercial as $commercial):?>
					<th>CA <?php echo $name." ".strtoupper($commercial->utl_login);?></th>
					<th>TAUX DE CONVERTION <?php echo strtoupper($commercial->utl_login);?></th>
					<th>NOMBRE DE CONTACTS <?php echo strtoupper($commercial->utl_login);?></th>
					<?php endforeach;?>
				</tr>
			</thead>
			<tbody>
				<?php 
				/** List Month **/
				foreach($months as $month):
					$monthId = $month['id'];
					$data_month = $data_monthly[$key][$monthId];
				?>
				<tr>
					<td><?php echo $month['name'];?></td>
					<td><?php echo $data_month[0]->ca;?></td>
					<td><?php echo $data_month[0]->taux;?></td>
					<td><?php echo $data_month[0]->nombre;?></td>
					<?php 
					foreach($list_commercial as $commercial):
						if(array_key_exists($commercial->utl_id, $data_month)):
					?>
					<td><?php echo $data_month[$commercial->utl_id]->ca;?></td>
					<td><?php echo $data_month[$commercial->utl_id]->taux;?></td>
					<td><?php echo $data_month[$commercial->utl_id]->nombre;?></td>
					<?php else: ?>
					<td>0</td>
					<td>0</td>
					<td>0</td>
					<?php 
						endif;
					endforeach;
					?>
				</tr>
				<?php 
				endforeach; 
				/** Eof List Month **/
				?>
			</tbody>
		</table>
		<?php 
		endforeach;
		/** Eof List Generale **/
		?>
	</div>
</div>
<!-- ./MONTHLY REPORT TABLE -->

<!-- WEEKLY REPORT TABLE -->
<div class="row">
	<div class="col-md-12">
		<h3>SEMAINE FOLLOWUP</h3>
	</div>
	<div class="col-md-12">
		<?php		
		/** List Generale **/
		foreach($list_generale as $key => $val):
			$name = strtoupper($val);
		?>
		<h5><strong><?php echo $name;?> QUICK semaine FOLLOWUP</strong></h5>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th></th>
					<th>CA <?php echo $name;?></th>
					<th>TAUX CONVERTION</th>
					<th>NOMBRE CONTACTS</th>
					<?php foreach($list_commercial as $commercial):?>
					<th>CA <?php echo $name." ".strtoupper($commercial->utl_login);?></th>
					<th>TAUX DE CONVERTION <?php echo strtoupper($commercial->utl_login);?></th>
					<th>NOMBRE DE CONTACTS <?php echo strtoupper($commercial->utl_login);?></th>
					<?php endforeach;?>
				</tr>
			</thead>
			<tbody>
				<?php 
				/** List Weeks **/
				$totalWeek = count($weeks);
				foreach($weeks as $week):					
					$data_week = $data_weekly[$key][$week];
				?>
				<tr>
					<td><?php echo "Semaine ".$totalWeek;?></td>
					<td><?php echo $data_week[0]->ca;?></td>
					<td><?php echo $data_week[0]->taux;?></td>
					<td><?php echo $data_week[0]->nombre;?></td>
					<?php 
					foreach($list_commercial as $commercial):
						if(array_key_exists($commercial->utl_id, $data_week)):
					?>
					<td><?php echo $data_week[$commercial->utl_id]->ca;?></td>
					<td><?php echo $data_week[$commercial->utl_id]->taux;?></td>
					<td><?php echo $data_week[$commercial->utl_id]->nombre;?></td>
					<?php else: ?>
					<td>0</td>
					<td>0</td>
					<td>0</td>
					<?php			
						endif;		
					endforeach;
					?>
				</tr>
				<?php 
				--$totalWeek;
				endforeach; 
				/** Eof List Month **/
				?>
			</tbody>
		</table>
		<?php 
		endforeach;
		/** Eof List Generale **/
		?>
	</div>
</div>
<!-- ./WEEKLY REPORT TABLE -->
