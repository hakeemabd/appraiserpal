CJMA.Loader = function (exports, di) {
    var $ = di.get('jq'),
        defaultConfig = {
            activateClass: 'active',
            loaderMarkup: ['<div class="app-loader valign center-align preloader-wrapper big">',
                '    <div class="spinner-layer spinner-blue-only">',
                '        <div class="circle-clipper left">',
                '            <div class="circle"></div>',
                '        </div><div class="gap-patch">',
                '            <div class="circle"></div>',
                '        </div><div class="circle-clipper right">',
                '            <div class="circle"></div>',
                '        </div>',
                '    </div>',
                '</div>'].join("\n"),
            maskClass: 'teal lighten-5',
            getContainer: function (el) {
                return $(el).parent();
            }
        },
        config = {},
        elements = {};
    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
    };
    exports.show = function (el) {
        var $el = $(el),
            elId = Math.random();
        if (!elements[$el.data('loader-id')]) {
            $el.data('loader-id', elId);
            elements[elId] = {
                el: $(el)
            };
        }
        else {
            elId = $el.data('loader-id');
        }
        if (!elements[elId].hasMarkup) {
            addMarkup(config.getContainer($el));
            elements[elId].hasMarkup = true;
        }
        config.getContainer($el).children('.load-mask').show().children('.app-loader').addClass(config.activateClass);
    };
    exports.hide = function (el) {
        var $el = $(el),
            elId = $el.data('loader-id');
        if (!elements[elId]) {
            console.warn("Trying to remove loader without adding it beforehand for element", el);
            return;
        }
        config.getContainer($el).children('.load-mask').hide().children('.app-loader').removeClass(config.activateClass)
    };
    function addMarkup($container) {
        $container.append(['<div class="load-mask valign-wrapper ', config.maskClass, '" style="opracity:0.8">', config.loaderMarkup, '</div>'].join("\n"));
        $container.addClass('loader-container');
    }
};