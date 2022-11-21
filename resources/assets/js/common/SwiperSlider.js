CJMA.SwiperSlider = function (exports, di) {
    var SLIDER_SLIDE_CLS = '.swiper-slide',
        BOOTSTRAP_THEME = 'bootstrap',
        MATERIALIZE_THEME = 'materialize',
        $ = di.get('jq'),
        tpl = di.get('tpl'),
        imgTpl = _.template('<img src="<%= path %>" width="100%"/>'),
        defaultConfig = {
            name: null,
            sliderSel: null,
            modalView: false,
            imgCLs: '.img-sim',
            modalConfig: {
                cls: 'slider-view',
                contentSel: null,
                titleSel: null
            },
            sliderConfig: {
                slidesPerView: 4,
                spaceBetween: 20,
                nextButton: '.swiper-button-next',
                prevButton: '.swiper-button-prev',
            },
            theme: BOOTSTRAP_THEME
        },
        config = {};

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        initSlider();
    };

    exports.instance = null;

    function initSlider() {
        exports.instance = new Swiper(config.sliderSel, config.sliderConfig);

        if (config.modalView) {
            initSliderModalEvents();
        }
    }

    function initSliderModalEvents() {
        $(config.sliderSel + ' ' + SLIDER_SLIDE_CLS).on('click', onSlideClick);
    }

    function onSlideClick(e) {
        var el = $(e.currentTarget),
            imgPath = $(config.modalConfig.contentSel, el).data('img'),
            modalEl = $(tpl.modalTpl({
                cls: config.modalConfig.cls,
                title: $(config.modalConfig.titleSel, el).text(),
                content: imgTpl({
                    path: imgPath
                }),
                footer: false
            }));

        if (config.theme === BOOTSTRAP_THEME) {
            modalEl.modal('toggle');
            modalEl.on('hidden.bs.modal', $.proxy(onModalHidden, this, modalEl));
        } else if (config.theme === MATERIALIZE_THEME) {
            modalEl.appendTo("body");

            modalEl.openModal({
                dismissible: true,
                complete: $.proxy(onModalHidden, this, modalEl)
            });
        }

    }

    function onModalHidden(modalEl) {
        modalEl.remove();
    }

    return exports;
};
