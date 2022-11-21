(function () {
  'use strict';

  angular
    .module('appraiserpal.fileManager.cloneFileManager')
    .config(routerConfig);

  routerConfig.$inject = [
    "$stateProvider"
  ];

  function routerConfig($stateProvider) {
      $stateProvider
        .state('home.fileManager.clone', {
            url: '/clone',
            templateUrl: 'app/fileManager/cloneFileManager/templates/fm.html',
            controller: 'CloneFileManagerController',
            controllerAs: 'cloneFileManagerCtrl'
        })
  }

})();
