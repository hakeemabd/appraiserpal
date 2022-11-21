(function () {
  'use strict';

  angular.module('appraiserpal.order.createOrder').controller('UploadCloneFileController', UploadCloneFileController);

  UploadCloneFileController.$inject = [
    '$modalInstance',
    'S3FileUploadService',
    'FILES',
    'FileUploadConfigService'
  ];

  /* @ngInject */
  function UploadCloneFileController($modalInstance, S3FileUploadService, FILES, fileUploadConfigService) {
    var vm = this;

    vm.clone = {
      label: null,
      file: null
    };
    vm.progress = 0;

    vm.cloneFileUploadConfig = fileUploadConfigService.getConfig()[FILES.CLONE];

    vm.ok = ok;
    vm.cancel = cancel;

    ////////////////

    function ok(form) {
      if (form.$valid) {
        vm.progress = 1;

        S3FileUploadService.upload(vm.clone.file, FILES.CLONE).then(
            function (file) {
              var clone = {
                label: vm.clone.label,
                key: file.key,
                file: vm.clone.file
              };
              $modalInstance.close(clone);
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

