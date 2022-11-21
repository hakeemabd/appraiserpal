(function () {
  'use strict';

  angular.module('appraiserpal.fileManager').config(routerConfig);

  routerConfig.$inject = [
    "$stateProvider"
  ];

  /** @ngInject */
  function routerConfig($stateProvider) {
    $stateProvider.state('home.fileManager', {
      url: 'fileManager',
      templateUrl: 'app/fileManager/fileManager.html'
    });

  }

})();
