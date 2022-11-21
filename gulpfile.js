//process.env.DISABLE_NOTIFIER = true;
require('laravel-elixir-sass-compass');
var BOWER_COMPONENTS_PATH = '../../../bower_components/',
    elixir = require('laravel-elixir'),
    gulp = require('gulp'),
    run = require('gulp-run');

gulp.task('build', function () {
  return run("cd ./angular && gulp build && cd ../").exec().pipe(gulp.dest('output'));
});

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
  var commonFiles = [
    BOWER_COMPONENTS_PATH + 'jquery/dist/jquery.js',
    BOWER_COMPONENTS_PATH + 'jquery-validation/dist/jquery.validate.js',
    BOWER_COMPONENTS_PATH + 'jquery-validation/dist/additional-methods.js',
    BOWER_COMPONENTS_PATH + 'datatables.net/js/jquery.dataTables.js',
    BOWER_COMPONENTS_PATH + 'datatables.net-responsive/js/dataTables.responsive.js',
    BOWER_COMPONENTS_PATH + 'moment/min/moment.min.js',
    'common/ns.js',
    'common/DI.js',
    'common/App.js',
    'common/Loader.js',
    'common/Tpl.js',
    'common/SwiperSlider.js',
    'common/Form.js',
    'common/FlashMessage.js',
    'common/Confirmation.js',
    'common/Datagrid.js',
    'common/DropdownButton.js',
    'common/Toaster.js'
  ];

  var materialJS = ["initial.js",
    "jquery.easing.1.3.js",
    "animation.js",
    "velocity.min.js",
    "hammer.min.js",
    "jquery.hammer.js",
    "global.js",
    "collapsible.js",
    "dropdown.js",
    "leanModal.js",
    "materialbox.js",
    "parallax.js",
    "tabs.js",
    "tooltip.js",
    "waves.js",
    "toasts.js",
    "sideNav.js",
    "scrollspy.js",
    "forms.js",
    "slider.js",
    "cards.js",
    "chips.js",
    "pushpin.js",
    "buttons.js",
    "transitions.js",
    "scrollFire.js",
    "date_picker/picker.js",
    "date_picker/picker.date.js",
    "character_counter.js",
    "carousel.js"].map(function (file) {
    return BOWER_COMPONENTS_PATH + 'Materialize/js/' + file;
  });

  mix.scripts(commonFiles.concat([
    BOWER_COMPONENTS_PATH + 'jquery/dist/jquery.js',
    BOWER_COMPONENTS_PATH + 'jquery-validation/dist/jquery.validate.js',
    BOWER_COMPONENTS_PATH + 'jquery-validation/dist/additional-methods.js',
    BOWER_COMPONENTS_PATH + 'bootstrap-sass/assets/javascripts/bootstrap.js',
    BOWER_COMPONENTS_PATH + 'jquery.toaster/jquery.toaster.js',
    BOWER_COMPONENTS_PATH + 'datatables.net-bs/js/dataTables.bootstrap.min.js',
    BOWER_COMPONENTS_PATH + 'datatables.net-responsive-bs/js/responsive.bootstrap.js',
    BOWER_COMPONENTS_PATH + 'select2/dist/js/select2.full.js',
    BOWER_COMPONENTS_PATH + 'Swiper/dist/js/swiper.min.js',
    BOWER_COMPONENTS_PATH + 'underscore/underscore.js',
    BOWER_COMPONENTS_PATH + 'blueimp-file-upload/js/vendor/jquery.ui.widget.js',
    BOWER_COMPONENTS_PATH + 'blueimp-file-upload/js/jquery.fileupload.js',
    'common/TplMaterial.js',
    'common/CorsForm.js',
    'customer/lib/waypoints.min.js',
    'customer/lib/jquery.animateNumber.min.js',
    'customer/lib/waypoints-sticky.min.js',
    'customer/lib/retina.min.js',
    'customer/lib/jquery.magnific-popup.min.js',
    'customer/lib/jquery.ajaxchimp.min.js',
    'customer/legacy.js',
    'customer/landing.js',
    'customer/dashboard.js',
    'customer/customerEnv.js',
    'customer/orderView.js',
    'customer/profile.js',
    'customer/boot.js',
    'customer/init.js'
  ]), 'public/customer/js/all.js');

  mix.scripts(commonFiles.concat(materialJS, [
    BOWER_COMPONENTS_PATH + 'Swiper/dist/js/swiper.min.js',
    BOWER_COMPONENTS_PATH + 'underscore/underscore.js',
    BOWER_COMPONENTS_PATH + 'blueimp-file-upload/js/vendor/jquery.ui.widget.js',
    BOWER_COMPONENTS_PATH + 'blueimp-file-upload/js/jquery.iframe-transport.js',
    BOWER_COMPONENTS_PATH + 'blueimp-file-upload/js/jquery.fileupload.js',
    'admin/orderView.js',
    'common/TplMaterial.js',
    'common/CorsForm.js',
    'admin/GroupCrud.js',
    'admin/boot.js'
  ]), 'public/admin/js/all.js');

  mix.scripts(commonFiles.concat(materialJS, [
    BOWER_COMPONENTS_PATH + 'Swiper/dist/js/swiper.min.js',
    BOWER_COMPONENTS_PATH + 'underscore/underscore.js',
    BOWER_COMPONENTS_PATH + 'blueimp-file-upload/js/vendor/jquery.ui.widget.js',
    BOWER_COMPONENTS_PATH + 'blueimp-file-upload/js/jquery.iframe-transport.js',
    BOWER_COMPONENTS_PATH + 'blueimp-file-upload/js/jquery.fileupload.js',
    'worker/orderView.js',
    'common/TplMaterial.js',
    'common/CorsForm.js',
    'worker/WorkerDashboard.js',
    'worker/boot.js',
    'worker/init.js'
  ]), 'public/worker/js/all.js');

  mix.version(['public/customer/js/all.js', 'public/admin/js/all.js', 'public/worker/js/all.js']);

  mix.compass('*.scss', 'public/styles/', {
    require: ['compass', 'bootstrap-sass'],
    config_file: "config.rb",
    sass: "resources/assets/scss",
    output_style: 'expanded',
    relative_assets: true,
    comments: true,
    preferred_syntax: 'scss'
  });
});