(function () {
  'use strict';

  angular.module('appraiserpal.order.createOrder').service('AttachmentService', AttachmentService);

  AttachmentService.$inject = ['$http', '$filter', 'FILES'];

  /* @ngInject */
  function AttachmentService($http, $filter, FILES) {
    this.getCloneFiles = getCloneFiles;
    this.getReportSampleFiles = getReportSampleFiles;
    this.getImageSamplesFiles = getImageSamplesFiles;

    ////////////////

    function getCloneFiles(softwareId) {
      return $http.get("/api/attachment/" + FILES.CLONE).then(function (resp) {
        return $filter("filter")(resp.data.files, {software_id: softwareId});
      })
    }

    function getReportSampleFiles() {
      return $http.get("/api/attachment/" + FILES.SAMPLE).then(function (resp) {
        return resp.data.files;
      })
    }

    function getImageSamplesFiles() {
      return $http.get("/api/attachment/" + FILES.PHOTO).then(function (resp) {
        return resp.data.files;
      })
    }
  }

})();

