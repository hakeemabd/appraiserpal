(function () {
    'use strict';

    angular
        .module('appraiserpal.profile')
        .config(routerConfig);

    routerConfig.$inject = [
        "$stateProvider"
    ];

    /** @ngInject */
    function routerConfig($stateProvider) {
        $stateProvider
            .state('home.profile', {
                url: 'profile',
                templateUrl: 'app/profile/templates/profile.html'
            })
            .state('home.profile.billing', {
                url: '/billing/edit',
                templateUrl: 'app/profile/templates/billingInfoEdit.html',
                controller: "BillingInfoController",
                controllerAs: "billingInfoCtrl"
            });

    }

})();
