(function () {
    'use strict';

    angular
        .module('appraiserpal')
        .directive('cjChat', cjChat);

    cjChat.$inject = [];

    /* @ngInject */
    function cjChat() {
        return {
            templateUrl: 'app/components/directives/cjChat.html'
        };
    }

})();

