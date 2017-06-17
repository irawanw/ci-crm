<table class="table table-bordered">
    <thead>
        <tr>
			<td>#</td>
			<td>Date</td>
			<td>Client</td>
			<td>Commande Ref.</td>
		</tr>
    </thead>
    <tbody>
    	<?php 
    		$n = 0;
    		foreach ($values as $value) { 
    		$n++;
    	?>
    	<tr>
			<td> <?php echo $n ?></td>
			<td> <?php echo date('d-m-Y', strtotime($value['dvi_date'])); ?> </td>
			<td> <?php echo $value['ctc_nom']?> </td>			
			<td> <?php echo $value['cmd_reference']?> </td>
		</tr>
		<?php } ?>
    </tbody>
</table>