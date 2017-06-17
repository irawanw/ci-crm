<?php
if (isset($modal)) {
    $class_form_modal = ((is_string($modal)) ? $modal : 'modal-').'form ';
} else {
    $class_form_modal = '';
    $modal = false;
}

//action for retour a la liste
//when the reques is ajax then close the modal
//when it usual page go back to previous page
if($this->input->is_ajax_request()){
    //$retour = "javascript:$('.template-modal').modal('toggle')";
	$retour = "#";
} else {
	//$retour = "javascript:history.back();";
	$retour = site_url($this->uri->segment(1));
}
?>

<div class="row" style="margin-top: 20px">	
    <div class="col-md-12 fiche">        
        <?php if ($action == 'modif') {
            $cible = "$controleur/$methode/$id";
        }
        else {
            $cible = "$controleur/$methode";
        }
        if (!isset($form_id)) {
            $form_id = form_unique_id($controleur,$methode);
        }
        if ($multipart) {
            echo form_open_multipart(current_url(),array('id'=>$form_id,'role'=>'form','class'=>$class_form_modal.'form-horizontal'),array('__form'=>'x'));
        }
        else {
            echo form_open(current_url(),array('id'=>$form_id,'role'=>'form','class'=>$class_form_modal.'form-horizontal'),array('__form'=>'x'));
        }
        include 'application/views/templates/form_champs.php';
        if (count($descripteur['onglets']) > 0) {?>
            <ul class="nav nav-tabs" role="tablist">
                <?php $actif = ' class="active"';
                $tab = 1;
                foreach ($descripteur['onglets'] as $o) {
                    $libelle = $o[0];
                    $cle = $controleur.'-tab-'.$tab++;
                    ?>
                    <li id="<?php echo 's'.$cle?>" role="presentation" <?php echo $actif?>><a href="#<?php echo $cle?>" aria-controls="<?php echo $cle?>" role="tab" data-toggle="tab"><?php echo $libelle?></a></li>
                    <?php $actif = '';
                }?>
				<li>
                    <a id="show-modal-retour" href="<?php echo $retour; ?>" ><span class="glyphicon glyphicon-menu-left"></span> retour à la liste</a>
                </li>
            </ul>

            <div class="tab-content">
                <?php $actif = ' active';
                $tab = 1;
                foreach ($descripteur['onglets'] as $o) {
                    $cle = $controleur.'-tab-'.$tab++;?>
                    <div role="tabpanel" class="tab-pane <?php echo $actif?>" id="<?php echo $cle?>">
                        <?php   $actif = '';
                        foreach($o[1] as $c) {							
							//$champ = $champs[$c];
                            $champ = isset($champs[$c]) ? $champs[$c] : '';
                            echo $champ;
                        }?>
                    </div>
                <?php }?>
            </div>
        <?php }
        else {
			/*
			echo '<ul class="nav nav-tabs" role="tablist">';
			echo 	'<li><a href="'.$retour.'" id="show-modal-retour"><span class="glyphicon glyphicon-menu-left"></span> retour à la liste</a></li>';
			echo '</ul>';
			*/
			//echo '<div style="margin-top:10px"';
            foreach($champs as $c) {
                echo $c;
            }
			//echo '</div>';
        }?>
        <br/>
        <p class="text-center">
            <button id="form-submit-<?php echo $controleur?>-<?php echo $methode?>" type="submit" class="btn btn-primary"><?php echo $confirmation?></button>
        </p>
        <?php echo form_close()?>
        
    </div>
</div>
