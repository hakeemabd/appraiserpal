CJMA.Profile = function (exports, di) {
    var $ = di.get('jq'),
        app = di.get('app'),
        tpl = di.get('tpl'),
        form = di.get('form'),
        defaultConfig = {
            route: 'profile',
            initProfileEditingSel: '#editProfile',
            modalConfig: {
                cls: 'slider-view',
                contentSel: null,
                title: 'Update Personal Information',
                content: $('#editProfileTpl').html(),
                footer: false
            }
        },
        config = {},
        forms = {
            'editProfile': {
                cls: '.edit-profile-form',
                msg: 'Can\'t edit your profile. Please check your credentials',
                successUrl: window.location.pathname
            }
        };

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        if (app.routeMatches(config.route)) {
            initProfileEditing();
        }
    };

    function initProfileEditing() {
        $(config.initProfileEditingSel).on('click', editProfile);
    }

    function initEditProfileForm(selector) {
        form.addForm({
            form: selector,
            errorMessage: forms.editProfile.msg,
            successUrl: forms.editProfile.successUrl
        });
    }

    function editProfile() {
        var modalEl = $(tpl.modalTpl({
            cls: '',
            title: config.modalConfig.title,
            content: config.modalConfig.content,
            footer: config.modalConfig.footer
        }));

        $('select', modalEl).select2({
            minimumResultsForSearch: -1
        });

        modalEl.modal('toggle');
        modalEl.on('shown.bs.modal', $.proxy(onModalShown, this, modalEl));
        modalEl.on('hidden.bs.modal', $.proxy(onModalHidden, this, modalEl));
    }

    function onModalShown(modalEl) {
        initEditProfileForm($(forms.editProfile.cls, modalEl));
    }

    function onModalHidden(modalEl) {
        modalEl.remove();
    }

    return exports;
};