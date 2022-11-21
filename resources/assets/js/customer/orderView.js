CJMA.OrderView = function (exports, di) {
    var BOOTSTRAP_BREAKPOINT_MEDIUM = 992,
        BOOTSTRAP_BREAKPOINT_WIDESCREEN_PHONE = 768,
        CUSTOM_BREAKPOINT_PORTRAIT_PHONE = 540,
        $ = di.get('jq'),
        app = di.get('app'),
        tpl = di.get('tpl'),
        swiperSlider = di.get('swiperSlider'),
        defaultConfig = {
            viewOrderRoute: "/order/{id}/show",
            editOrderRoute: "/order/{id}/edit",
            $textFullView: $('.panel-popup[data-full]'),
            modalFullText: {
                titleCls: '.panel-heading',
            }
        },
        config = {},
        sliders = {
            imageSlider: {
                name: 'imageSliderOrderView',
                sliderSel: '#photos .object-slider',
                modalView: true,
                modalConfig: {
                    cls: 'slider-view',
                    contentSel: '.img-sim',
                    titleEl: 'h5.h5'
                },
                sliderConfig: {
                    slidesPerView: 4,
                    spaceBetween: 20,
                    nextButton: '#photos .swiper-button-next',
                    prevButton: '#photos .swiper-button-prev',
                    breakpoints: {}
                }
            },
            comparablesSlider: {
                instance: null,
                name: 'comparablesSlider',
                sliderSel: '#comparables .object-slider',
                modalView: true,
                modalConfig: {
                    cls: 'slider-view',
                    contentSel: '.img-sim',
                    titleSel: 'h5.h5'
                },
                sliderConfig: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                    nextButton: '#comparables .swiper-button-next',
                    prevButton: '#comparables .swiper-button-prev',
                    breakpoints: {}
                }
            }
        },
        imageSliderBreakpoints = sliders.imageSlider.sliderConfig.breakpoints,
        comparablesSlideBreakpoints = sliders.comparablesSlider.sliderConfig.breakpoints;

    imageSliderBreakpoints[BOOTSTRAP_BREAKPOINT_MEDIUM] = {
        slidesPerView: 3,
        spaceBetween: 20
    };

    imageSliderBreakpoints[BOOTSTRAP_BREAKPOINT_WIDESCREEN_PHONE] = {
        slidesPerView: 2,
        spaceBetween: 20
    };

    imageSliderBreakpoints[CUSTOM_BREAKPOINT_PORTRAIT_PHONE] =
        comparablesSlideBreakpoints[BOOTSTRAP_BREAKPOINT_MEDIUM] = {
            slidesPerView: 1,
            spaceBetween: 20
        };

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);

        if (app.routeMatches(config.viewOrderRoute.replace("{id}", "\\d[0-9]*"))) {
            swiperSlider.init(sliders.imageSlider);
            swiperSlider.init(sliders.comparablesSlider);
            config.$textFullView.on('click', onTextFullViewClick);
        }
    };

    function onTextFullViewClick() {
        var el = $(this),
            modalEl = $(tpl.modalTpl({
                cls: config.$textFullView,
                title: $(config.modalFullText.titleCls, el).html(),
                content: el.data('full'),
                footer: false
            }));

        modalEl.modal('toggle');
        modalEl.on('hidden.bs.modal', $.proxy(onModalHidden, this, modalEl));
    }

    function onModalHidden(modalEl) {
        modalEl.remove();
    }

    return exports;
};