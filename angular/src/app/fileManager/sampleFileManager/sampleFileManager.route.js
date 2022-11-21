(function () {
  'use strict';

  angular
    .module('appraiserpal.fileManager.sampleFileManager')
    .config(routerConfig);

  routerConfig.$inject = [
    "$stateProvider"
  ];

  function routerConfig($stateProvider) {
      $stateProvider
        .state('home.fileManager.sample', {
            url: '/sample',
            templateUrl: 'app/fileManager/sampleFileManager/templates/fm.html',
            controller: 'SampleFileManagerController',
            controllerAs: 'sampleFileManagerCtrl'
        })
  }

})();
