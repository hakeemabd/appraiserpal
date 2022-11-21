/*eslint angular/document-service:0*/
(function () {
    'use strict';

    //wait user load
    document.addEventListener('userReady', angularInit);

    function angularInit(e) {
        angular.module('appraiserpal')
            .constant('currentUser', e.detail);

        angular.bootstrap(document.body, ['appraiserpal']);
    }

    angular
        .module('appraiserpal')
        .run(runBlock);

    function runBlock() {

    }

})();
