(function () {
  'use strict';

  angular.module('appraiserpal.order').factory('LabelModel', OrderModel);

  OrderModel.$inject = [
    'DS'
  ];

  /* @ngInject */
  function OrderModel(DS) {

    return DS.defineResource({

      name: "Label",

      idAttribute: "id",

      endpoint: "/label"
    });
  }

})();