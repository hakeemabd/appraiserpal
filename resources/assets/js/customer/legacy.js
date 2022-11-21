CJMA.Legacy = function (exports, di) {
    var $ = di.get('jq'),
        app = di.get('app'),
        defaultConfig = {
            route: '^/$'
        },
        config = {};

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        if (app.routeMatches(config.route)) {
            initPlugins();
        }
    };

    function initPlugins() {
        // need clean after approving landing page
        /* ==========================================================================
         Sub Form
         ========================================================================== */

        //$('#mc-form').ajaxChimp({
        //    language: 'cm',
        //    url: 'http://csmthemes.us3.list-manage.com/subscribe/post?u=9666c25a337f497687875a388&id=5b881a50fb'
        //    //http://xxx.xxx.list-manage.com/subscribe/post?u=xxx&id=xxx
        //});
        //
        //
        //$.ajaxChimp.translations.cm = {
        //    'submit': 'Submitting...',
        //    0: '<i class="fa fa-envelope"></i> Awesome! We have sent you a confirmation email',
        //    1: '<i class="fa fa-exclamation-triangle"></i> Please enter a value',
        //    2: '<i class="fa fa-exclamation-triangle"></i> An email address must contain a single @',
        //    3: '<i class="fa fa-exclamation-triangle"></i> The domain portion of the email address is invalid (the
        // portion after the @: )', 4: '<i class="fa fa-exclamation-triangle"></i> The username portion of the email
        // address is invalid (the portion before the @: )', 5: '<i class="fa fa-exclamation-triangle"></i> This email
        // address looks fake or invalid. Please enter a real email address' };

        /* ==========================================================================
         litebox
         ========================================================================== */

        $('.litebox-hero, .litebox-tour').magnificPopup({
            type: 'iframe'
        });


        /* ==========================================================================
         Number animation
         ========================================================================== */


        $('.welcome-message').waypoint(function () {

            var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');

            $('.total-number-1').animateNumber({
                number: 50, //change value here
                numberStep: comma_separator_number_step
            }, 6000);

        }, {
            offset: '80%'

        });


        /* ==========================================================================
         Feature image absolute position height fix
         ========================================================================== */

        $(window).load(function () {
            var featureImg = function () {
                $(".features div[class='row'] .col-md-7").each(function () {
                    var newHeight = 0,
                        $this = $(this);
                    $.each($this.children(), function () {
                        newHeight += $(this).height();
                    });
                    $this.height(newHeight);
                });
            };


            featureImg();


            $(window).on("resize", function () {
                featureImg();
            });


        });


        /* ==========================================================================
         Smooth scroll
         ========================================================================== */

        $("a[href*=#]:not([href=#])").click(function () {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html,body').animate({

                        scrollTop: (target.offset().top - 90)
                    }, 1000);
                    return false;
                }
            }
        });
    }

    return exports;
};