(function () {
    'use strict';

    angular
        .module('appraiserpal')
        .directive('cjStopEvent', cjStopEvent);

    cjStopEvent.$inject = [];

    /* @ngInject */
    function cjStopEvent() {
        return {
            restrict: 'A',
            link: link
        };

        function link($scope, element, attr) {
            element.on(attr.cjStopEvent, function (e) {
                e.stopPropagation();
            });
        }
    }

})();


