(function () {
  'use strict';

  angular.module('appraiserpal.order.createOrder').service('ReportSamplesService', ReportSamplesService);

  ReportSamplesService.$inject = ['$q'];

  /* @ngInject */
  function ReportSamplesService($q) {
    this.getReportSamples = getReportSamples;

    ////////////////

    function getReportSamples() {
      var deferred = $q.defer();

      deferred.resolve([{
        text: "1004: Uniform Residential Appraisal Report",
        value: "1004"
      },
        {
          text: "1004C: Manufactured Home Appraisal",
          value: "1004C"
        },
        {
          text: "1025: Small Residential Income Property Appraisal Report",
          value: "1025"
        },
        {
          text: "1073: Individual Condo Unit Appraisal Report",
          value: "1073"
        },
        {
          text: "FHA 1004: FHA Uniform Residential Appraisal Report",
          value: "FHA1004"
        },
        {
          text: "FHA 1025: FHA Small Residential Income Property Appraisal Report",
          value: "FHA1025"
        },
        {
          text: "2055: Exterior Only Inspection Residential Appraisal Report",
          value: "2055"
        },
        {
          text: "1075: Exterior Only Condo",
          value: "1075"
        }]);

      return deferred.promise;
    }
  }

})();

