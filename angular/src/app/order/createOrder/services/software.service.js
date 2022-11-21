(function () {
  'use strict';

  angular.module('appraiserpal.order.createOrder').service('SoftwareService', SoftwareService);

  SoftwareService.$inject = ['$http'];

  /* @ngInject */
  function SoftwareService($http) {
    this.getSoftwares = getSoftwares;

    ////////////////

    function getSoftwares() {
      return $http.get('/api/software').then(function (resp) {
        return resp.data.softwares;
      });
    }
  }

})();

