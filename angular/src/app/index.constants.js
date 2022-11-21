/* global FILE_UPLOAD_CONFIG:false, moment:false, jQuery:false */
(function () {
    'use strict';

    angular
        .module('appraiserpal')
        .constant('FILE_UPLOAD_CONFIG', FILE_UPLOAD_CONFIG)
        .constant('moment', moment)
        .constant('$', jQuery)
        .constant('FILES', {
            ADJUSTMENT_SHEETS: 'adj_sheets',
            CLONE: 'clone',
            COMPARABLE: 'comparables',
            COMPARABLE_INFO: 'comparable_info',
            CONTRACT: 'contract',
            COLLECT_DATA_MANUAL: 'data_file_manual',
            COLLECT_DATA_MOBILE: 'data_file_mobile',
            MC1004: 'mc_1004',
            MSL: 'mls',
            PHOTO: 'photo',
            SAMPLE: 'sample',
            SKETCH: 'sketch',
            INSPECTION_SHEETS: 'subject',
            MISCELLANEOUS: 'miscellaneous'
        });
})();
