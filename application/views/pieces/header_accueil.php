<?php include "application/config/droits.php"?>
<?php include "application/views/_data/menus.php"?>
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
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
        <!--
        .k-grid {
            font-size: 12px;
        }
        .k-grid td {
            line-height: 1em;
        }
        .rappel {
            background-color: #FF4444;
        }
        -->
    </style>
</head>
<body style="padding-top: 56px">
<div class="container">
    <?php $profil = $this->session->profil;
    if (! isset($profil)) {
        $profil = 'public';
    }
    $this->load->view('templates/menu',array('menus'=>$menus,'droits'=>$droits,'profil'=>$profil));?>
    <div class="row cdf">
        <div class="col-md-8">
            <div><?php echo $this->contexte->chemin_de_fer($menu)?></div>
        </div>
    </div>
<?php if (isset($barre_action)) {
        $this->load->view('templates/barre_action',array('menus'=>$menus,'droits'=>$droits,'profil'=>$profil,'barre'=>$barre_action));
    }?>
    <br />
    <div class="row">
        <div class="col-md-9">
            <div>&nbsp;</div>
        </div>
    </div>

    <!-- menu latéral -->
    <div class="row">
        <div class="col-md-2">
            <?php $this->load->view('templates/menu_lateral',array('menus'=>$menus,'profil'=>$profil))?>
        </div>

        <!-- corps -->
        <div class="col-md-10">
            <?php if ($title != '') {?>
            <h4 class="text-center"><?php echo $title?></h4>
            <?php }
            else {?>
            <br />
            <?php }?>