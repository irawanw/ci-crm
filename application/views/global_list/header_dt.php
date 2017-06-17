<?php include "application/config/droits.php";?>
<?php include "application/views/_data/menus.php";?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title;?></title>
	<link href="<?php echo base_url()?>min/?g=css" rel="stylesheet" />
</head>
<body style="padding-top: 56px">
<div id="bandeau_alerte" class="alert alert-info alert-dismissible hidden" role="alert">
    <p id="texte_alerte">Texte d'alerte</p>
</div>
<div class="container">

    <?php $profil = $this->session->profil;
    if (! isset($profil)) {
        $profil = 'public';
    }
    if ($title != '') {
        $debut = strpos($title,'[');
        if ($debut !== false) {
            $fin = strpos($title,']');
            $nom_session = substr($title,$debut+1,$fin-$debut-1);
            $info_session = $this->session->$nom_session;
            $title = substr($title,0,$debut).$info_session.substr($title,$fin+1);
        }
    }
    $this->load->view('templates/menu',array('menus'=>$menus,'droits'=>$droits,'profil'=>$profil))?>
    <div class="row cdf">
        <!--
        <div class="col-md-8">
            <div><?php echo $this->contexte->chemin_de_fer($menu)?></div>
        </div>
        <div class="col-md-4">
            <p><?php echo $title?></p>
        </div>
        -->
        <p style="text-align:center"><?php echo $title?></p>

    </div>
<?php if (isset($barre_action)) {
        $this->load->view('templates/barre_action',array('menus'=>$menus,'droits'=>$droits,'profil'=>$profil,'barre'=>$barre_action));
    }?>
    <!-- corps -->