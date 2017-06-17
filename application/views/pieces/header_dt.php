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
	<link href="<?php echo base_url('assets/css/bootstrap-multiselect.css')?>" rel="stylesheet">
	
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

    <!-- DataTables CSS -->
    <link href="<?php echo base_url('assets/css/jquery.dataTables.css')?>" rel="stylesheet" />
    <!-- <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" /> -->
    <link href="https://cdn.datatables.net/scroller/1.4.2/css/scroller.dataTables.min.css" rel="stylesheet" />
    <link href="<?php echo base_url('assets/css/dt_customisations.css')?>" rel="stylesheet" />
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.3/summernote.css" rel="stylesheet">
    <!-- Datetime picker jquery plugin -->
    <link href="<?php echo base_url('assets/css/jquery.datetimepicker.css')?>" rel="stylesheet" /> 
    <link href="<?php echo base_url('assets/css/jquery.periodpicker.min.css')?>" rel="stylesheet" />
    <!-- <link href="<?php echo base_url('assets/css/jquery.timepicker.min.css')?>" rel="stylesheet" /> -->

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
    <?php 
    if(isset($menu_extra)): 
        if(is_array($menu_extra)):
    ?>
    <div class="row cdf" style="margin-top: 20px;">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
              <?php foreach($menu_extra as $row): ?>
              <?php $active = $row['url'] === current_url() ? 'class="active"' : "";?>
              <li <?php echo $active;?>><a href="<?php echo $row['url'];?>"><?php echo $row['name'];?></a></li>
            <?php endforeach;?>
            </ul>
        </div>
    </div>
    <?php 
        endif;
    endif;
    ?>

<?php if (!empty($barre_action)) {
        $this->load->view('templates/barre_action',array('barre'=>$barre_action));
    }?>
    <!-- corps -->
