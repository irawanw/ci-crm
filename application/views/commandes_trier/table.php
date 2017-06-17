<?php if(count($lists) > 0):?>
<table class="table table-bordered" style="font-size: 11px">
    <thead>
        <tr>
    	<?php        
        foreach($lists[0] as $i => $row):
            if($i == "ville"):
        ?>
    	    <th style="padding: 4px">Villes/Clients</th>
    	<?php else: ?>
            <th style="padding: 4px"><?php echo $i;?></th>
        <?php
            endif;
        endforeach; 
        ?>
    	   </tr></thead>
    	<tbody>
    	<?php
        foreach($lists as $list):
    	?>
    		<tr>
    		<?php
            foreach($list as $val):
    		?>
    			<td style="padding: 4px"><?php echo $val;?></td>
    		<?php 
            endforeach;
            ?>
    		</tr>
    	<?php
        endforeach;        
        ?>
</tbody>
</table>
<?php else: ?>
    <div class="row" style="margin-top: 20px;">
        <h3>Not Found Data</h3>
    </div>
<?php endif; ?>