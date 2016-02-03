/**
 * @module package/quiqqer/twitter/bin/controls/AuthSettings
 * @author www.pcsg.de (Henning Leutz)
 *
 */
define('package/quiqqer/twitter/bin/controls/AuthSettings', [

    'qui/QUI',
    'qui/controls/Control'

], function (QUI, QUIControl) {
    "use strict";

    return new Class({
        Extends: QUIControl,
        Type   : 'package/quiqqer/twitter/bin/controls/AuthSettings',

        Binds: [
            '$onImport'
        ],

        initialize: function (options) {
            this.parent(options);

            this.addEvents({
                onImport: this.$onImport
            });
        },

        /**
         * event: on inject
         */
        $onImport: function () {
            var Elm = this.getElm(),
                Url = Elm.getElement('.callback-url');

            var callbackUrl = window.location.toString().replace(URL_SYS_DIR, '') +
                              URL_OPT_DIR +
                              'quiqqer/twitter/bin/auth/callback.php';

            if (Url) {
                Url.set('html', callbackUrl);
            } else {
                console.info(callbackUrl);
            }
        }
    });
});