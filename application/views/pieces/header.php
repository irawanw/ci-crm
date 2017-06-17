<?php include "application/config/droits.php";?>
<?php include "application/views/_data/menus.php";?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title;?></title>

    <!-- Bootstrap -->
    <link href="<?php echo base_url('assets/css/bootstrap.min.css')?>" rel="stylesheet">

    <!-- Common Kendo UI CSS -->
    <link href="<?php echo base_url('assets/styles/kendo.common.min.css')?>" rel="stylesheet" />
    <!-- Default Kendo UI theme CSS -->
    <link href="<?php echo base_url('assets/styles/kendo.bootstrap.min.css')?>" rel="stylesheet" />
    <!-- Personnalisation Kendo UI theme CSS -->
    <link href="<?php echo base_url('assets/css/crm.css')?>" rel="stylesheet" />

    <!-- Datetime picker jquery plugin -->
    <link href="<?php echo base_url('assets/css/jquery.datetimepicker.css')?>" rel="stylesheet" />
    <link href="<?php echo base_url('assets/css/jquery.periodpicker.min.css')?>" rel="stylesheet" />
    <!-- <link href="<?php echo base_url('assets/css/jquery.timepicker.min.css')?>" rel="stylesheet" /> -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
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
        <div class="col-md-7 cdf-row">
<?php $contexte = $this->contexte->chemin_de_fer($menu);
if ($contexte == '') $contexte = '&nbsp;';?>
            <p><?php echo $contexte?></p>
        </div>
        <div class="col-md-5 cdf-row">
            <p><?php echo $title?>&nbsp;</p>
        </div>
    </div>
<?php if (!empty($barre_action)) { ?>
</div> <!-- container -->
    <?php
    $param = array(
        'barre' => $barre_action
    );
    if (isset($barre_action->unitaire) AND isset($id)) {
        $param['id'] = $id;
    }
    $this->load->view('templates/barre_action', $param);
    ?>
<div class="container">
<?php }?>
    <!-- corps -->
