<div class="row" style="margin-top: 20px">
    <div class="col-md-12 fiche">
        <?php if ($action == 'modif') {
            $cible = "$controleur/$methode/$id";
        }
        else {
            $cible = "$controleur/$methode";
        }
		
        if ($multipart) {
            echo form_open_multipart(current_url(),array('role'=>'form','class'=>'form-horizontal'),array('__form'=>'x'));
        }
        else {
            echo form_open(current_url(),array('role'=>'form','class'=>'form-horizontal'),array('__form'=>'x'));
        }
		
        //include 'application/views/templates/form_champs.php';
       ?>
	   <style>
	   .guide{
		   border-bottom:0px!important;
	   }
	   </style>

<div class="tab-content">

<div class="row guide">
		&nbsp;
			<div class="form-group">
			<label class="col-sm-3 control-label" >Upload</label>
			<div class="col-sm-9">
			<input id="ctc_upload" class="form-control" name="ctc_upload"  type="file">
			<input id="ctc_test" class="form-control" name="ctc_test" value="1"  type="hidden">
			</div>
		    </div>
			</div>
			
            </div><!---tab end-->
        <br />
        <p class="text-center"><button type="submit" class="btn btn-primary"><?php echo $confirmation?></button> <a href="<?php echo  base_url()."index.php/".$controleur;?>" class="btn btn-default">Retour</a></p>
		<p class="text-center">

</p>
        <?php echo form_close()?>
    </div>
</div>
