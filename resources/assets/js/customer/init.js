(function (di) {
    di.get('app').init();
    di.get('loader').init({
        loaderMarkup: '<div class="app-loader">Loading...</div>',
        maskClass: ''
    });

    di.get('form').init();
    di.get('toaster').init();

    di.get('customerEnv').init();
    di.get('legacy').init();
    di.get('landing').init();
    di.get('datagrid').init();
    di.get('corsForm').init();
    di.get('dashboard').init();
    di.get('orderView').init();
    di.get('flashmsg').init();
    di.get('profile').init();
})(CJMA.DI);