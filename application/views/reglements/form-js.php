<script type="text/javascript">

    var reglements_form = {

        factureId: 0,
        enteredManually: false,

        init: function () {
            var fac_id = reglements_form.factureId;
            if (fac_id > 0) {
                $("#checkbox-F" + fac_id).prop("checked", true);
            } else {
                $("#check-all").prop("checked", false);
                $("#check-all").click();
            }
            var amount = $('#rgl_montant').val();
            if (amount.length == 0) {
                var totalSelected = reglements_form.facturesSelected();
                $("#rgl_montant").val(totalSelected.factures.toFixed(2));
            }
            reglements_form.typeReglement($("#rgl_type").val());
            reglements_form.balance();
        },

        balance: function() {
            var amounts = this.facturesSelected();
            var avoirs = amounts.avoirs;
            var due = amounts.factures;
            if (avoirs > 0.0) {
                $("#rgl_montant").val('0.00');
                reglements_form.enteredManually = false;
            } else if ($("#rgl_montant").val() == '' || !reglements_form.enteredManually) {
                $("#rgl_montant").val(due.toFixed(2).toString());
            }

            if ($("#rgl_type").val() == "4") {
                // Avoirs
                $("#rgl_avoirs").parent().parent().parent().removeClass("hidden");
                $("#rgl_montant").parent().parent().parent().addClass("hidden");
                $("#rgl_banque").parent().parent().parent().addClass("hidden");
                $("#rgl_cheque").parent().parent().parent().addClass("hidden");
            } else {
                $("#rgl_avoirs").parent().parent().parent().addClass("hidden");
                $("#rgl_montant").parent().parent().parent().removeClass("hidden");
                if ($("#rgl_type").val() == "2" || $("#rgl_type").val() == "5") {
                    // "Espèces" et "Pertes et profits"
                    $("#rgl_banque").parent().parent().parent().addClass("hidden");
                    $("#rgl_cheque").parent().parent().parent().addClass("hidden");
                } else {
                    // Chèque et virement
                    $("#rgl_banque").parent().parent().parent().removeClass("hidden");
                    $("#rgl_cheque").parent().parent().parent().removeClass("hidden");
                }
            }

            $("#rgl_du").val(due.toFixed(2).toString());
            $("#rgl_avoirs").val(avoirs.toFixed(2).toString());

            var paid = this.amountPaid();
            $("#rgl_regle").val(Math.min(due, paid).toFixed(2).toString());
            this.tropVerseAndCompensation(paid, due);

            return Math.max(paid - due, 0);
        },

        // Changement de type de règlement
        typeReglement: function (type) {
            if (type == "4" || type == 4) {
                // "Avoir" sélectionné
                $("#rgl_montant").val('0.00');
                reglements_form.enteredManually = false;
            } else {
                // Type de règlement autre qu'"Avoir", décocher tous les avoirs
                $("#content-factures tr:has(input:checked)").each(function () {
                    if ($(this).find('td:eq(3)').text() == "Avoir") {
                        $(this).find("input:checked").prop("checked", false);
                    }
                });
                $("#rgl_avoirs").val('0.00');
                if (reglements_form.amountPaid() == 0) {
                    reglements_form.enteredManually = false;
                    $("#rgl_montant").val('');
                }
            }
        },

        /**
         * Totalise le montant "solde dû" pour le type donné
         *
         * @param type "Facture" ou "Avoir"
         * @return {number}
         */
        sum: function(type) {
            var total = 0.0;

            $("#content-factures tr:has(input:checked)").each(function () {
                var type_text = $(this).find('td:eq(3)').text();
                var amount = parseFloat($(this).find('td:eq(5)').text().replace(',', '.'));

                if (type_text == type) {
                    total += amount;
                }
            });
            return total;
        },

        facturesSelected: function () {
            var total = reglements_form.sum('Facture');
            var avoir = reglements_form.sum('Avoir');

            if (avoir > 0) {
                $("#rgl_type").val("4");
            }

            this.setPieces();
            return {
                factures: total,
                avoirs: avoir,
            };
        },

        loadFactures: function (callback) {
            var clientId = $('#rgl_client').val();
            var enseigneId = $('#rgl_societe_vendeuse').val();

            // Clear the field to reset to total of selected factures upon load (see init() above)
            $("#rgl_montant").val('');
            reglements_form.enteredManually = false;
            if (clientId && enseigneId) {
                $.get('<?php echo site_url("reglements/get_factures/");?>/' + clientId + '/' + enseigneId, function (response) {
                    reglements_form.embedFactures(response);
                    reglements_form.init();
                    if (callback) {
                        callback();
                    }
                }, "json");
            }
        },

        setPieces: function () {
            var pieces = '';
            var sep = '';
            $("#content-factures tr input:checked").each(function () {
                var id = $(this).val();
                pieces = pieces + sep + id;
                sep = ',';
            });
            $("#pieces").val(pieces);
        },

        tropVerseAndCompensation: function (paid, due) {
            var balance = Math.max(paid - due, 0);

            if (balance > 0) {
                var balance_str = balance.toFixed(2).toString();
                $("#trop_verse").val(balance_str);
                $("#trop_verse").parent().parent().parent().removeClass("hidden");
                $("input[name='compensation']").parent().parent().parent().removeClass("hidden");
            } else {
                $("#trop_verse").val(0);
                $("#trop_verse").parent().parent().parent().addClass("hidden");
                $("input[name='compensation']").parent().parent().parent().addClass("hidden");
            }
        },

        amountPaid: function (amount) {
            if (arguments.length == 0) {
                var verse = parseFloat($('#rgl_montant').val().replace(",", "."));
                var avoirs = parseFloat($('#rgl_avoirs').val().replace(",", "."));
                return Math.max((isNaN(verse)) ? 0.0 : verse, (isNaN(avoirs)) ? 0.0 : avoirs);
            }
            amount = parseFloat(amount);
            if (isNaN(amount)) {
                amount = 0.0;
            }
            this.tropVerseAndCompensation(amount, this.facturesSelected());
        },

        embedFactures: function (response) {
            var header = "<h3>Table Facture</h3>";
            var table = '<table class="table table-border">' +
                '<thead><tr>' +
                '<th><input type="checkbox" id="check-all" /></th>' +
                '<th>Date</th>' +
                '<th>Montant TTC</th>' +
                '<th>Type de pièce</th>' +
                '<th>N° de pièce</th>' +
                '<th>Solde dû</th>' +
                '</tr></thead>' +
                '<tbody id="content-factures">';
            if (response.length > 0) {
                $.each(response, function () {
                    var checkbox = '<input id="checkbox-' + this.id + '" type="checkbox" class="checkbox" value="' + this.id + '" />';
                    table += '<tr><td>' + checkbox + '</td><td>' + this.date + '</td><td>' + this.montant + '</td><td>' + this.type + '</td><td>' + this.numero + '</td><td>' + this.du + '</td></tr>';
                });
            } else {
                table += '<tr><td colspan="6">Pas de factures</td></tr>';
            }
            table += '</tbody></table>';

            var html = '<div class="form-group div-factures"><div class="col-md-3"></div><div class="col-md-9" id="table-factures">' + header + table + '</div></div>';

            var divFactures = $('#rgl_client').parent().parent();
            divFactures.parent().find('.div-factures').remove();
            divFactures.after(html);
        }
    };

    $(document).ready(function () {

        // sélection / désélection des lignes
        $(document).on("click", "#check-all", function (e) {
            var checked = $(this).is(":checked");
            $('.checkbox').prop("checked", checked);
            reglements_form.balance();
        });
        // sélection / désélection d'une ligne
        $(document).on("click", '.checkbox', function (e) {
            reglements_form.balance();
        });

        // détermination du trop versé éventuel
        $("#template-modal-detail").on("change", "#rgl_montant", function (ev) {
            //if (reglements_form.enteredManually) {
                //reglements_form.amountPaid($(this).val().replace(",", "."));
                reglements_form.balance();
            //}
        });

        // changement de client
        $("#template-modal-detail").on("change", "#rgl_client", function (ev) {
            reglements_form.loadFactures();
        });

        // changement de type de règlement
        $("#template-modal-detail").on("change", "#rgl_type", function (ev) {
            reglements_form.typeReglement($(this).val());
            reglements_form.balance();
        });

        // changement d'enseigne
        $("#template-modal-detail").on("change", "#rgl_societe_vendeuse", function (ev) {
            reglements_form.loadFactures();
        });

        // Bind a callback for when the content in the modal is shown
        // Note: This is not the bootstrap event, but the one triggered by the barre action!
        $('#template-modal-detail').on('shown.bt.modal', function (ev, extra) {
            var divFactures = $('#rgl_client');
            if (divFactures.length > 0) {
                reglements_form.loadFactures();
            }
        });

        $('#template-modal-detail').on('keypress', '#rgl_montant', function (ev) {
            reglements_form.enteredManually = ($(this).val() != '');
        });
    });
</script>