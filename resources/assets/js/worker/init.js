(function (di) {
    di.get('app').init({
        parallaxSelector: '.parallax',
        tooltipedSelector: '.tooltiped'
    });
    di.get('loader').init();
    di.get('form').init();
    di.get('flashmsg').init();
    di.get('confirmation').init();
    di.get('dropdownButton').init();
    di.get('datagrid').init();
    di.get('corsForm').init();
    di.get('dashboard').init();
    di.get('orderView').init();
})(CJMA.DI);