(function () {
  'use strict';

  angular.module('appraiserpal.order.createOrder').controller('SelectSoftwareController', SelectSoftwareController);

  SelectSoftwareController.$inject = ['$modalInstance', 'SoftwareService'];

  /* @ngInject */
  function SelectSoftwareController($modalInstance, SoftwareService) {
    var vm = this;

    vm.software = null;

    vm.ok = ok;
    vm.selectSoftware = selectSoftware;

    activate();

    ////////////////

    function activate() {
      SoftwareService.getSoftwares().then(function (softwares) {
        vm.softwareList = softwares;
      });
    }

    function ok() {
      $modalInstance.close(vm.software);
    }

    function selectSoftware(software) {
      vm.software = software;
      ok();
    }
  }

})();

