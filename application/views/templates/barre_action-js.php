<script>
var actionMenuBar = (function($) {

    // Media for event broadcasting
    var media = 'localStorage';

    $(document).ready(function() {
        // Passage en AJAX des actions qui ont besoin d'une confirmation
        // et qui modifient les informations dans la liste
        // (c.à.d des actions qui nécessitent que la page soit mise à jour)
        $(".action-bar .action-confirm-modify a").click(function(ev){
            ev.preventDefault();
            actionMenuBar.confirmModify(this);
        })

        // Passage en AJAX des actions qui ont besoin d'une confirmation
        // et qui suppriment les informations dans la liste
        // (c.à.d des actions qui nécessitent que la page soit mise à jour)
        $(".action-bar .action-confirm-delete a").click(function(ev){
            ev.preventDefault();
            actionMenuBar.confirmModify(this);
        })

        // Passage en AJAX des actions qui ont besoin d'une confirmation
        // et qui lancent un processur
        // (c.à.d des actions qui nécessitent que la page soit mise à jour)
        $(".action-bar .action-confirm-launch-process a").click(function(ev){
            ev.preventDefault();
            actionMenuBar.confirmAction(this);
        })

        // Passage en AJAX des actions qui modifient les informations sur la ligne
        // (c.à.d des actions qui nécessitent que la page soit mise à jour)
        $(".action-bar .action-modify a").click(function(ev){
            ev.preventDefault();
            actionMenuBar.modify(this);
        })

        // Passage en AJAX des actions qui lancent un processus
        $(".action-bar .action-launch-process a").click(function(ev){
            ev.preventDefault();
            actionMenuBar.action(this);
        })

        // Passage en AJAX des actions qui demandent un téléchargement
        $(".action-bar .action-download a").click(function(ev){
            ev.preventDefault();
            actionMenuBar.download(this);
        })

        // Passage en AJAX des actions qui demandent une impression d'un document PDF
        $(".action-bar .action-print-pdf a").click(function(ev){
            ev.preventDefault();
            actionMenuBar.print(this, 'pdf');
        })

        // Actions that need to submit as POST
        $(".action-bar .action-method-post a").click(function(ev) {
            ev.preventDefault();
            actionMenuBar.post(this);
        })

        // Actions that need to submit as POST after confirmation
        $(".action-bar .action-confirm-method-post a").click(function(ev) {
            ev.preventDefault();
            actionMenuBar.confirmPost(this);
        })
    });

    return {
        /**
         * DataTable helper object
         *
         * This needs to be set by the script that handles the DataTable setup on the page.
         *
         * @property Object
         */
        datatable: null,

        /**
         * Switches buttons on / off according to a status function
         *
         * @param {(JQuery|string)} button - jQuery object or selector of the action buttons
         * @param {(function|boolean)} status - A function to determine the status of a button
         * @param {(function|string)} [params] - URL parameter(s) to append to the URL found in the matching
         *                                       "data-href-template" attribute, or a function that generates them
         */
        switch: function(selector, status, params) {
            $(selector).each(function(i) {
                var enable, _params;

                if (typeof status === 'function') {
                    enable = status(this);
                } else {
                    enable = !!status;
                }

                if (typeof params === 'function') {
                    _params = params(this);
                } else {
                    _params = params;
                }

                if (enable) {
                    actionMenuBar.enable(this, _params);
                } else {
                    actionMenuBar.disable(this);
                }
            });

            return this;
        },

        /**
         * Disables a button
         *
         * The URL of the anchor is also set to "#"
         *
         * @param {(JQuery|string)} button - jQuery object or selector of the action button
         */
        disable: function(button) {
            $(button).find('a').attr('href', '#');
            $(button).addClass('disabled');

            return this;
        },

        /**
         * Enables a button
         *
         * @param {(JQuery|string)} button - jQuery object or selector of the action button
         * @param {string} [params] - URL parameter(s) to append to the URL found in the matching "data-href-template" attribute
         */
        enable: function(button, params) {
            if (arguments.length > 1) {
                this.href(button, params);
            }
            $(button).removeClass('disabled');

            return this;
        },

        /**
         * Gets or sets the href of the button
         *
         * @param {(JQuery|string)} button - jQuery object or selector of the action button
         * @param {string} [params] - URL parameter(s) to append to the URL found in the matching "data-href-template" attribute
         */
        href: function(button, params) {
            if (arguments.length == 1) {
                return $(button).find('a').attr('href');
            }
            // Only update the URL if it's not the blank anchor "#"
            var href = this.hrefTemplate(button);
            if (href && href != '#') {
                var target = href + '/' + params;
                $(button).find('a').attr('href', target);
            }

            return this;
        },

        /**
         * Gets or sets the href template of the button
         *
         * @param {(JQuery|string)} button - jQuery object or selector of the action button
         * @param {string} [url] - New URL template for "data-href-template" attribute
         */
        hrefTemplate: function(button, url) {
            if (arguments.length == 1) {
                return $(button).find('a').data("href-template");
            }

            $(button).find('a').data("href-template", url);
            return this;
        },

        /**
         * Checks if the action menu bar item is enabled
         *
         * @param {(JQuery|string)} a - jQuery object or selector for &lt;a> element of the button
         */
        isEnabled: function(a) {
            return !$(a).parents('li').hasClass("disabled");
        },

        /**
         * Resets the buttons in the action menu bar to their initial state
         */
        reset: function() {
            $('.action-bar .disabled-by-default').each(function (i) {
                actionMenuBar.disable(this);
            });
        },

        /**
         * Reloads the current page after a given timeout
         *
         * Helper function
         */
        reload: function(timeout = 2000) {
            setTimeout(function() {
                location.reload(true) ;
            }, timeout) ;
        },

        /**
         * Handles the pseudo-events generated by the server regarding records
         */
        handleServerEvent: function(event) {
            // Do we have a DataTable list on which to apply the actions of the events?
            var helper = actionMenuBar.datatable;
            if (!helper || !event) {
                return;
            }
            if (event.controleur != helper.controleur) {
                /**
                 *  Do nothing, this page is not concerned by that event
                 *
                // Reload the datatable
                helper.reload();
                 */
            } else {
                switch (event.type) {
                    case 'recordadd':
                        if (event.id) {
                            // Insert a new row in the datatable
                            helper.load(event.id);
                        } else {
                            helper.reload();
                        }
                        break;
                    case 'recordchange':
                        if (event.id) {
                            helper.reload(event.id);
                        } else {
                            helper.reload();
                        }
                        break;
                    case 'recorddelete':
                        // Remove a row from the datatable
                        // Disable the buttons if the row being removed was the one currently selected
                        if (helper.selected(event.id)) {
                            actionMenuBar.reset();
                        }
                        helper.unload(event.id);
                        break;
                }
            }
        },

        /**
         * Callback for jQuery.ajax().done()
         */
        updateOnSuccess: function(data) {
            if (!data.success) {
                return;
            }

            var event = (data.data && data.data.event) ? data.data.event : null;

            if (event && actionMenuBar.datatable) {
                if (Object.prototype.toString.call(event) === '[object Array]') {
                    for (var i = 0; i < event.length; ++i) {
                        actionMenuBar.handleServerEvent(event[i]);
                    }
                } else {
                    actionMenuBar.handleServerEvent(event);
                }
            } else if (event && event.redirect) {
                console.log("redirect");
                switch (event.type) {
                    case 'recorddelete':
                        window.location.replace(event.redirect);
                        break;
                    default:
                        window.location.href = event.redirect;
                }
            //} else {
            //    // Reload the whole page
            //    actionMenuBar.reload();
            }
        },

        /**
         * Returns a callback handler for jQuery.ajax().done()
         *
         * @param string type - Type of document to print. One of:
         * <ul>
         *     <li>pdf</li>
         *     <li>image</li>
         * </ul>
         * @return function
         */
        printOnSuccess: function(type) {
            switch (type) {
                case 'pdf':
                case 'image':
                    break;
                default:
                    return function(data) {};
            }
            return function(data) {
                if (data.success && data.data && data.data.url) {
                    printJS(data.data.url, type);
                }
            }
        },

        /**
         * Returns a callback for jQuery.ajax().done()
         * @return function
         */
        redirectOnSuccess: function(target) {
            return function(data) {
                if (!data.success) {
                    return;
                }
                if (data.data && data.data.url) {
                    if (target && target != '_self') {
                        console.log("redirectOnSuccess")
                        window.open(data.data.url, target);
                    } else {
                        window.location.href = data.data.url;
                    }
                }
            }
        },

        /**
         * Returns a callback for jQuery.ajax().done()
         * @return function
         */
        hideModalOnSuccess: function(modal) {
            return function(data) {
                if (data.success) {
                    actionMenuBar.hideModal(modal);
                }
            }
        },

        hideModal: function(modal) {
            $(modal).modal('hide');
        },

        /**
         * Returns a callback for jQuery.ajax().done()
         * @return function
         */
        executeOnSuccess: function(callback) {
            return function(data) {
                if (callback && data.success) {
                    callback();
                }
            }
        },

        /**
         * Callback for jQuery.ajax().done()
         */
        displayNotification: function(data) {
            if (data.message && data.notif) {
                notificationWidget.show(data.message, data.notif);
            }
        },

        /**
         * Returns a callback handler for jQuery.ajax().done()
         *
         * @param string myMedia - Broadcasting method (for now, only 'localStorage' is implemented)
         * @return function
         */
        broadcastOnSuccess: function(myMedia) {
            // Default to current broadcasting media
            if (arguments.length == 0) {
                myMedia = media;
            }

            return function(data) {
                if (data.success && data.data && data.data.event) {
                    var key = 'ajax_response';
                    var json = JSON.stringify(data);
                    try {
                        window[myMedia] && window[myMedia].setItem(key, json);
                    } catch (e) {
                    }
                }
            }
        },

        /**
         * Returns an event handler
         *
         * @param string media - Broadcasting method (for now, only 'localStorage' is implemented)
         * @return function
         */
        listenBroadcast: function(myMedia) {
            // Default to current media
            if (arguments.length == 0) {
                myMedia = media;
            }

            return function(event) {
                // Unwrap jQuery event
                if (event.originalEvent) {
                    event = event.originalEvent;
                }

                if (event.type == 'storage'
                    && /^ajax_response$/.test(event.key)) {
                    var newValue = event.newValue;
                    if (typeof newValue === 'string') {
                        try {
                            var data = JSON.parse(newValue);
                            actionMenuBar.displayNotification(data);
                            actionMenuBar.updateOnSuccess(data);
                        } catch (e) {
                        }
                    }
                }
            }
        },

        /**
         * Loads button action in the modal window
         *
         * The anchor must contain the URL (in the "href" attribute) of the content to load
         *
         * @param {(JQuery|string)} a - jQuery object or selector for &lt;a> element of the button
         * @param {(JQuery|string)} modal - jQuery object or selector for the modal window
         */
        view: function(a, modal) {
            if (this.isEnabled(a)) {
                this.loadInModal(a, modal);
            }
            return this;
        },

        /**
         * Loads anchor's URL in the modal window
         *
         * The anchor must contain the URL (in the "href" attribute) of the content to load.
         *
         * When the content is loaded in the modal, the event "shown.bt.modal" is triggered on it.
         *
         * @param {(JQuery|string)} a - jQuery object or selector for a &lt;a> element
         * @param {(JQuery|string)} modal - jQuery object or selector for the modal window
         */
        loadInModal: function(a, modal) {
            var extraParameters = {
                url: $(a).attr('href')
            };
            var contentLoaded = function() {
                $(modal).trigger('shown.bt.modal', extraParameters);
            };

            $.ajax({
                type: "GET",
                url: $(a).attr('href')+'/ajax',
                dataType: "json"
            }).done(
                this.displayNotification,
                function(data) {
                    if (data.success) {
                        $(modal).find(".modal-body").html(data.data);
                        $(modal).modal();
                    }
                },
                this.executeOnSuccess(contentLoaded)
            );
        },

        /**
         * Loads a form in the modal window
         *
         * The anchor must contain the URL (in the "href" attribute) of the form to load
         *
         * @param {(JQuery|string)} a - jQuery object or selector for &lt;a> element of the button
         * @param {(JQuery|string)} modal - jQuery object or selector for the modal window
         */
        form: function(a, modal) {
            return this.view(a, modal);
        },

        /**
         * Asynchronously launches a remote action
         *
         * A notification is displayed upon completion of the action.
         * The anchor must contain the URL (in the "href" attribute) of the action to run
         *
         * @param {(JQuery|string)} a - jQuery object or selector for &lt;a> element of the button
         * @param function callback - A function to run after a successful action
         */
        action: function(a, callback) {
            if (this.isEnabled(a)) {
                var url = $(a).attr('href')+'/ajax';
                this.reset();
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: "json"
                }).done(
                    this.displayNotification,
                    this.executeOnSuccess(callback),
                    this.broadcastOnSuccess(media)
                );
            }
            return this;
        },

        /**
         * Prompts for confirmation then calls the callback
         *
         * @param {(JQuery|string)} a - jQuery object or selector for &lt;a> element of the button
         * @param function callback - A function to run after a positive confirmation
         */
        confirm: function(a, callback) {
            var target = $(a).data('target');
            var href = $(a).attr('href');

            $(target).on('shown.bs.modal', function() {
                $(this).find("a.btn-confirm-action").attr('href', href).one('click', function(ev) {
                    ev.preventDefault();
                    callback(target, this);
                });
            }).on('hide.bs.modal', function() {
                $(this).find("a.btn-confirm-action").attr('href', '#').off('click');
            }).modal('show');

            return this;
        },

        /**
         * Prompts for confirmation then asynchronously launches a remote action
         *
         * A notification is displayed upon completion of the action.
         * The anchor must contain the URL (in the "href" attribute) of the action to run
         *
         * @param {(JQuery|string)} a - jQuery object or selector for &lt;a> element of the button
         * @param function callback - A function to run after a successful action
         */
        confirmAction: function(a, callback) {
            if (this.isEnabled(a)) {
                this.confirm(a, function(modal, a) {
                    var url = $(a).attr('href')+'/ajax';
                    actionMenuBar.reset();
                    $.ajax({
                        type: "POST",
                        url: url,
                        dataType: "json"
                    }).done(
                        actionMenuBar.hideModal(modal),
                        actionMenuBar.displayNotification,
                        actionMenuBar.updateOnSuccess,
                        actionMenuBar.executeOnSuccess(callback),
                        actionMenuBar.broadcastOnSuccess(media)
                    );
                });
            }
            return this;
        },

        /**
         * Asynchronously launches the download request of a file
         *
         * A notification might be displayed (if the file does not exist for instance), then,
         * upon success, the web browser is redirected to the direct download URL.
         * The anchor must contain the URL (in the "href" attribute) of the download to request.
         *
         * In this handler, success refers to the request for download, no the download itself.
         *
         * @param {(JQuery|string)} a - jQuery object or selector for &lt;a> element of the button
         * @param function callback - A function to run after a successful request for download
         */
        download: function(a, callback) {
            if (this.isEnabled(a)) {
                var url = $(a).attr('href')+'/ajax';
                var target = $(a).attr('target');
                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "json"
                }).done(
                    this.displayNotification,
                    this.updateOnSuccess,
                    this.executeOnSuccess(callback),
                    this.broadcastOnSuccess(media),
                    this.redirectOnSuccess(target)
                );
            }
            return this;
        },

        /**
         * Asynchronously launches a POST request
         *
         * A notification might be displayed, then, upon success, the web browser is redirected
         * to the URL returned.
         * The anchor must contain the URL (in the "href" attribute) of the POST to request.
         *
         * @param {(JQuery|string)} a - jQuery object or selector for &lt;a> element of the button
         * @param function callback - A function to run after a successful request for download
         */
        post: function(a, callback) {
            if (this.isEnabled(a)) {
                var url = $(a).attr('href')+'/ajax';
                var target = $(a).attr('target');
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: "json"
                }).done(
                    this.displayNotification,
                    this.updateOnSuccess,
                    this.executeOnSuccess(callback),
                    this.broadcastOnSuccess(media),
                    this.redirectOnSuccess(target)
                );
            }
            return this;
        },

        /**
         * Prompts for confirmation then asynchronously launches a POST request
         *
         * A notification might be displayed upon completion of the action.
         * The anchor must contain the URL (in the "href" attribute) of the POST to request.
         *
         * @param {(JQuery|string)} a - jQuery object or selector for &lt;a> element of the button
         * @param function callback - A function to run after a successful action
         */
        confirmPost: function(a, callback) {
            if (this.isEnabled(a)) {
                this.confirm(a, function(modal, a) {
                    var url = $(a).attr('href')+'/ajax';
                    var target = $(a).attr('target');
                    $.ajax({
                        type: "POST",
                        url: url,
                        dataType: "json"
                    }).done(
                        actionMenuBar.hideModal(modal),
                        actionMenuBar.displayNotification,
                        actionMenuBar.updateOnSuccess,
                        actionMenuBar.executeOnSuccess(callback),
                        actionMenuBar.broadcastOnSuccess(media),
                        actionMenuBar.redirectOnSuccess(target)
                    );
                });
            }
            return this;
        },

        /**
         * Asynchronously launches the print request of a document
         *
         * A notification might be displayed (if the file does not exist for instance), then,
         * upon success, the web browser should prompt to print the requested document.
         * The anchor must contain the URL (in the "href" attribute) of the document to print.
         *
         * In this handler, success refers to the request for document, no the print itself.
         *
         * @param {(JQuery|string)} a - jQuery object or selector for &lt;a> element of the button
         * @param function callback - A function to run after a successful request for download
         */
        print: function(a, type, callback) {
            if (this.isEnabled(a)) {
                var url = $(a).attr('href')+'/ajax';
                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "json"
                }).done(
                    this.displayNotification,
                    this.updateOnSuccess,
                    this.executeOnSuccess(callback),
                    this.broadcastOnSuccess(media),
                    this.printOnSuccess(type)
                );
            }
            return this;
        },

        /**
         * Asynchronously launches a remote action with side effects
         *
         * A notification is displayed upon completion of the action, then, upon success,
         * the current page is reloaded or information is updated to show the changes.
         * The anchor must contain the URL (in the "href" attribute) of the action to run
         *
         * @param {(JQuery|string)} a - jQuery object or selector for &lt;a> element of the button
         * @param function callback - A function to run after a successful action
         */
        modify: function(a, callback) {
            if (this.isEnabled(a)) {
                var url = $(a).attr('href')+'/ajax';
                this.reset();
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: "json"
                }).done(
                    this.displayNotification,
                    this.updateOnSuccess,
                    this.executeOnSuccess(callback),
                    this.broadcastOnSuccess(media)
                );
            }
            return this;
        },

        /**
         * Prompts for confirmation then asynchronously launches a remote action
         * with side effects
         *
         * A notification is displayed upon completion of the action, then, upon success,
         * the current page is reloaded to show the changes.
         * The anchor must contain the URL (in the "href" attribute) of the action to run
         *
         * @param {(JQuery|string)} a - jQuery object or selector for &lt;a> element of the button
         * @param function callback - A function to run after a successful action
         */
        confirmModify: function(a, callback) {
            if (this.isEnabled(a)) {
                this.confirm(a, function(modal, a) {
                    var url = $(a).attr('href')+'/ajax';
                    actionMenuBar.reset();
                    $.ajax({
                        type: "POST",
                        url: url,
                        dataType: "json"
                    }).done(
                        actionMenuBar.displayNotification,
                        actionMenuBar.hideModal(modal),
                        actionMenuBar.updateOnSuccess,
                        actionMenuBar.executeOnSuccess(callback),
                        actionMenuBar.broadcastOnSuccess(media)
                    );
                });
            }
            return this;
        }
    }
})(jQuery);

</script>