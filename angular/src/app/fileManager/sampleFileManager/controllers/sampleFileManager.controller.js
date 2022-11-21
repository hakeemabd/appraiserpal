(function () {
  'use strict';

  angular
    .module('appraiserpal.fileManager.sampleFileManager')
    .controller('SampleFileManagerController', SampleFileManagerController);

  SampleFileManagerController.$inject = [
    '$modal',
    '$scope',
    'AttachmentService',
    '$http',
    'ReportTypesService',
    'FileUploadConfigService',
    '$filter'
  ];

  /* @ngInject */
  function SampleFileManagerController(
    $modal,
    $scope,
    attachmentService,
    $http,
    ReportTypesService,
    fileUploadConfigService,
    $filter
  ) {

        var vm = this;
        vm.reportTypesList = null;
        vm.currentReportType = null;
        vm.reportId = null;
        //vm.reportSamples = null;
        vm.filesUploadConfig = fileUploadConfigService.getConfig();
        vm.data = null;
        vm.columns = [
          {"data": "Category"},
          {"data": "File"},
          {"data": "Delete"}
        ];
        vm.options = {
          "bFilter": false,//search
          responsive: true
        };

        //activate();
        ////////////////

        getReportTypes();
    //function activate() {
          //getReportSamples();
        //}

        function getReportTypes() {
          ReportTypesService.getReportTypes().then(function (reportTypes) {
            vm.reportTypesList = reportTypes;
            getReportSamples();
          });
        }

        $scope.$watch(
          function () {
            return vm.currentReportType;
          },
          watchReportType
        );

        function watchReportType(newValue, oldValue) {
          if (newValue && newValue !== oldValue) {
            vm.reportId = newValue;
            openUploadSampleFileModal();
          }
        }

        function openUploadSampleFileModal() {

          var modalInstance = $modal.open({
            templateUrl: 'app/order/createOrder/templates/uploadReportSample.html',
            controller: 'UploadReportSampleController',
            controllerAs: 'uploadReportSampleCtrl',
            backdrop: 'static'
          });

          return modalInstance.result
            .then(function (sample) {

              sample.report_type_id = vm.reportId.value;

              return $http.post("/api/attachment/sample", sample).success(function(data, status) {
                getReportSamples();
                vm.currentReportType = null;
              });
            })
            .catch(function (reason) {
              vm.currentReportType = null;
            });

        }

        function getReportSamples() {

          attachmentService.getReportSampleFiles().then(function (reportSamples) {
            var data = [];
            //vm.reportSamples = reportSamples;
            for(var i in reportSamples) {
              var reportType = $filter('filter')(vm.reportTypesList, function (d) {return d.value === reportSamples[i].report_type_id;})[0];
              data.push({
                Category: reportType.text,
                File: '<a href="' + reportSamples[i].path + '">' + reportSamples[i].label + '</a>',
                Delete: '<i class="fa fa-times" target="_blank" data-id="'+ reportSamples[i].id +'"></i>'
              });
            }

            if(data.length != 0) {
              vm.data = data;
            }
            else {
              vm.data = null;
            }
          });
        }

        $scope.removeComparableByIndex = function(id) {

          return $http.delete("/api/attachment/" + id).success(function(data, status) {
            getReportSamples();
          });

        };




    }

})();
