(function () {
  'use strict';

  angular.module('appraiserpal.fileManager', [
      'appraiserpal.fileManager.cloneFileManager',
      'appraiserpal.fileManager.sampleFileManager',
      'appraiserpal.fileManager.imageLabelFileManager'
  ]).directive('datatable', function() {
      return function (scope, element, attrs) {

        var options = scope.$eval(attrs.datatable);
        options["columns"] = scope.$eval(attrs.aoColumnDefs);

        scope.$eval(attrs.fnRowCallback);

        $(element).on("click", ".fa-times", function(e) {
          scope.removeComparableByIndex($(this).attr('data-id'));
        });

        var dataTable = $(element).dataTable(options);

        scope.$watch(attrs.aaData, function(value) {
          var val = value || null;
          dataTable.fnClearTable();
          if (val) {
            dataTable.fnAddData(scope.$eval(attrs.aaData));
          }
        });
      }
    })
    .directive('activeLink', ['$location', function (location) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs, controller) {
        var clazz = attrs.activeLink;
        var path = attrs.href;
        path = path.substring(1); //hack because path does not return including hashbang
        scope.location = location;
        scope.$watch('location.path()', function (newPath) {
          if (path === newPath) {
            element.parent().addClass(clazz);
          } else {
            element.parent().removeClass(clazz);
          }
        });
      }
    };
  }]);

})();
