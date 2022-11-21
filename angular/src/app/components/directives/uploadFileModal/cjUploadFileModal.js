(function () {
    'use strict';

    angular.module('appraiserpal').directive('cjUploadFileModal', cjUploadFileModal);

    cjUploadFileModal.$inject = ['$'];

    /* @ngInject */
    function cjUploadFileModal($) {
        return {
            bindToController: true,
            templateUrl: "app/components/directives/uploadFileModal/cjUploadFileModal.html",
            scope: {
                "sendUrl": "@",
                "modalBodyTpl": "@",
                "btnTitle": "@",
                "fileType": "@"
            },
            controllerAs: "uploadFileCtrl",
            controller: UploadFileController,
            link: link
        };

        function link($scope, element, attrs, ctrl) {
            var $el = $(element);

            ctrl.$el = $el;

            $('.modal-trigger', $el).click(function () {
                var $modal = $(".modal", $el);

                ctrl.$modal = $modal;

                $modal.openModal();
            });
        }

    }


    UploadFileController.$inject = ["$element", "$", "$http", "$log", "$window", "S3FileUploadService"];

    function UploadFileController($element, $, $http, $log, $window, S3FileUploadService) {
        var vm = this;

        vm.file = {
            file: null,
            label: null
        };
        vm.uploadProgress = 0;

        vm.submitForm  = submitForm;

        function submitForm(form) {
            if (form.$valid) {
                S3FileUploadService.upload(vm.file.file, vm.fileType).then(
                    function (file) {
                        var data = angular.copy(vm.file);

                        data.key = file.key;

                        delete data.file;
                        
                        $http.post('/attachment/save', data)
                            .then(function () {
                                vm.$modal.closeModal();
                                window.upload_documents_datatable.ajax.reload();
                            })
                            .catch(function (error) {
                                $log.debug(error);
                            });
                    },
                    null,
                    function (evt) {
                        vm.uploadProgress = uploadPercentProgress(evt)
                    }
                );
            } else {
                form.$setSubmitted();
            }
        }

        function uploadPercentProgress(evt) {
            return parseInt(100.0 * evt.loaded / evt.total);
        }
    }

})();

