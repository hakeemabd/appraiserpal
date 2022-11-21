(function () {
  'use strict';

  angular.module('appraiserpal').config(routerConfig);

  /** @ngInject */
  function routerConfig($stateProvider, $urlRouterProvider) {
    $stateProvider.state('home', {
      url: '/',
      templateUrl: 'app/layout/layout.html',
      controller: "LayoutController",
      controllerAs: "layoutController"
    });

    $urlRouterProvider.otherwise('/');
  }

})();
