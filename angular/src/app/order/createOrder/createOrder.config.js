(function () {
  'use strict';

  angular.module('appraiserpal.order.createOrder').config(config);

  config.$inject = [
    '$compileProvider'
  ];

  /** @ngInject */
  function config($compileProvider) {
    $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|tel|file|blob):/);
  }

})();
