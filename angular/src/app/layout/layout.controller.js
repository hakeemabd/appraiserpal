(function () {
    'use strict';

  angular.module('appraiserpal').controller('LayoutController', LayoutController);

    LayoutController.$inject = [];

    /* @ngInject */
    function LayoutController() {
        var vm = this;

        vm.app = {
            name: 'AppraiserPal',
            settings: {
                themeID: 1,
                navbarHeaderColor: 'bg-black',
                navbarCollapseColor: 'bg-white-only',
                asideColor: 'bg-black',
                headerFixed: true,
                asideFixed: false,
                asideFolded: false,
                asideDock: false,
                container: false
            }
        };

        activate();

        ////////////////

        function activate() {
        }
    }

})();

