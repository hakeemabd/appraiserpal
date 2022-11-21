(function () {
    'use strict';

    angular
        .module('appraiserpal.order.createOrder')
        .config(routerConfig);

    routerConfig.$inject = [
        "$stateProvider"
    ];

    /** @ngInject */
    function routerConfig($stateProvider) {
        $stateProvider
            .state('home.order.create', {
                url: '/create',
                templateUrl: 'app/order/createOrder/templates/createOrder.html',
                controller: 'CreateOrderController',
                controllerAs: 'createOrderCtrl'
            })
            .state('home.order.edit', {
                url: '/edit/:orderId',
                templateUrl: 'app/order/createOrder/templates/createOrder.html',
                controller: 'CreateOrderController',
                controllerAs: 'createOrderCtrl'
            });
    }

})();
