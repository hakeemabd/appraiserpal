(function () {
  'use strict';

  angular.module('appraiserpal.order.createOrder').controller('UploadImageLabelController', UploadImageLabelController);

  UploadImageLabelController.$inject = [
    '$modalInstance'
  ];

  /* @ngInject */
  function UploadImageLabelController($modalInstance) {
    var vm = this;

    vm.label = {
      name: null
    };
    vm.progress = 0;

    vm.ok = ok;
    vm.cancel = cancel;

    ////////////////

    function ok(form) {
      if (form.$valid) {
        vm.progress = 100;
        $modalInstance.close(vm.label);
      } else {
        form.showValidation = true;
      }
    }

    function cancel() {
      $modalInstance.dismiss('cancel');
    }
  }

})();

