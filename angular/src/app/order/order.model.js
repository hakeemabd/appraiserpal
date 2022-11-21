(function () {
    'use strict';

    angular
        .module('appraiserpal.order')
        .factory('OrderModel', OrderModel);

    OrderModel.$inject = [
        '$q',
        '$http',
        'DS',
        'S3FileUploadService',
        'FILES',
        'moment'
    ];

    /* @ngInject */
    function OrderModel($q, $http, DS, S3FileUploadService, FILES, moment) {

        return DS.defineResource({

            name: "Order",

            idAttribute: "id",

            endpoint: "/order",

            beforeInject: function (resource, data) {
                var newData = {
                        id: data.id,
                        collectData: {},
                        files: resource.initFistStepFiles(),
                        reportType: data.report_type_id,
                        title: data.title,
                        effectiveDate: moment(data.effective_date).format("L"),
                        assignmentType: data.assignment_type,
                        financing: data.financing,
                        occupancy: data.occupancy_type,
                        propertyRightAppraised: data.property_rights,
                        standardOrderInstruction: data.standard_instructions,
                        customOrderInstruction: data.specific_instructions,
                        adjustmentSheetsType: data.adjustment_type,
                        orderTransaction: data.order_transaction,
                        price: data.price
                    },
                    file;

                if (data.data_file_mobile) {
                    file = data.data_file_mobile[0];

                    newData.collectData = {
                        type: FILES.COLLECT_DATA_MOBILE,
                        file: {
                            id: file.id,
                            path: file.path,
                            name: file.name
                        }
                    };
                } else if (data.data_file_manual) {
                    file = data.data_file_manual[0];

                    newData.collectData = {
                        type: FILES.COLLECT_DATA_MANUAL,
                        file: {
                            id: file.id,
                            path: file.path,
                            name: file.name
                        }
                    };
                }

                S3FileUploadService.markUploaded(newData.collectData.file);

                angular.forEach(newData.files, function (value, fileConstant) {
                    if (data[fileConstant] && data[fileConstant].length) {
                        var file = data[fileConstant][0];

                        newData.files[fileConstant] = {
                            has: false,
                            file: {
                                id: file.id,
                                name: file.name,
                                path: file.path
                            },
                            label: newData.files[fileConstant].label,
                            collectDataType: value.collectDataType
                        };

                        S3FileUploadService.markUploaded(newData.files[fileConstant].file);
                    } else {
                        newData.files[fileConstant].has = true;
                    }
                });

                if (data[FILES.CLONE]) {
                    var clone = data[FILES.CLONE][0];
                    
                    newData.clone = {
                        label: clone.label,
                        path: clone.path,
                        id: clone.id,
                        name: clone.name,
                        file: null
                    }
                }
                if (data[FILES.SAMPLE]) {
                    var sample = data.sample[0];

                    newData.reportSample = {
                        label: sample.label,
                        id: sample.id,
                        name: sample.name,
                        path: sample.path
                    }
                }
                if (data[FILES.MISCELLANEOUS]) {
                    newData.miscellaneousFiles = [];

                    data[FILES.MISCELLANEOUS].forEach(function (file) {
                        file = {
                            id: file.id,
                            path: file.path,
                            name: file.name
                        };

                        S3FileUploadService.markUploaded(file);

                        newData.miscellaneousFiles.push(file);
                    });
                }
                if (data[FILES.COMPARABLE]) {
                    newData.comparables = [];

                    data[FILES.COMPARABLE].forEach(function (file) {
                        var comparablePhoto = {
                            id: file.id,
                            path: file.path,
                            name: file.name
                        };

                        S3FileUploadService.markUploaded(comparablePhoto);

                        newData.comparables.push(angular.extend({
                            photo: comparablePhoto
                        }, angular.fromJson(file.label)));
                    });

                }

                if (data[FILES.ADJUSTMENT_SHEETS]) {
                    var adjustmentSheets = data[FILES.ADJUSTMENT_SHEETS][0];

                    newData.adjustmentSheets = {
                        id: adjustmentSheets.id,
                        path: adjustmentSheets.path,
                        name: adjustmentSheets.name
                    };

                    S3FileUploadService.markUploaded(newData.adjustmentSheets);
                }

                if (data[FILES.PHOTO]) {
                    newData.photos = [];

                    data[FILES.PHOTO].forEach(function (file) {
                        var photoFile = {
                                id: file.id,
                                path: file.path,
                                name: file.name
                             };

                        S3FileUploadService.markUploaded(photoFile);

                        newData.photos.push({
                            file: photoFile,
                            label: {
                                id: file.label_id,
                                name: file.label
                            }
                        });
                    });
                }


                angular.forEach(data, function (value, index, array) {
                    delete array[index];
                });

                angular.forEach(newData, function (value, index) {
                    data[index] = value;
                });

                return data;
            },


            methods: {
                prepareDataFor1Step: function () {
                    var data = {
                            software_id: this.software.id,
                            collect_data_type: this.collectData.type,
                            clone: {}
                        },
                        files = {};


                    //If file from server
                    if (this.clone.id) {
                        data.clone.id = this.clone.id;
                    } else {
                        data.clone.key = this.clone.key;
                        data.clone.label = this.clone.label;
                    }

                    //If file from server
                    if (this.collectData.file.id) {
                        data[this.collectData.type] = {id: this.collectData.file.id};
                    } else {
                        files[this.collectData.type] = this.collectData.file;
                    }


                    angular.forEach(this.files, function (file, fileConstant) {
                        if (!file.has && (!file.collectDataType || file.collectDataType === this.collectData.type)) {
                            //If file from server
                            if (file.file.id) {
                                data[fileConstant] = {id: file.file.id};
                                data['has_' + fileConstant] = true;
                            } else if (!S3FileUploadService.isUploaded(file.file)){
                                if (angular.isUndefined(file.collectDataType) || file.collectDataType === this.collectData.type) {
                                    files[fileConstant] = file.file;
                                    S3FileUploadService.markUploaded(this.files[fileConstant].file);
                                }
                            }

                        }
                    }.bind(this));


                    if (this.miscellaneousFiles) {
                        //If file from server
                        if (this.miscellaneousFiles[0].id) {
                            data[FILES.MISCELLANEOUS] = [];
                            angular.forEach(this.miscellaneousFiles, function (file) {
                                data[FILES.MISCELLANEOUS].push({id: file.id});
                            })
                        } else if (!S3FileUploadService.isUploaded(this.miscellaneousFiles[0])) {
                            files[FILES.MISCELLANEOUS] = this.miscellaneousFiles;
                            S3FileUploadService.markUploaded(this.miscellaneousFiles);
                        }

                    }

                    return this.uploadFiles(files).then(function (files) {
                        if (files[FILES.COLLECT_DATA_MOBILE]) {
                            data["data_file_mobile"] =  {key: files[FILES.COLLECT_DATA_MOBILE].key};
                            delete files[FILES.COLLECT_DATA_MOBILE];
                        } else if (files[FILES.COLLECT_DATA_MANUAL]) {

                            data["data_file_manual"] =  {key: files[FILES.COLLECT_DATA_MANUAL].key};
                            delete files[FILES.COLLECT_DATA_MANUAL];
                        }

                        if (files[FILES.MISCELLANEOUS]) {
                            if (!angular.isArray(files[FILES.MISCELLANEOUS])) {
                                data[FILES.MISCELLANEOUS] = [{key: files[FILES.MISCELLANEOUS].key}];
                            } else {
                                data[FILES.MISCELLANEOUS] = [];
                                angular.forEach(files[FILES.MISCELLANEOUS], function (file) {
                                    data[FILES.MISCELLANEOUS].push({key: file.key});
                                });
                            }

                            delete files[FILES.MISCELLANEOUS];
                        }

                        angular.forEach(files, function (file, fileType) {
                            data['has_' + fileType] = true;
                            data[fileType] = {key: file.key};
                        });

                        return data;
                    });

                },
                prepareDataFor2Step: function () {
                    var files = {},
                        data = {
                            comparables: {}
                        },
                        self = this;

                    files[FILES.COMPARABLE] = [];

                    angular.forEach(this.comparables, function (comparable, index) {
                        var comparableWithOutPhoto = angular.copy(comparable);

                        delete comparableWithOutPhoto.photo;

                        //If file from server
                        if (comparable.photo.id) {
                            data.comparables[index] = comparableWithOutPhoto;
                            data.comparables[index].id = comparable.photo.id;
                        } else if(!S3FileUploadService.isUploaded(comparable.photo)) {
                            data.comparables[index] = comparableWithOutPhoto;
                            files[FILES.COMPARABLE].push(comparable.photo);
                            S3FileUploadService.markUploaded(comparable.photo);
                        }
                    });

                    return this.uploadFiles(files).then(function (files) {
                        if (files.comparables && !angular.isArray(files.comparables)) {
                            files.comparables = [files.comparables];
                        }

                        //find files by name and set comparables
                        angular.forEach(files.comparables, function (file) {
                            angular.forEach(self.comparables, function (comparable, index) {
                                var fileName = comparable.photo.name,
                                    lastModified = comparable.photo.lastModified;

                                if(fileName === file.name && lastModified === file.lastModified) {
                                    data.comparables[index].key = file.key;
                                }
                            })
                        });

                        return data;
                    });
                },

                prepareDataFor3Step: function () {
                    var data = {
                        title: this.title,
                        effective_date: this.effectiveDate,
                        report_type_id: this.reportType,
                        assignment_type: this.assignmentType,
                        financing: this.financing,
                        occupancy_type: this.occupancy,
                        property_rights: this.propertyRightAppraised,
                        standard_instructions: this.standardOrderInstruction,
                        specific_instructions: this.customOrderInstruction
                    };

                    if (this.reportSample) {
                        //If file from server
                        if (this.reportSample.id) {
                            data[FILES.SAMPLE] = {
                                id: this.reportSample.id
                            }
                        } else if(this.reportSample.key) {
                            data[FILES.SAMPLE] = {
                                key: this.reportSample.key,
                                label: this.reportSample.label
                            };
                        }
                    }


                    return $q.when(data);
                },

                prepareDataFor4Step: function () {
                    var data = {
                        adjustment_type: this.adjustmentSheetsType
                    };

                    if (this.adjustmentSheets && this.adjustmentSheetsType) {

                        return this.uploadFiles(this.adjustmentSheets, FILES.ADJUSTMENT_SHEETS)
                            .then(function (file) {
                                data.adj_sheets = {key: file.key};

                                return data;
                            });
                    }

                    return $q.when(data);
                },

                prepareDataFor5Step: function () {
                    var files = {},
                        data = {
                            photo: [],
                            completed: 1
                        },
                        self = this;

                    files[FILES.PHOTO] = [];

                    angular.forEach(this.photos, function (photo, key) {
                        if (photo.file.id) {
                            data.photo.push({
                                id: photo.file.id,
                                label: photo.label.id
                            });
                        } else if(!S3FileUploadService.isUploaded(photo.file)) {
                            files[FILES.PHOTO].push(photo.file);
                            S3FileUploadService.markUploaded(this.photos[key].file);
                        }
                    }.bind(this));

                    return this.uploadFiles(files).then(function (files) {
                        if (files.photo &&  !angular.isArray(files.photo)) {
                            files.photo = [files.photo];
                        }

                        //find files by name and set label
                        angular.forEach(files, function (files) {
                            angular.forEach(self.photos, function (photo) {
                                angular.forEach(files, function (file) {
                                    if(file.name === photo.file.name && file.lastModified === photo.file.lastModified) {
                                        data.photo.push({
                                            key: file.key,
                                            label: photo.label.id
                                        });
                                    }
                                });
                            })
                        });

                        return data;
                    });
                },

                /**
                 * save order model by step number
                 * @param stepNumber
                 * @returns {promise}
                 */
                save: function (stepNumber) {
                    var url = "/api/order/";

                    return this['prepareDataFor' + stepNumber + 'Step']().then(function (data) {
                        return $http.put(url + this.id, data).then(function (response) {
                            this.orderTransaction = response.data.order_transaction;

                            return response;
                        }.bind(this));
                    }.bind(this));
                },
                /**
                 *
                 * @param files files array or single file
                 * @param fileType if files is single file this attr need contains filetype constant
                 * @returns {Promise|*}
                 */
                uploadFiles: function (files, fileType) {
                    var promises = [],
                        order = this,
                        filesCount = 0;

                    order.uploadProgress = 0;

                    //Is not file
                    if (!files.name) {
                        //Count files
                        angular.forEach(files, function (file) {
                            if (angular.isArray(file)) {
                                angular.forEach(file, function () {
                                    filesCount++;
                                });
                            } else {
                                filesCount++;
                            }
                        });

                        angular.forEach(files, function (file, fileType) {
                            var fileProgress = 0;

                            //If its multiple files in one file field
                            if (angular.isArray(file)) {
                                (function () {
                                    angular.forEach(file, function (file) {
                                        var fileProgress = 0;

                                        promises.push(S3FileUploadService.upload(file, fileType).then(null, null, function (evt) {
                                            var currentProgress = uploadPercentProgress(evt, filesCount);

                                            order.uploadProgress += currentProgress - fileProgress;
                                            fileProgress = currentProgress;
                                        }));
                                    });
                                })()

                            } else {
                                promises.push(S3FileUploadService.upload(file, fileType).then(null, null, function (evt) {
                                    var currentProgress = uploadPercentProgress(evt, filesCount);

                                    order.uploadProgress += currentProgress - fileProgress;
                                    fileProgress = currentProgress;
                                }));
                            }

                        });

                        return $q.all(promises).then(function (responses) {
                            var files = {};

                            angular.forEach(responses, function (file) {
                                //If the keys are duplicated
                                if (angular.isDefined(files[file.type])) {
                                    if (!angular.isArray(files[file.type])) {
                                        var temp = files[file.type];

                                        files[file.type] = [];
                                        files[file.type].push(temp);
                                    }
                                    files[file.type].push({
                                        key: file.key,
                                        name: file.name,
                                        lastModified: file.lastModified
                                    });
                                } else {
                                    files[file.type] = {
                                        key: file.key,
                                        name: file.name,
                                        lastModified: file.lastModified
                                    };
                                }
                            });

                            return files;
                        }.bind(this));
                    } else {
                        return S3FileUploadService.upload(files, fileType).then(null, null, function (evt) {
                            order.uploadProgress = uploadPercentProgress(evt);
                        });
                    }


                    function uploadPercentProgress(evt, filesCount) {
                        var progress = parseInt(100.0 * evt.loaded / evt.total);

                        return (angular.isDefined(filesCount)) ? progress / filesCount : progress;
                    }
                }
            },

            initFistStepFiles: function initializationOrderFiles() {
                var filesForFirstStep = [
                        {
                            label: "Subject and Inspection Info",
                            constant: FILES.INSPECTION_SHEETS
                        },
                        {
                            label: "Sketches",
                            constant: FILES.SKETCH
                        },
                        {
                            label: "1004MC Export",
                            constant: FILES.MC1004,
                            collectDataType: FILES.COLLECT_DATA_MANUAL
                        },
                        {
                            label: "Contract Info",
                            constant: FILES.CONTRACT
                        },
                        {
                            label: "Comparable Info",
                            constant: FILES.COMPARABLE_INFO,
                            collectDataType: FILES.COLLECT_DATA_MOBILE
                        },
                        {
                            label: "MLS Sheet for Subject and Comparables",
                            constant: FILES.MSL,
                            collectDataType: FILES.COLLECT_DATA_MANUAL
                        }
                    ],
                    files = {};

                filesForFirstStep.forEach(function (file) {
                    files[file.constant] = {
                        has: false,
                        file: null,
                        label: file.label,
                        collectDataType: file.collectDataType
                    }
                });

                return files;
            }

        });
    }

})();