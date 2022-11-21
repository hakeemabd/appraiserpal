(function () {
    'use strict';

    angular
        .module('appraiserpal')
        .filter('has', has);

    has.$injection = ["$filter"];

    function has($filter) {
        return has;

        ////////////////

        function has(array, exception, comparator) {
            return !!$filter('filter')(array, exception, comparator).length;
        }
    }

})();

