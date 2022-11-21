CJMA.App = function (exports, di) {
    var $ = di.get('jq'),
        messenger = di.get('messageProvider');

    var defaultConfig = {
            getCsrfToken: function () {
                return $('meta[name="csrf-token"]').attr('content');
            },
            dropDownSelector: '.dropdown-button',
            dropDownOnHover: true,
            sidenavButtonSelector: '.button-collapse',
            selectSelector: 'select',
            parallaxSelector: null,
            modalTrigger: '.modal-trigger',
            materialTabs: '.material-tabs',
            navigationSelector: '#nav-mobile'
        },
        config;

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        injectCsrfToken();
        initEvents();
        initControls();
    };

    exports.navigate = function (url) {
        window.location = url;
    };

    exports.msg = function (mess) {
        if (!messenger) {
            console.warn('No messenger defined!');
        }
        messenger(mess, 4000);
    };

    exports.routeMatches = function (regExp) {
        if (!(regExp instanceof RegExp)) {
            regExp = new RegExp(regExp, 'i');
        }
        var match = window.location.pathname.match(regExp);
        return match && match.length > 0;
    };

    function injectCsrfToken() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': config.getCsrfToken()
            }
        });
    }

    function setMenuCls() {
        var width = $(window).width(),
            mobileBreakpoint = 992,
            mobileNavClass = 'mobile-side-nav',
            nav = $(config.navigationSelector);

        if (width <= mobileBreakpoint) {
            nav.addClass(mobileNavClass);
        } else {
            nav.removeClass(mobileNavClass);
        }
    }

    function initEvents() {
        $(window).on('resize', setMenuCls);
        $(window).on('orientationchange', setMenuCls);
    }

    function initControls() {
        if (config.dropDownSelector !== null && typeof $.fn.dropdown == 'function') {
            $(config.dropDownSelector).dropdown({hover: config.dropDownOnHover});
        }
        if (config.sidenavButtonSelector !== null && typeof $.fn.sideNav == 'function') {
            $(config.sidenavButtonSelector).sideNav();
        }
        if (config.selectSelector !== null && typeof $.fn.material_select == 'function') {
            $(config.selectSelector).material_select();
        }
        if (config.parallaxSelector !== null && typeof $.fn.parallax == 'function') {
            $(config.parallaxSelector).parallax();
        }
        if (config.modalTrigger !== null && typeof $.fn.leanModal == 'function') {
            $(config.modalTrigger).leanModal();
        }
        if (config.materialTabs !== null && typeof $.fn.tabs == 'function') {
            $(config.materialTabs).tabs();
        }

        setMenuCls();
    }

    return exports
};