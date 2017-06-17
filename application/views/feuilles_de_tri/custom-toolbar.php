<?php 
if($this->input->get('new_id') != 0){
?>
<div>
	<b>
		<a style="color: red" href="<?php echo site_url('feuilles_de_tri/group').'/'.$this->input->get('new_id'); ?>">
			VOIR LA FEUILLE DE TRI NOUVELLEMENT CREE
		</a>
	</b>
</div>
<?php } ?>
<br>
<?php 
//load confirmation mass remove box
$this->load->view('templates/remove_confirmation.php'); 
?>