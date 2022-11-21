(function () {
    'use strict';

    angular
        .module('appraiserpal.order.createOrder')
        .controller('CreateOrderController', CreateOrderController);

    CreateOrderController.$inject = [
        '$modal',
        '$state',
        '$stateParams',
        '$scope',
        '$filter',
        '$timeout',
        '$window',
        'Upload',
        'AttachmentService',
        'ReportTypesService',
        'OrderModel',
        'toaster',
        'FileUploadConfigService',
        'FILES',
        'StatesService',
        'ADJUSTMENT_TYPE',
        'LabelModel',
        'currentUser',
        'SoftwareService'
    ];

    /* @ngInject */
    function CreateOrderController(
        $modal,
        $state,
        $stateParams,
        $scope,
        $filter,
        $timeout,
        $window,
        Upload,
        attachmentService,
        reportTypesService,
        OrderModel,
        toaster,
        fileUploadConfigService,
        FILES,
        statesService,
        ADJUSTMENT_TYPE,
        LabelModel,
        currentUser,
        SoftwareService
    ) {
        var vm = this,
            CHECKOUT_STEP = 6,
            FIRST_STEP = 1,
            LAST_STEP = 5;

        vm.FILES = FILES;
        vm.ADJUSTMENT_TYPE = ADJUSTMENT_TYPE;

        vm.openOrderSummaryModal = openOrderSummaryModal;
        vm.openSelectSoftwareModal = openSelectSoftwareModal;
        vm.getActiveStepTitle = getActiveStepTitle;
        vm.getStepTitle = getStepTitle;
        vm.goToStep = goToStep;
        vm.goToNextStep = goToNextStep;
        vm.goToPrevStep = goToPrevStep;
        vm.addComparables = addComparables;
        vm.removeComparableByIndex = removeComparableByIndex;
        vm.removePhotoByIndex = removePhotoByIndex;
        vm.removeLabelByIndex = removeLabelByIndex;
        vm.getTextByValue = getTextByValue;
        vm.onSelectLabel = onSelectLabel;
        vm.addNewLabel = addNewLabel;
        vm.hasUploadFiles = hasUploadFiles;
        vm.onSelectPhotos = onSelectPhotos;
        vm.hasSomethingInCollectDataFile = hasSomethingInCollectDataFile;
        vm.isFileRequired = isFileRequired;
        vm.reset5StepForm = reset5StepForm;

        var order = {
            software: null,
            collectData: {
                type: vm.FILES.COLLECT_DATA_MOBILE,
                file: null
            },
            files: OrderModel.initFistStepFiles(),
            clone: {
                label: null,
                key: null,
                file: null
            },
            miscellaneousFiles: null,
            //Default comparables count
            comparables: [
                {},
                {},
                {}
            ],
            reportType: null,
            reportSample: {
                label: null,
                file: null,
                key: null
            },
            title: null,
            effectiveDate: null,
            assignmentType: null,
            financing: null,
            occupancy: null,
            propertyRightAppraised: null,
            standardOrderInstruction: currentUser.standard_instructions,
            customOrderInstruction: null,
            adjustmentSheetsType: null,
            adjustmentSheets: null,
            photos: null
        };

        vm.order = OrderModel.createInstance(order);

        vm.formWizard = {
            activeStep: ($stateParams.orderId) ? LAST_STEP : FIRST_STEP,
            progressStep: ($stateParams.orderId) ? LAST_STEP : FIRST_STEP,
            completePercent: 0,
            stepsTitles: {
                step1: "Step 1. Upload files",
                step2: "Step 2. Comparables",
                step3: "Step 3. Information",
                step4: "Step 4. Adjustment Sheets",
                step5: "Step 5. Photos"
            },
            forms: {}
        };
        vm.cloneFiles = null;
        vm.reportTypes = null;
        vm.mainPhoto = 0;
        vm.labels = null;
        vm.states = null;

        vm.filesUploadConfig = fileUploadConfigService.getConfig();


        //Watcher for open modal window if clone type changed to 'new'
        $scope.$watch(
            function () {
                return vm.order.clone;
            },
            watchCloneType
        );
        //Watcher for open modal window if reportSample type changed to 'new'
        $scope.$watch(
            function () {
                return vm.order.reportSample;
            },
            watchReportSample
        );
        $scope.$watch(
            function () {
                return vm.order.reportType;
            },
            watchReportType
        );
        $scope.$watchCollection(
            function () {
                return vm.order.photos;
            },
            watchPhotos
        );
        $scope.$watch(
            function () {
                return vm.order.collectData.type;
            },
            watchCollectDataType
        );
        //Watcher for change main photo if label selected
        $scope.$watchCollection(
            function () {
                if (vm.order.photos) {
                    return vm.order.photos[vm.mainPhoto];
                }
            },
            watchMainPhoto,
            true
        );

        $scope.$watchCollection(
            function () {
                var watchArray = [];

                if (vm.order.software) {
                    watchArray.push(vm.order.software.id);
                }

                if (vm.order.reportType) {
                    watchArray.push(vm.order.reportType);
                }

                return watchArray;
            },
            changeOrderPrice,
            true
        );

        activate();

        ////////////////

        function activate() {
            initOrderModel($stateParams.orderId);
            initSoftware();

            getReportTypes();
            getLabels();
            getStates();
        }

        function initOrderModel(orderId) {
            if (orderId) {
                OrderModel.find(orderId, {cacheResponse: false, bypassCache: true}).then(function (order) {
                    order = OrderModel.inject(order);
                    angular.extend(vm.order, order);
                });
            } else {
                OrderModel.create({software_id: currentUser.software_id}, {cacheResponse: false}).then(function (order) {
                    vm.order.id =  order.id;
                    vm.order.price =  order.price;
                });
            }
        }

        function initSoftware() {
            if (currentUser.software_id) {
                SoftwareService.getSoftwares().then(function (softwares) {
                    vm.order.software = $filter('filter')(softwares, {id:currentUser.software_id})[0];
                    getCloneFiles();
                });
            } else {
                openSelectSoftwareModal();
            }
        }

        function openSelectSoftwareModal() {
            var modalInstance = $modal.open({
                size: "lg",
                templateUrl: 'app/order/createOrder/templates/selectSoftware.html',
                windowClass: 'select-software',
                controller: 'SelectSoftwareController',
                controllerAs: 'selectSoftwareController',
                keyboard: false,
                backdrop: 'static'
            });

            return modalInstance.result
                .then(function (selectedSoftware) {
                    vm.order.software = selectedSoftware;

                    getCloneFiles();
                });
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
                    clone.software_id = vm.order.software.id;
                    vm.cloneFiles.push(clone);
                    vm.order.clone = clone;
                })
                .catch(function () {
                    vm.order.clone = {};
                });
        }

        function openOrderSummaryModal() {
            $modal.open({
                templateUrl: 'app/order/createOrder/templates/orderSummaryModal.html',
                controller: 'ModalSummaryController',
                controllerAs: 'createOrderCtrl',
                resolve: {
                    createOrderCtrl: function () {
                        return vm;
                    }
                }
            });
        }

        function openUploadReportSampleModal() {
            var modalInstance = $modal.open({
                templateUrl: 'app/order/createOrder/templates/uploadReportSample.html',
                controller: 'UploadReportSampleController',
                controllerAs: 'uploadReportSampleCtrl',
                backdrop: 'static'
            });

           return modalInstance.result
                .then(function (sample) {
                    sample.report_type_id = vm.order.reportType;
                    vm.reportSamples.push(sample);
                    vm.order.reportSample = sample;
                })
                .catch(function () {
                    vm.order.reportSample = {};
                });
        }


        function getActiveStepTitle() {
            return vm.formWizard.stepsTitles['step' + vm.formWizard.activeStep];
        }

        function getStepTitle(step) {
            return vm.formWizard.stepsTitles['step' + step];
        }

        function watchCloneType(newValue, oldValue) {
            if (newValue && newValue.id == "new" && newValue !== oldValue) {
                openUploadCloneFileModal();
            }
        }

        function watchReportSample(newValue, oldValue) {
            if (newValue && newValue.id == "new" && newValue !== oldValue) {
                openUploadReportSampleModal();
            }
        }

        function watchReportType(newValue, oldValue) {
            if (newValue !== oldValue) {
                getReportSamples();
            }
        }

        function watchPhotos(newPhotos) {
            if (newPhotos != undefined) {
                if (!newPhotos.length) {
                    vm.order.photos = null;
                }
                newPhotos.forEach(function (photo, index, photos) {
                    //IS FILE?
                    if (photo.size) {
                        photos[index] = {file: photo, label: null};
                    }
                });
            }
        }


        function watchMainPhoto(newMainPhoto, oldMainPhoto) {
            //Change Main photo if mainphoto have label
            if (newMainPhoto != undefined &&
                oldMainPhoto != undefined &&
                newMainPhoto.file &&
                newMainPhoto.file.name == oldMainPhoto.file.name &&
                newMainPhoto.label) {
                for (var i = 0; i < vm.order.photos.length; i++) {
                    if (!vm.order.photos[i].label) {
                        vm.mainPhoto = i;
                        break;
                    }
                }
            }
        }


        function watchCollectDataType() {
            vm.order.collectData.file = null;
        }

        function changeOrderPrice() {
            var order = vm.order,
                request = {},
                send = false;

            if (order.software) {
                request.software_id = order.software.id;
                send = true;
            }
            if (order.reportType) {
                request.report_type_id = order.reportType;
                send = true;
            }

            if (send) {
                order.DSUpdate(request).then(function (order) {
                    vm.order.price = order.price;
                });
            }
        }


        function goToNextStep() {
            goToStep(vm.formWizard.activeStep + 1)
        }

        function goToPrevStep() {
            goToStep(vm.formWizard.activeStep - 1)
        }

        function goToStep(stepNumber) {
            var activeForm = getActiveForm();

            if (vm.formWizard.activeStep > stepNumber) {
                vm.formWizard.activeStep = stepNumber;
            } else if (vm.formWizard.activeStep < stepNumber) {
                if (activeForm.$valid) {
                    if (activeForm.$dirty) {
                        vm.order.save(vm.formWizard.activeStep)
                            .then(function () {
                                if (stepNumber === CHECKOUT_STEP) {
                                    endOrder(+vm.order.orderTransaction.is_free);
                                }

                                vm.formWizard.activeStep = stepNumber;
                                if (vm.formWizard.progressStep < stepNumber) {
                                    vm.formWizard.progressStep++;
                                }

                                vm.order.uploadProgress = 0;

                                activeForm.$setPristine();
                            })
                            .catch(function (resp) {
                                toaster.pop({
                                    type: "error",
                                    body: "cj-error",
                                    bodyOutputType: 'directive',
                                    directiveData: {errors: resp.data.errors}
                                });
                                vm.order.uploadProgress = 0;
                            });
                    } else {
                        if (stepNumber === CHECKOUT_STEP) {
                            endOrder(+vm.order.orderTransaction.is_free);
                        }
                        vm.formWizard.activeStep = stepNumber;
                    }

                } else {
                    activeForm.showValidation = true;
                }
            }
        }

        function endOrder(free) {
            if (free) {
                $window.location.href = "/dashboard";
            } else {
                $state.go("home.order.checkout", {orderId: vm.order.id});
            }
        }

        function getActiveForm() {
            return vm.formWizard.forms['step' + vm.formWizard.activeStep + 'Form'];
        }

        function addComparables() {
            vm.order.comparables.push({});
        }

        function removeComparableByIndex(index) {
            vm.order.comparables.splice(index, 1);
        }

        function getCloneFiles() {
            attachmentService.getCloneFiles().then(function (cloneFiles) {
                cloneFiles.push({
                    id: "new",
                    label: "new",
                    software_id: vm.order.software.id
                });
                vm.cloneFiles = cloneFiles;

                //if clone file selected
                if (vm.order.clone && vm.order.clone.id) {
                    vm.order.clone = $filter('filter')(cloneFiles, {id:+vm.order.clone.id}, true)[0];
                }

            });
        }

        function getReportTypes() {
            reportTypesService.getReportTypes().then(function (reportTypes) {
                vm.reportTypes = reportTypes;
            });
        }

        function getLabels() {
            LabelModel.findAll().then(function (labels) {
                vm.labels = labels;
            });
        }

        function getStates() {
            statesService.getStates().then(function (states) {
                vm.states = states;
            });
        }

        function getReportSamples() {
            attachmentService.getReportSampleFiles().then(function (reportSamples) {
                reportSamples.push({
                    id: "new",
                    label: "new",
                    report_type_id: vm.order.reportType
                });
                vm.reportSamples = reportSamples;

                //if report sample selected
                if (vm.order.reportSample && vm.order.reportSample.id) {
                    vm.order.reportSample = $filter('filter')(reportSamples, {id:+vm.order.reportSample.id}, true)[0];
                }
            });
        }

        function getTextByValue(selectArray, value) {
            if (selectArray) {
                for (var i = 0; i < selectArray.length; i++) {
                    if (selectArray[i].value === value) {
                        return selectArray[i].text;
                    }
                }
            }

        }

        function removePhotoByIndex(index) {
            if (index === vm.mainPhoto) {
                vm.mainPhoto = 0;
            } else if(vm.mainPhoto) {
                --vm.mainPhoto;
            }
            vm.order.photos.splice(index, 1);

            if (!vm.order.photos.length) {
                vm.order.photos = null;
                vm.emptyPhotos = true;
            }
        }

        function onSelectLabel($item, $select) {
            if (!$item && !$select.clickTriggeredSelect) {
                addNewLabel($select.search).then(function (newLabel) {
                    vm.order.photos[vm.mainPhoto].label = newLabel;
                });
            }

            $select.search = "";
        }

        function addNewLabel(label) {
            return LabelModel.create({name:label}).then(function (newLabel) {
                vm.labels.push(newLabel);

                return newLabel;
            });
        }


        function hasUploadFiles() {
            for(var key in vm.order.files) {
                var file = vm.order.files[key];

                if (file.file && !file.has) {
                    return true;
                }
            }

            if (vm.order.miscellaneousFiles && vm.order.miscellaneousFiles.length) {
                return true;
            }

            return false;
        }

        function hasSomethingInCollectDataFile() {
            for(var key in vm.order.files) {
                var file = vm.order.files[key];

                if (file.has && (!file.collectDataType || vm.order.collectData.type === file.collectDataType)) {
                    return true;
                }
            }

            return false;
        }

        function onSelectPhotos(newFiles, files, file, duplicateFiles, invalidFiles, event, input) {
            //if its D&D
            if (event instanceof MouseEvent) {
                if (!vm.order.photos) {
                    vm.order.photos = [];
                }

                var valid = true;
                angular.forEach(newFiles, function (newFile) {
                    for(var i = 0;i < invalidFiles.length;i++) {
                        if (invalidFiles[i].lastModified === newFile.lastModified) {
                            input.$ngfValidations = [{name:invalidFiles[i].$error, valid: false}];
                            input.$setValidity(invalidFiles[i].$error, false);
                            input.$setDirty();
                            valid = false;

                            return;
                        }
                    }
                    vm.order.photos.push(newFile);
                });

                if (valid) {
                    input.$setViewValue(vm.order.photos, true);
                    input.$error = {};
                    input.$validate();
                }
            }
            if (vm.emptyPhotos) {
                for(var i = 0; i < newFiles.length; i++) {
                    if (newFiles[i].$error) {
                        vm.order.photos = null;
                        input.$setViewValue(vm.order.photos, true);
                        return;
                    }
                }
                vm.order.photos = newFiles;

                input.$setViewValue(vm.order.photos, true);

                vm.emptyPhotos = false;
            }
            if (!invalidFiles.length) {
                input.$valid = true;
                input.$invalid = false;
            }
        }

        function reset5StepForm() {
            var form = vm.formWizard.forms.step5Form,
                photos = form.photos;

            if (vm.order.photos) {
                photos.$error = {};
                photos.$ngfValidations = [];
                photos.$validate();
                angular.forEach(form.$error, function (error, key) {
                    if (error[0].$name === photos.$name) {
                        delete form.$error[key];
                    }
                });

                if (!Object.keys(form.$error).length) {
                    form.$valid = true;
                    form.$invalid = false;
                }
            }
        }


        function removeLabelByIndex(index) {
            var removedLabel = vm.labels[index];

            angular.forEach(vm.order.photos, function (photo) {
               if (photo.label && photo.label.id === removedLabel.id) {
                   photo.label = null;
               }
            });

            vm.labels[index].DSDestroy();

            vm.labels.splice(index, 1);
        }

        function isFileRequired(file) {
            return (file && (file.id || file.key)) ? false : true;
        }
    }

})();