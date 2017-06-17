<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reprise des factures</title>

    <!-- Bootstrap -->
    <link href="<?php echo base_url('assets/css/bootstrap.min.css')?>" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Reprise des factures</h1>
            <p><a href="<?php echo site_url('reprise3'); ?>">Reprise des clients</a></p>
        </div>
    </div>
    <?php echo form_open('reprise2/factures', array('class' => 'form-horizontal'))?>
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="fichier">Fichier à importer</label>
                <div class="col-sm-9">
                    <select name="fichier" id="fichier" class="form-control" required="required">
                        <option value="">(choisissez)</option>
                        <?php foreach($fichiers as $rep0 => $fichier0) {?>
                            <?php if (is_array($fichier0)) {?>
                                <?php if (count($fichier0)) {?>
                                    <optgroup label="<?php echo htmlspecialchars($rep0);?>">
                                        <?php foreach($fichier0 as $rep1 => $fichier1) {?>
                                            <option value="<?php echo htmlspecialchars($rep0.$fichier1)?>"><?php echo htmlspecialchars($fichier1)?></option>
                                        <?php }?>
                                    </optgroup>
                                <?php }?>
                            <?php } else {
                                if (isset($fichier) AND $fichier == $fichier0) {
                                    $sel = ' selected="selected"';
                                }
                                else {
                                    $sel = '';
                                }?>
                                <option value="<?php echo htmlspecialchars($fichier0)?>"<?php echo $sel?>><?php echo htmlspecialchars($fichier0)?></option>
                            <?php }?>
                        <?php }?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="enseigne">Enseigne</label>
                <div class="col-sm-9">
                    <select name="enseigne" id="enseigne" class="form-control" required="required">
                        <option value="">(choisissez)</option>
                        <?php foreach($enseignes as $e) {
                            if (isset($enseigne) AND $enseigne == $e->scv_id) {
                                $sel = ' selected="selected"';
                            }
                            else {
                                $sel = '';
                            }?>
                            <option value="<?php echo $e->scv_id?>"<?php echo $sel?>><?php echo htmlspecialchars(strip_tags($e->scv_nom))?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <br />
            <br />
            <p class="text-center"><button type="submit" name="action" value="tester" class="btn btn-primary">Tester la reprise</button> <button type="submit" name="action" value="effectuer" class="btn btn-danger">Effectuer la reprise</button></p>
        </div>
    </div>
    <?php echo form_close()?>
    <br />
    <br />
    <?php foreach ($messages as $message) {?>
        <div class="alert alert-<?php echo $message['type']?>" role="alert">
            <?php if (isset($message['html'])) {
                echo $message['html'];
            } else {?>
                <p><?php echo str_replace("\n", "<br />", htmlspecialchars($message['text'])); ?></p>
            <?php }?>
        </div>
    <?php }?>

    <div class="row">
        <div class="col-md-12">
            <p>Liste des colonnes attendues dans le fichier importé :</p>
            <ol style="list-style-type: upper-alpha">
                <?php foreach ($colonnes as $header) {
                    echo '<li>'.str_replace(' ', '&nbsp;', htmlspecialchars($header)).'</li>';
                }
                ?>
            </ol>
        </div>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo base_url('assets/js/bootstrap.min.js')?>"></script>

</body>
</html>
