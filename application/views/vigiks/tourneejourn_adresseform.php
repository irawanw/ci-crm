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
			<div class="form-group">
			<label class="col-sm-3 control-label" >Nom de la Adresse</label>
			<div class="col-sm-9">
			<select id="sel_adresse" class="form-control" name="sel_adresse" >
			<?php print_r($adresse); ?>			      
			</select>
			 <input id="tournee_id" class="form-control" name="tournee_id" value="<?php echo $values->tournee_id;?>"  type="hidden">
			</div>
		    </div>
			</div>
			<div class="row guide">
			<div class="form-group">
			<label class="col-sm-3 control-label" ></label>
			<div class="col-sm-9">
			
			</div>
		    </div>
			</div>
	      
        <br />
        <p class="text-center"><button type="submit" class="btn btn-primary"><?php echo $confirmation?></button>  <a href="<?php echo  base_url()."index.php/".$controleur;?>" class="btn btn-default">Retour</a></p>
        <?php echo form_close()?>
    </div>
</div>
<script>
    document.getElementById("sel_adresse").onchange = function() {
        if (this.selectedIndex===1) {
            window.location.href = this.value;
        }        
    };
</script>

       

