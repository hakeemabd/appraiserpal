CJMA.CustomerEnv = function (exports, di) {
    var $ = di.get('jq'),
        app = di.get('app'),
        defaultConfig = {},
        config = {};

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        initPlugins();
    };

    function initPlugins() {
        $('.navbar-default').waypoint('sticky', {
            offset: 30
        });
    }

    return exports;
};