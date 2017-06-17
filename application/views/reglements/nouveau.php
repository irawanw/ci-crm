<div class="panel panel-default">
    <div class="panel-body">
        <?php echo form_open(site_url("reglements/nouveau/$client/$facture"))?>
        <div class="row">
            <div class="col-md-6">
                <div style="border:1px solid lightgrey;padding:2px;margin-bottom:10px;">
                    <?php
                    echo form_hidden('__form','x');
                    echo form_input(array('type'=>'hidden','name'=>'pieces','id'=>'pieces','value'=>''));
                    include 'application/views/templates/form_champs.php';
                    foreach($champs as $c) {
                        echo $c;
                    }?>
                </div>
            </div>
            <div class="col-md-6">
                <div id="div_trop_verse" class="hidden">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="trop_verse">Trop versé</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="trop_verse" name="trop_verse" placeholder="0" readonly>
                        </div>
                    </div>
                    <br /><br />
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="compensation"></label>
                        <div class="col-sm-9">
                            <label class="radio-inline">
                                <input type="radio" name="compensation" id="comp_avoir" value="avoir" checked="checked"> Transformer en avoir
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="compensation" id="comp_pp" value="profits_pertes"> Profits et pertes
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <p class="text-center"><button type="submit" id="enregistrer" class="btn btn-primary enregistrer-reglement-ajax">Enregistrer</button></p>
        <?php echo form_close()?>
    </div>
</div>
<div id="grid-reglement-detail"></div>

