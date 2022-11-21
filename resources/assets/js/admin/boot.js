(function ($, materialize, di) {
    di.autoInit = true;
    di.add('jq', $);
    di.add('messageProvider', materialize.toast);

    var app = CJMA.App({}, di);
    di.add('app', app);

    CJMA.Loader(app.loader = {}, di);
    di.add('loader', app.loader);

    CJMA.Form(app.form = {}, di);
    di.add('form', app.form);

    di.add('dropdownButton', CJMA.DropdownButton({}, di));
    di.add('confirmation', CJMA.Confirmation({}, di));
    di.add('datagrid', CJMA.Datagrid({}, di));
    di.add('group-crud', CJMA.GroupCrud({}, di));

    CJMA.CorsForm(app.corsForm = {}, di);
    di.add('corsForm', app.corsForm);

    CJMA.TplMaterial(app.TplMaterial = {}, di);
    di.add('tpl', app.TplMaterial);

    CJMA.SwiperSlider(app.swiperSlider = {}, di);
    di.add('swiperSlider', app.swiperSlider);
    di.add('orderView', CJMA.OrderView({}, di));
})(jQuery, Materialize, CJMA.DI);