(function () {
  'use strict';

  angular.module('appraiserpal.order.createOrder').service('ReportTypesService', ReportTypesService);

  ReportTypesService.$inject = ['$http'];

  /* @ngInject */
  function ReportTypesService($http) {
    this.getReportTypes = getReportTypes;

    ////////////////

    function getReportTypes() {
      return $http.get('/api/report-types').then(function (resp) {
        var reportTypes = [];

        angular.forEach(resp.data.report_types, function (reportType) {
          reportTypes.push({
            value: reportType.id,
            text: reportType.name
          });
        });

        return reportTypes;
      });
    }
  }

})();

