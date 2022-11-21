CJMA.FlashMessage = function (exports, di, data) {
    var $ = di.get('jq'),
        app = di.get('app'),
        defaultConfig = {},
        config = {};
    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        $(onPageLoaded);
    };

    function onPageLoaded() {
        if (!data || !data.text) {
            return;
        }
        app.msg(data.text);
    }

    return exports;
};