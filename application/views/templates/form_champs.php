<?php
// mise en forme des champs
// Indexes de valeur des champs:
// $c[0] => Label (STRING)
// $c[1] => Type (STRING)
// $c[2] => Pour le champs a multiples valeurs (ARRAY)
//          (index "1" = propriété des objets dans le $listes_valeurs qui correspond au champ,
//           index "2" = propriété des objets dans le $listes_valeurs qui correspond au champ,
// $c[3] => Obligatoire (TRUE / FALSE)
// $c[4] => Liste d'attributs (nom / valeur) (ARRAY)
$champs = array();
$viewable_fields = viewable_fields($controleur, 'write');
foreach($descripteur['champs']as $k=>$c) {

    $is_viewable = verify_viewable_field($k, $viewable_fields);

    if($is_viewable) {
        $texte = '';
        $attributs = (isset($c[4])) ? $c[4] : array();
        switch($c[1]) {
            case "password-c":
            case "password":
                $texte = $this->load->view('templates/ctrl_standard',array(
                    'type'=>'password','controle'=>$k,'label'=>$c[0],'valeur'=>'','obligatoire'=>$c[3],'attr'=>$attributs),true);
                break;
                break;
            case "text":
            case "datetime":
            case "datetime-local":
            case "month":
            case "time":
            case "week":
            case "number":
            case "email":
            case "url":
            case "search":
            case "color":
                $texte = $this->load->view('templates/ctrl_standard',array(
                    'type'=>$c[1],'controle'=>$k,'label'=>$c[0],'valeur'=>$values->$k,'obligatoire'=>$c[3],'attr'=>$attributs),true);
                break;
            case "upload":
                $texte = $this->load->view('templates/ctrl_upload',array(
                    'controle'=>$k,'label'=>$c[0],'types'=>$c[2],'obligatoire'=>$c[3],'attr'=>$attributs),true);
                break;
            case "date":
                $texte = $this->load->view('templates/ctrl_date',array(
                    'controle'=>$k,'label'=>$c[0],'valeur'=>$values->$k,'obligatoire'=>$c[3],'attr'=>$attributs),true);
                break;
            case "rich-editor":
            case "textarea":
                $texte = $this->load->view('templates/ctrl_textarea',array(
                    'controle'=>$k,'label'=>$c[0],'valeur'=>$values->$k,'obligatoire'=>$c[3],'attr'=>$attributs),true);
                break;
            case "checkbox":
            case "checkbox-h":
                $checked = ($values->$k == 1)?" checked=\"checked\"":"";
                $texte = $this->load->view('templates/ctrl_checkbox',array(
                    'type'=>$c[1],'controle'=>$k,'label'=>$c[0],'checked'=>$checked,'obligatoire'=>$c[3],'attr'=>$attributs),true);
                break;
            case "radio":
            case "radio-h":
                $texte = $this->load->view('templates/ctrl_radio',array(
                    'type'=>$c[1],'controle'=>$k,'label'=>$c[0],'valeurs'=>$listes_valeurs->$k,'valeur'=>$values->$k,'id'=>$c[2][1],'value'=>$c[2][2],'obligatoire'=>$c[3],'attr'=>$attributs),true);
                break;
            case "select":
                $texte = $this->load->view('templates/ctrl_select',array(
                    'controle'=>$k,'label'=>$c[0],'valeurs'=>$listes_valeurs->$k,'valeur'=>$values->$k,'id'=>$c[2][1],'value'=>$c[2][2],'obligatoire'=>$c[3],'attr'=>$attributs),true);
                break;
    		case "select-multiple":
               	$texte = $this->load->view('templates/ctrl_select_multiple',array(
               	'controle'=>$k,'label'=>$c[0],'valeurs'=>$listes_valeurs->$k,'valeur'=>$values->$k,'id'=>$c[2][1],'value'=>$c[2][2],'obligatoire'=>$c[3],'attr'=>$attributs),true);
               	break;
            case "multiple-upload":
                $texte = $this->load->view('templates/ctrl_multiple_upload',array(
                    'controle'=>$k,'label'=>$c[0],'types'=>$c[2],'obligatoire'=>$c[3],'attr'=>$attributs),true);
                break;
            case "hidden":
                $texte = '<input type="'.$c[1].'" name="'.$k.'" id="'.$k.'" class="form-control" value="'.$values->$k.'" />';
                break;
            default:
                log_message('error',"type de champ ".$c[1]." inconnu");
        }
        $champs[$k] = $texte;
    }
}
?>