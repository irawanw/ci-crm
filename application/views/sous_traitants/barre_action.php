<!-- barre d'actions -->
    </div> <!-- container -->
    <div class="row action-bar">
        <ul class="nav nav-pills"><li>
<?php $numero = 1;
$modales = array();
foreach($barre as $section) {
    $rang = count($section);
    foreach ($section as $nom=>$action) {
        $target = '';
        if (substr($action[0],0,1) == '*') {
            $target = ' target="_blank"';
            $action[0] = substr($action[0],1);
        }
        $cible = site_url($action[0]);
        if (! $action[2]) {
            $disabled = ' disabled';
            $href = '#';
        }
        else {
            $disabled = '';
            $href = $cible;
            if (isset($id)) {
                $href .= "/$id";
            }
            else {
                $id = 0;
            }
        }
        if (verifie_droits($droits,$profil,$action[0])) {?>
            <li class="text-center<?php echo $disabled?><?php if (--$rang == 0) echo ' action-sep'?>"<?php
            if (count($action) == 4) {
                echo ' id="'.$action[3].'"'?>><a href="<?php echo $href?>" data-cible="<?php echo $cible?>"<?php echo $target?>>
                <span class="glyphicon glyphicon-<?php echo $action[1]?>" aria-hidden="true"></span><span style="font-size:12px">
                <br/><?php echo $nom?></span></a></li>
<?php       }
            elseif (count($action) == 5) {
if($action[0]=="newtournee_journalieres/validate"){
		//$numero="popup_tournee_journ_valid";
		
	  echo ' id="'.$action[3].'"'?>>
			<a href="#"  data-toggle="modal" data-target="#popup_tournee_journ_valid">
                <span class="glyphicon glyphicon-<?php echo $action[1]?>" aria-hidden="true"></span><span style="font-size:12px">
                <br/><?php echo $nom?></span></a>

</li>
<?php         
  $ancre = anchor($action[0]."/$id",'Oui','class="btn btn-success" role="button"'); 
  
   $modales[] = <<<EOD
    <div class="modal fade" id="popup_tournee_journ_valid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Confirmation Valider la tournee journaliere</h4>
                </div>
                <div class="modal-body">				
                   Voulez-vous valider la tournee journaliere? 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                    $ancre
                </div>
            </div>
        </div>
    </div>

EOD;
 } 
elseif($action[0]=="newtournee_journalieres/devalidate"){
//	$numero="popup_tournee_journ_devalid";
  echo ' id="'.$action[3].'"'?>>
			<a href="#"  data-toggle="modal" data-target="#popup_tournee_journ_devalid">
                <span class="glyphicon glyphicon-<?php echo $action[1]?>" aria-hidden="true"></span><span style="font-size:12px">
                <br/><?php echo $nom?></span></a>

</li>
<?php         
  $ancre = anchor($action[0]."/$id",'Confirmer','class="btn btn-warning" role="button"'); 
   $modales[] = <<<EOD
    <div class="modal fade" id="popup_tournee_journ_devalid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Confirmation Devalider la tournee journaliere</h4>
                </div>
                <div class="modal-body">
                   Voulez-vous devalider la tournee journaliere? 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    $ancre
                </div>
            </div>
        </div>
    </div>

EOD;
  } 
else{
  echo ' id="'.$action[3].'"'?>>
<a href="#" class="action-button-conf" data-toggle="modal" data-target="#popup_<?php echo $numero?>">
                <span class="glyphicon glyphicon-<?php echo $action[1]?>" aria-hidden="true"></span><span style="font-size:12px">
                <br/><?php echo $nom?></span></a>
	
</li>
<?php           $ancre = anchor($action[0]."/$id",$nom,'class="btn btn-danger" role="button"'); 

 $modales[] = <<<EOD
    <div class="modal fade" id="popup_$numero" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Confirmation d'opération</h4>
                </div>
                <div class="modal-body">
                    $action[4]
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    $ancre
                </div>
            </div>
        </div>
    </div>

EOD;


}		
               

            }
        }
    }
    $numero++;
}?>
        </ul>
    </div>
<?php // création des fenêtres modales de confirmation
foreach($modales as $m) {
    echo $m;
}?>
    <div class="container">
