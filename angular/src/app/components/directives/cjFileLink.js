(function () {
    'use strict';

    angular
        .module('appraiserpal')
        .directive('cjFileLink', cjFileLink);

    cjFileLink.$inject = ['$window'];

    /* @ngInject */
    function cjFileLink($window) {
        var directive = {
            replace: true,
            link: link,
            restrict: 'E',
            scope: {
                file: "=",
                label: "@"
            },
            template: "<a class='link file-link' target='_blank'>{{label}}</a>"
        };
        return directive;

        function link($scope, element) {
            $scope.$watch('file', function () {
                if ($scope.file) {
                    if (angular.isString($scope.file)) {
                        element.attr("href", $scope.file);
                    } else  {
                        element.attr("href", $scope.file.path || $window.URL.createObjectURL($scope.file));
                    }
                }
            })
        }
    }

})();

