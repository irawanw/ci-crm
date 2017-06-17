<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Tâches urgentes</h3>
            </div>
            <div class="panel-body">
                Tâches urgentes non démarrées ou en cours
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Alertes</h3>
            </div>
            <div class="panel-body">
                Alertes non acquittées
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Tâches</h3>
            </div>
            <div class="panel-body">
                Agenda des tâches
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Messages non lus</h3>
            </div>
            <div class="panel-body">
<?php foreach($messages as $m) {
    echo        '<p>Le '.formatte_date($m->msg_envoi)." de $m->utl_login : $m->msg_amorce...</p>\n";
}?>
            </div>
        </div>
    </div>
</div>