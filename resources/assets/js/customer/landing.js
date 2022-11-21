CJMA.Landing = function (exports, di) {
    var BOOTSTRAP_MOBILE_WIDTH_BREAKPOINT = 753,
        $ = di.get('jq'),
        app = di.get('app'),
        form = di.get('form'),
        loaderWrapCls = 'loader-container',
        authPopoverVisibility = false,
        $documentEl = $(document),
        $windowEl = $(window),
        defaultConfig = {
            "authFormWrapCls": '.nav .popover .popover-content',
            "backToLoginCls": '.back-to-login',
            "recoverPasswordLinkCls": '.recover-password',
            "openFormButtonSelector": $(".navbar-right"),
            "loginPopoverSelector": $(".popover-login"),
            "registrationPopoverInitSelector": $('#registartion'),
            "headerNavbarSelector": $("ul.nav.navbar-nav.navbar-right")
        },
        config = {},
        forms = {
            "loginForm": {
                'cls': '.popover .auth-form',
                'msg': 'Login failed. Please check your credentials.',
                'tpl': $('#login-tpl').html()
            },
            "recoveryForm": {
                'cls': '.popover .recoverForm',
                'msg': 'Can\'t recover your password. Please check your email.',
                'tpl': $('#recover-password-tpl').html()
            },
            "registrationForm": {
                'cls': '.popover .registration-form',
                'msg': 'Can\'t register your account.',
                'tpl': $('#registration-tpl').html()
            }
        };

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        initPlugins();
        initEvents();
    };

    function initPlugins() {
        initAuthPopover();
    }

    function initEvents() {
        $windowEl.on('resize', onWindowResize.bind(this, $windowEl.width()));
        config.loginPopoverSelector.on('click', onPopoverLoginInitBtnClick);
        config.headerNavbarSelector.on('click', config.recoverPasswordLinkCls, onRecoverPasswordLinkClick);
        config.headerNavbarSelector.on('click', config.backToLoginCls, onBackToLoginClick);
        config.registrationPopoverInitSelector.on('click', onRegistrationInitBtnClick);
        config.loginPopoverSelector.on('shown.bs.popover', afterPopoverContentShow);
        config.registrationPopoverInitSelector.on('shown.bs.popover', afterRegistrationPopoverShow);
    }

    function initForm(formConfig) {
        form.addForm({
            form: $(formConfig.cls),
            errorMessage: formConfig.msg,
            successUrl: '/'
        });
    }

    function isPopoverVisible() {
        if (authPopoverVisibility) {
            return true;
        }

        return false;
    }

    function initAuthPopover() {
        config.loginPopoverSelector.popover({
            trigger: 'manual',
            container: config.headerNavbarSelector,
            placement: getHeaderPopoverPlacement,
            html: true,
            content: forms.loginForm.tpl + forms.recoveryForm.tpl
        });

        config.registrationPopoverInitSelector.popover({
            trigger: 'manual',
            container: config.headerNavbarSelector,
            placement: getHeaderPopoverPlacement,
            html: true,
            content: forms.registrationForm.tpl
        });
    }

    function onRegistrationInitBtnClick() {
        config.registrationPopoverInitSelector.popover('toggle');
        config.loginPopoverSelector.popover('hide');
    }

    function getHeaderPopoverPlacement() {
        if ($documentEl.width() < BOOTSTRAP_MOBILE_WIDTH_BREAKPOINT) {
            return 'top';
        }

        return 'bottom';
    }

    //@todo to many form init
    function afterPopoverContentShow() {
        var popoverContentEl = $(config.authFormWrapCls);

        if (!popoverContentEl.hasClass(loaderWrapCls)) {
            popoverContentEl.addClass(loaderWrapCls);
        }

        initForm(forms.loginForm);
        initForm(forms.recoveryForm);
    }

    function afterRegistrationPopoverShow() {
        initForm(forms.registrationForm);
    }

    function onWindowResize(prevWidth) {
        if (isPopoverVisible() && (prevWidth != $(this).width())) {
            config.loginPopoverSelector.popover('hide');
            authPopoverVisibility = false;
        }

        config.registrationPopoverInitSelector.popover('hide');
    }

    function onPopoverLoginInitBtnClick() {
        config.loginPopoverSelector.popover('toggle');
        config.registrationPopoverInitSelector.popover('hide');
        authPopoverVisibility = !authPopoverVisibility;
    }

    function onRecoverPasswordLinkClick() {
        $(forms.loginForm.cls).toggle();
        $(forms.recoveryForm.cls).toggle();
    }

    function onBackToLoginClick() {
        $(forms.loginForm.cls).toggle();
        $(forms.recoveryForm.cls).toggle();
    }

    return exports;
};