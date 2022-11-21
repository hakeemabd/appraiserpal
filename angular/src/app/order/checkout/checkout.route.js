(function () {
    'use strict';

    angular
        .module('appraiserpal.order')
        .config(routerConfig);

    routerConfig.$inject = [
        "$stateProvider"
    ];

    /** @ngInject */
    function routerConfig($stateProvider) {
        $stateProvider
            .state('home.order.checkout', {
                url: '/checkout/:orderId',
                templateUrl: 'app/order/checkout/checkout.html',
                controller: 'CheckoutController',
                controllerAs: 'checkoutCtrl'
            });
    }

})();
