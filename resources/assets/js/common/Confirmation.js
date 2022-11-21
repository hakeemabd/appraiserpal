CJMA.Confirmation = function (exports, di) {
    var $ = di.get('jq'),
        app = di.get('app'),
        defaultConfig = {
            parent: document.body,
            header: {
                "generic": "Are you sure?",
                "remove": "Removal confirmation"
            },
            message: {
                "generic": "You can't undo this!",
                "remove": "Are you sure you want to remove this?"
            },
            buttons: {
                "generic": ['no', 'yes'],
                "remove": ['no', 'yes']
            }
        },
        config = {},
        $modal,
        $header,
        $content,
        $footer,
        btnListeners = {};

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
    };

    exports.generic = function (options) {
        showModal(options, 'generic');
    };

    exports.wantRemove = function (options) {
        showModal(options, 'remove');
    };

    function showModal(options, type) {
        options = options || {};
        if (!$modal) {
            renderMarkup();
        }
        $header.html(options.header || config.header[type]);
        $content.html(options.message || config.message[type]);
        $footer.html(renderButtons(options.buttons || config.buttons[type]));
        btnListeners.yes = options.onYes;
        btnListeners.no = options.onNo;
        $modal.openModal();
    }

    function renderButtons(buttons) {
        var btnHtml = '', buttonText;
        for (var i = 0; i < buttons.length; i++) {
            buttonText = buttons[i][0].toUpperCase() + buttons[i].substr(1);
            btnHtml += '<a data-action="' + buttons[i] + '" class=" modal-action modal-close waves-effect waves-green btn">' + buttonText + '</a>';
        }
        return btnHtml;
    }

    function renderMarkup() {
        $modal = $('<div class="modal">' +
            '<div class="modal-content">' +
            '<h4>Modal Header</h4>' +
            '<p></p>' +
            '</div>' +
            '<div class="modal-footer">' +
            '</div>' +
            '</div>');
        $header = $('h4', $modal);
        $content = $('.modal-content p', $modal);
        $footer = $('.modal-footer', $modal);
        $(config.parent).append($modal);
        $footer.click(onBtnClick);
    }

    function onBtnClick(e) {
        if (!$(e.target).hasClass('btn')) {
            return true;
        }
        e.preventDefault();
        $modal.closeModal();
        var action = $(e.target).data('action');
        if ($.isFunction(btnListeners[action])) {
            btnListeners[action].apply(this, arguments);
        }
    }

    return exports;
};