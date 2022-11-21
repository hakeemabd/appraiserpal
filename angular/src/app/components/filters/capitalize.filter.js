(function () {
    'use strict';

    angular.module('appraiserpal').filter('capitalize', capitalize);

    function capitalize() {
        return capitalize;

        ////////////////

        function capitalize(input) {
            if (!input) {
                return "";
            }
            return input.substring(0, 1).toUpperCase() + input.substring(1);
        }
    }

})();

