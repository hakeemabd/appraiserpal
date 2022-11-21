(function () {
    "use strict";

    angular
        .module("appraiserpal.settings")
        .controller("SettingsController", SettingsController);

    SettingsController.$inject = [
        "$scope",
        "$state",
        "$stateParams",
        "$windows",
        "$http",
        "StatesService",
        "moment",
        "currentUser",
        "SettingsService"
    ];

    /* @ngInject */
    function SettingsController(
        $scope,
        $state,
        $stateParams,
        $window,
        $http,
        statesService,
        moment,
        currentUser,
        settingsService
    ) {
        var vm = this;
        console.log('entro');
        settingsService.getSettings().then(function (settings) {
            console.log(settings);
            vm.settings = settings;
        });
    }
})();

