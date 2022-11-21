(function () {
    'use strict';

    angular
        .module('appraiserpal.core')
        .config(config);

    config.$inject = [
        "DSHttpAdapterProvider",
        "stripeProvider"
    ];

    /** @ngInject */
    function config(DSHttpProvider, stripeProvider) {
        angular.extend(DSHttpProvider.defaults, {
            //base Path to all queries JSDATA
            basePath: "/api",
            cacheResponse: false,
            bypassCache: true
        });
        stripeProvider.setPublishableKey(STRIPE_KEY);
    }

})();
