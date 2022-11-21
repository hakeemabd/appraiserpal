(function() {
  'use strict';

  angular
    .module('appraiserpal.fileManager.imageLabelFileManager')
    .controller('ImageLabelFileManagerController', ImageLabelFileManagerController);

  ImageLabelFileManagerController.$inject = [
    'LabelModel',
    '$modal',
    '$scope'
  ];

  function ImageLabelFileManagerController(
    LabelModel,
    $modal,
    $scope
  ) {
      var vm = this;
      vm.labels = null;
      vm.data = null;
      vm.columns = [
        {"data": "Label"},
        {"data": "Delete"}
      ];
      vm.options = {
        "bFilter": false,//search
        responsive: true
      };

      $scope.removeComparableByIndex = function (index) {
        vm.labels[index].DSDestroy();
        vm.labels.splice(index, 1);
        getLabels();
      };
      vm.openUploadImageFileModal = openUploadImageFileModal;

      getLabels();
      ////////////////

      function getLabels() {
        LabelModel.findAll().then(function (labels) {
          vm.labels = labels;
          var data = [];
          for(var i in labels) {
            if(labels[i].canDelete) {
              data.push({
                Label : labels[i].name,
                Delete: '<i class="fa fa-times" target="_blank" data-id="' + i + '"></i>'
              })
            }
          }
          if(data.length != 0) {
            vm.data = data;
          }
          else {
            vm.data = null;
          }

        });
      }

      function addNewLabel(label) {
        return LabelModel.create(label).then(function (newLabel) {
          vm.labels.push(newLabel);
          if(vm.data == null) {
            vm.data = [];
          }
          vm.data.push({
            Label : newLabel.name,
            Delete: '<i class="fa fa-times" target="_blank" data-id="' + (vm.data.length + 1) + '"></i>'
          });
          getLabels();
          return newLabel;
        });
      }

      function openUploadImageFileModal() {
        var modalInstance = $modal.open({
          templateUrl: 'app/fileManager/imageLabelFileManager/templates/uploadImageLabel.html',
          controller: 'UploadImageLabelController',
          controllerAs: 'uploadImageLabelCtrl',
          backdrop: 'static'
        });

        return modalInstance.result
          .then(addNewLabel)
          .catch(function () {
            vm.currentReportType = null;
          });
      }
  }

})();
