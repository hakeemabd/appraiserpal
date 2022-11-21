(function () {
  'use strict';

  angular.module('appraiserpal.order.createOrder').controller('ModalSummaryController', ModalSummaryController);

  ModalSummaryController.$inject = ['$modalInstance', 'createOrderCtrl'];

  /* @ngInject */
  function ModalSummaryController($modalInstance, createOrderCtrl) {
    var vm = this;

    vm.close = $modalInstance.close;

    angular.extend(vm, createOrderCtrl);
  }
})();

