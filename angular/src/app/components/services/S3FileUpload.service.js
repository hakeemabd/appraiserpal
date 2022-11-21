(function () {
    'use strict';

    angular
        .module('appraiserpal')
        .service('S3FileUploadService', S3FileUploadService);

    S3FileUploadService.$inject = ['$log', 'Upload', 'FILE_UPLOAD_CONFIG'];

    /* @ngInject */
    function S3FileUploadService($log, Upload, FILE_UPLOAD_CONFIG) {
        this.upload = upload;
        this.isUploaded = isUploaded;
        this.markUploaded = markUploaded;

        ////////////////

        function upload(file, fileType) {
            var fileConfig = FILE_UPLOAD_CONFIG.uploadConfig[fileType],
                prefix = Math.random().toString(36).slice(2) + "_",
                data = {
                    key: fileConfig.folder + prefix + file.name,
                    AWSAccessKeyId: FILE_UPLOAD_CONFIG.accessKey,
                    acl: 'private',
                    policy: fileConfig.policy,
                    signature: fileConfig.signature,
                    'Content-Type': file.type === null || file.type === '' ? 'application/octet-stream' : file.type,
                    file: file
                };

            return Upload.upload({
                url: FILE_UPLOAD_CONFIG.uploadUrl,
                method: 'POST',
                data: data
            }).then(function () {
                return {
                    key: data.key,
                    type: fileType,
                    name: file.name,
                    lastModified: file.lastModified
                };
            }, function (resp) {
                $log.info("Amazon S3 Error ", angular.element(resp.data).find("message").text());
            });
        }

        function isUploaded(file) {
            return file.uploaded;
        }

        function markUploaded(file) {
            if (file) {
                if (angular.isArray(file)) {
                    angular.forEach(file, function (fileObject, key) {
                        file[key].uploaded = true;
                    })
                }
                file.uploaded = true;
            }
        }
    }

})();

