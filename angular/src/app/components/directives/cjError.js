(function () {
    'use strict';

    angular.module('appraiserpal').directive('cjError', cjError);

    cjError.$inject = [];

    /* @ngInject */
    function cjError() {
        return {
            template: ["<div><span ng-repeat='(field, errorArray) in directiveData.errors'>{{field}} : ",
                "<span ng-repeat='error in errorArray'>{{error}}</span><br></span></div>"].join("")
        };
    }

})();

