(function () {
    'use strict';

    angular
        .module('appraiserpal')
        .config(config);

    config.$inject = ["$logProvider", "$httpProvider"];

    /** @ngInject */
    function config($logProvider, $httpProvider) {
        // Enable log
        $logProvider.debugEnabled(true);

        $httpProvider.defaults.headers.post["X-CSRF-TOKEN"] = angular.element('meta[name="csrf-token"]').attr('content');

    }

})();
