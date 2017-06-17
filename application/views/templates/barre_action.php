<?php
if (!empty($barre)) {
    if (isset($modal)) {
        $prefix_modal = (is_string($modal)) ? $modal : 'modal-';
    } else {
        $prefix_modal = '';
        $modal = false;
    }
?>
<!-- barre d'actions -->
    <div class="row action-bar"<?php if ($prefix_modal != '') echo ' data-prefix="'.$prefix_modal.'"'; ?>>
        <ul class="nav nav-pills">
<?php $numero = 0;
$modales = array();
$default_view = false;
$double_click = false;
foreach($barre as $section) {
    $rang = count($section);
    foreach ($section as $nom=>$action) {
        $target = '';
        // Whether the link target opens a list of records (vs a single record)
        $ouvre_liste = false;
        if (substr($action[0], -2) == '[]') {
            $ouvre_liste = true;
            $action[0] = substr($action[0],0,-2);
        }
        // Should the link target open in a new window / tab by default
        if (substr($action[0],0,1) == '*') {
            $target = ' target="_blank"';
            $action[0] = substr($action[0],1);
        }
        // If we are displaying the button bar in a modal window and the target link
        // opens a list, then it should open in a new window / tab
        if ($modal && $ouvre_liste) {
            $target = ' target="_blank"';
        }
        // Remove any initialized parameters that shouldn't be in the template
        // @see initialise_action_barre_action()
        $cible = site_url(preg_replace('/\\{[^}]*\\}/', '', $action[0]));
        // If not enabled and not a controller URL
        if ($action[0] == '#') {
            $disabled = (! $action[2]) ? ' disabled disabled-by-default' : '';
            $href = '#';
            $cible = '#';
        }
        // If not enabled, but a valid controller URL
        elseif (! $action[2]) {
            $disabled = ' disabled disabled-by-default';
            $href = '#';
        }
        else {
            $disabled = '';
            // Remove curly braces around any initialized value
            // @see initialise_action_barre_action()
            $href = site_url(str_replace(array('{', '}'), '', $action[0]));;
            if (isset($id)) {
                $href .= "/$id";
            }
        }
        if (!isset($id)) {
            $id = 0;
        }

        $classes_actions = '';
        if (!empty($action[5])) {
            $action[5] = (array)$action[5];
            foreach ($action[5] as $type_action => $impact_action) {
                if (is_int($type_action)) {
                    $type_action = $impact_action;
                    $impact_action = null;
                }
                switch ($type_action) {
                    case 'view':
                    case 'form':
                        // HTML is loaded in the modal window
                        $classes_actions .= ' action-view';
                        break;
                    case 'default-view':
                        // This is the button that brings the view by default
                        // Only one such button is allowed (the first one declared)
                        if (!$default_view) {
                            $classes_actions .= ' action-view-default';
                            $default_view = true;
                        }
                        break;
                    case 'dblclick':
                        // This is the button that would be called in case the user
                        // double-clicks on a element of the main list
                        // Only one such button is allowed (the first one declared)
                        if (!$double_click) {
                            $classes_actions .= ' action-double-click-handler';
                            $double_click = true;
                        }
                        break;
                    case 'action':
                        // Some process is launched without impact on the display
                        $classes_actions .= ' action-launch-process';
                        break;
                    case 'modify':
                        // The content of the current page is modified by this action
                        $classes_actions .= ' action-modify';
                        break;
                    case 'confirm-action':
                        // Some process is launched without impact on the display
                        // but a confirmation is required first
                        $classes_actions .= ' action-confirm action-confirm-launch-process';
                        break;
                    case 'confirm-modify':
                        // The content of the current page is modified by this action
                        // but a confirmation is required first
                        $classes_actions .= ' action-confirm action-confirm-modify';
                        break;
                    case 'confirm-delete':
                        // The current element is deleted by this action
                        // but a confirmation is required first
                        $classes_actions .= ' action-confirm action-confirm-danger action-confirm-delete';
                        break;
                    case 'download':
                        $classes_actions .= ' action-download';
                        break;
                    case 'download-pdf':
                        $classes_actions .= ' action-download-pdf';
                        break;
                    case 'print':
                        $classes_actions .= ' action-print';
                        break;
                    case 'print-pdf':
                        $classes_actions .= ' action-print-pdf';
                        break;
                    case 'positive':
                        $classes_actions .= ' action-positive';
                        break;
                    case 'negative':
                        $classes_actions .= ' action-negative';
                        break;
                    case 'post':
                        $classes_actions .= ' action-method-post';
                        break;
                    case 'confirm-post':
                        $classes_actions .= ' action-confirm action-confirm-method-post';
                        break;
                    default:
                        // Do nothing
                }
            }
        } else {
            $action[5] = array();
        }

        ?>
            <li class="text-center<?php echo $disabled.$classes_actions?><?php if (--$rang == 0) echo ' action-sep'?>" id="<?php echo $prefix_modal.$action[3]?>"><?php
            if (!isset($action[4])) {
                ?><a href="<?php echo $href?>" data-href-template="<?php echo $cible?>"<?php echo $target?>>
                <span class="glyphicon glyphicon-<?php echo $action[1]?>" aria-hidden="true"></span><span class="action-text">
                <br/><?php echo $nom?></span></a></li>
<?php       }
            elseif (isset($action[4])) {
                if (strpos($classes_actions, 'action-confirm-danger')) {
                    $btn_type = 'btn-danger';
                }
                else {
                    $btn_type = 'btn-warning';
                }
                ?><a href="<?php echo $href?>" data-href-template="<?php echo $cible?>" class="action-button-conf" data-target="#popup-<?php echo $controleur.'-'.$methode.'-'.(++$numero)?>">
                <span class="glyphicon glyphicon-<?php echo $action[1]?>" aria-hidden="true"></span><span class="action-text">
                <br/><?php echo $nom?></span></a></li>
<?php
                $ancre = anchor($action[0]."/$id",$nom,'class="btn '.$btn_type.' btn-confirm-action" role="button"');
                $modales[] = <<<EOD
    <div class="modal fade action-confirmation-modal" id="popup-$controleur-$methode-$numero" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-$controleur-$methode-$numero" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel-$controleur-$methode-$numero">Confirmation d'opération</h4>
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
}?>
        </ul>
    </div>
<?php
// création des fenêtres modales de confirmation
foreach($modales as $m) {
    echo $m;
}?>
<?php } ?>