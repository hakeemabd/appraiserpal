(function () {
  'use strict';

  angular
    .module('appraiserpal.fileManager.imageLabelFileManager')
    .config(routerConfig);

  routerConfig.$inject = [
    "$stateProvider"
  ];

  function routerConfig($stateProvider) {
    $stateProvider
      .state('home.fileManager.imageLabel', {
        url         : '/image',
        templateUrl : 'app/fileManager/imageLabelFileManager/templates/fm.html',
        controller  : 'ImageLabelFileManagerController',
        controllerAs: 'imageLabelFileManagerCtrl'
      });
  }


})();
