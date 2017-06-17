<?php
//$origine_total = $list_origine['resultTotal'];
//$list_origine_detail = $list_origine['resultDetail'];

//$generale_selected = $this->input->post('generale'); 	
//$origine_selected = $this->input->post('origine'); 	
?>

<div class="row">	
	<div class="col-md-12">
		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">		
			<?php //if($generale_selected == 0 && $origine_selected == 0):?>
			<div class="panel panel-default">
				<div class="panel-heading"><h4 class="panel-title">Total</h4></div>
				<div class="panel-body">
					<table class="table table-condensed">
						<thead>
							<tr>
								<th></th>
								<?php foreach($header as $key => $val):?>
									<th><?php echo strtoupper($val)?></th>
								<?php endforeach;?>
							</tr>
						</thead>
						<tbody>
							<?php foreach($data as $master => $header):?>
								<?php foreach($header as $key => $value):?>
								<tr>
									<td><b><?php echo strtoupper(str_replace('_',' ',$key));?></b></td>
									<?php foreach($value as $val):?>
									<td><?php echo $val;?></td>
									<?php endforeach;?>
								</tr>
								<?php endforeach;?>
							<?php endforeach;?>
						</tbody>
					</table>
				</div>
			</div>
			<?php //endif; ?>
		</div>
	</div>
</div>