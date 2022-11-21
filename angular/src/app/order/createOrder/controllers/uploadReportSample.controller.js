(function () {
  'use strict';

  angular.module('appraiserpal.order.createOrder').controller('UploadReportSampleController', UploadReportSampleController);

  UploadReportSampleController.$inject = [
    '$modalInstance',
    'S3FileUploadService',
    'FileUploadConfigService',
    'FILES'
  ];

  /* @ngInject */
  function UploadReportSampleController($modalInstance, S3FileUploadService, fileUploadConfigService, FILES) {
    var vm = this;

    vm.sample = {
      label: null,
      file: null
    };
    vm.progress = 0;

    vm.sampleFileUploadConfig = fileUploadConfigService.getConfig()[FILES.SAMPLE];

    vm.ok = ok;
    vm.cancel = cancel;

    ////////////////

    function ok(form) {
      if (form.$valid) {
        vm.progress = 1;

        S3FileUploadService.upload(vm.sample.file, FILES.SAMPLE).then(
            function (file) {
              var sample = {
                label: vm.sample.label,
                key: file.key,
                file: vm.sample.file
              };

              $modalInstance.close(sample);
            },
            null,
            function (evt) {
              vm.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
            }
        );
      } else {
        form.showValidation = true;
      }
    }

    function cancel() {
      $modalInstance.dismiss('cancel');
    }
  }

})();

