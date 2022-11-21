(function () {
    'use strict';

    angular
        .module('appraiserpal')
        .service('FileUploadConfigService', FileUploadConfigService);

    FileUploadConfigService.$inject = ['FILE_UPLOAD_CONFIG'];

    /* @ngInject */
    function FileUploadConfigService(config) {
        this.getConfig = getConfig;

        ////////////////

        function getConfig() {
            var fileConf = angular.copy(config.uploadConfig);

            for (var i in fileConf) {
                fileConf[i].acceptMime = (fileConf[i].acceptMime.length) ? fileConf[i].acceptMime.join(',') : "";
                fileConf[i].extensions = (fileConf[i].extensions.length) ? '.' + fileConf[i].extensions.join(',.'): "";
            }


            return fileConf;
        }
    }

})();

