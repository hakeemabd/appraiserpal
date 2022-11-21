(function () {
  'use strict';

  angular.module('appraiserpal.settings').service('SettingsService', SettingsService);

  SettingsService.$inject = ['$http'];

  /* @ngInject */
  function SettingsService($http) {
    this.getSettings = getSettings;

    function getSettings() {
      return $http.get("/settings/").then(function (resp) {
        return resp.data;
      });
    }
  }

})();

