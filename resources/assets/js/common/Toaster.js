CJMA.Toaster = function (exports, di) {
    var $ = di.get('jq'),
        defaultConfig = {
            message: null,
            title: 'Notification',
            priority: 'info',
            settings: {
                toaster: {
                    css: {
                        top: 'auto',
                        right: 'auto',
                        bottom: '10px',
                        left: '10px'
                    }
                },
                toast: {}
            }
        },
        config = {};

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
    };

    exports.show = function (mess, duration) {
        $.extend(config, {
            message: mess
        });

        $.extend(config.settings, {
            timeout: duration
        });

        $.toaster(config);
    };

    return exports;
};
