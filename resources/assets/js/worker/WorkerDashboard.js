CJMA.WorkerDashboard = function (exports, di) {
    var $ = di.get('jq'),
        app = di.get('app'),
        loader = di.get('loader'),
        defaultConfig = {
            route: 'dashboard',
            availabilitySelector: '#availability'
        },
        config = {};

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        if (app.routeMatches(config.route)) {
            initHandlers();
        }
    };

    function initHandlers() {
        $(config.availabilitySelector).change(onAvailabilityChange);
    }

    function onAvailabilityChange(e) {
        var $checkbox = $(e.target),
            available = $checkbox.is(':checked') ? 1 : 0,
            boundBox = $checkbox.parents('.card');
        loader.show(boundBox);
        $.ajax({
            url: $checkbox.data('source') + '/' + available
        }).always(function () {
            loader.hide(boundBox);
        });
    }

    return exports;
};