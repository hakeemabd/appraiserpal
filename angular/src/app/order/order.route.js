(function () {
  'use strict';

  angular.module('appraiserpal.order').config(routerConfig);

  routerConfig.$inject = [
    "$stateProvider"
  ];

  /** @ngInject */
  function routerConfig($stateProvider) {
    $stateProvider.state('home.order', {
      url: 'order',
      templateUrl: 'app/order/order.html'
    });

  }

})();
