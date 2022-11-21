(function () {
  'use strict';

  angular
    .module('appraiserpal.fileManager.cloneFileManager')
    .controller('CloneFileManagerController', CloneFileManagerController);

  CloneFileManagerController.$inject = [
    '$modal',
    '$scope',
    'AttachmentService',
    'currentUser',
    'SoftwareService',
    '$http',
    'FILES',
    '$filter'
  ];

  /* @ngInject */
  function CloneFileManagerController(
    $modal,
    $scope,
    attachmentService,
    currentUser,
    SoftwareService,
    $http,
    FILES,
    $filter
  ) {

      var vm = this;
      //vm.cloneFiles = null;
      vm.softwareList = null;
      vm.currentSoftware = null;
      vm.softwareId = null;
      vm.FILES = FILES;
      vm.tableData = null;
      vm.data = null;
      vm.columns = [
        {"data": "Category"},
        {"data": "File"},
        {"data": "Delete"}
      ];
      vm.options = {
        "bFilter": false,//search
        responsive: true
      };

      getSoftware();

      $scope.$watch(
        function () {
          return vm.currentSoftware;
        },
        watchCloneType
      );

      $scope.removeComparableByIndex = function(id) {
        return $http.delete("/api/attachment/" + id).success(function(data, status) {
          getCloneFiles();
        });
      };

      function watchCloneType(newValue, oldValue) {
          if (newValue && newValue !== oldValue) {
            vm.softwareId = newValue;
            openUploadCloneFileModal();
          }
        }

      function openUploadCloneFileModal() {

        var modalInstance = $modal.open({
          templateUrl: 'app/order/createOrder/templates/uploadCloneFile.html',
          controller: 'UploadCloneFileController',
          controllerAs: 'uploadCloneFileCtrl',
          keyboard: false,
          backdrop: 'static'
        });

        return modalInstance.result
          .then(function (clone) {

            clone.software_id = vm.softwareId.id;

            return $http.post("/api/attachment/clone", clone).success(function(data, status) {
              getCloneFiles();
              vm.currentSoftware = null;
            });
          })
          .catch(function () {
            vm.currentSoftware = null;
          });

      }

      function getCloneFiles() {
        attachmentService.getCloneFiles().then(function (cloneFiles) {
          var data = [];
          //vm.cloneFiles = cloneFiles;
          for(var i in cloneFiles) {
            var software = $filter('filter')(vm.softwareList, function (d) {return d.id === cloneFiles[i].software_id;})[0];

            data.push({
              Category: software.name,
              File: '<a href="' + cloneFiles[i].path + '">' + cloneFiles[i].label + '</a>',
              Delete: '<i class="fa fa-times" target="_blank" data-id="'+ cloneFiles[i].id +'"></i>'
            });
          }

          if(data.length != 0) {
            vm.data = data;
          }
          else {
            vm.data = null;
          }
        });

      }

      function getSoftware() {
          SoftwareService.getSoftwares().then(function (softwares) {
            vm.softwareList = softwares;
            getCloneFiles();
          });
      }
    }

})();
