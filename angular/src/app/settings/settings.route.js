(function () {
    'use strict';

    angular
        .module('appraiserpal.settings')
        .config(routerConfig);

    routerConfig.$inject = [
        "$stateProvider"
    ];

    /** @ngInject */
    function routerConfig($stateProvider) {
        $stateProvider
            .state('home.settings', {
                url: 'settings',
                templateUrl: 'app/settings/templates/settings.html',
                controller: "SettingsController",
                controllerAs: "settingsfoCtrl"
            });

    }

})();
