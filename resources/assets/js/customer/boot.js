(function ($, di, pageMessages) {

    var app = app || {};

    di.add('jq', $);
    di.add('toaster', CJMA.Toaster(app.toaster = {}, di));
    di.add('messageProvider', di.get('toaster').show);

    app = CJMA.App({}, di);

    di.add('app', app);

    CJMA.Loader(app.loader = {}, di);
    di.add('loader', app.loader);

    CJMA.Form(app.form = {}, di);
    di.add('form', app.form);

    di.add('datagrid', CJMA.Datagrid({}, di));

    CJMA.Tpl(app.tpl = {}, di);
    di.add('tpl', app.tpl);

    CJMA.SwiperSlider(app.swiperSlider = {}, di);
    di.add('swiperSlider', app.swiperSlider);

    di.add('legacy', CJMA.Legacy({}, di));
    di.add('customerEnv', CJMA.CustomerEnv({}, di));
    di.add('landing', CJMA.Landing({}, di));
    di.add('dashboard', CJMA.Dashboard({}, di));
    di.add('orderView', CJMA.OrderView({}, di));
    di.add('flashmsg', CJMA.FlashMessage({}, di, pageMessages));
    di.add('profile', CJMA.Profile({}, di));
    CJMA.CorsForm(app.corsForm = {}, di);
    di.add('corsForm', app.corsForm);

})(jQuery, CJMA.DI, window.__MSG || null);