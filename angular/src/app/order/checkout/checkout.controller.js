(function () {
    "use strict";

    angular
        .module("appraiserpal.order.checkout")
        .controller("CheckoutController", CheckoutController);

    CheckoutController.$inject = [
        "$scope",
        "$state",
        "$stateParams",
        "$window",
        "$http",
        "$filter",
        "CARD_TYPES",
        "StatesService",
        "moment",
        "OrderModel",
        "LabelModel",
        "currentUser",
        "stripe",
        "toaster"
    ];

    /* @ngInject */
    function CheckoutController(
        $scope,
        $state,
        $stateParams,
        $window,
        $http,
        $filter,
        CARD_TYPES,
        statesService,
        moment,
        OrderModel,
        LabelModel,
        currentUser,
        stripe,
        toaster
    ) {
        var vm = this,
            currentYear = moment().year();

        vm.getLabelCountInPhotos = getLabelCountInPhotos;
        vm.returnToEditOrder = returnToEditOrder;
        vm.applyPromoCode = applyPromoCode;
        vm.submitLoader = false;
        vm.submit = submit;

        vm.cardTypes = {
            "visa": CARD_TYPES.VISA,
            "mastercard": CARD_TYPES.MASTERCARD,
            "amex": CARD_TYPES.AMERICAN_EXPRESS,
            "discover": CARD_TYPES.DISCOVER
        };

        vm.cardMonths = moment.monthsShort();
        vm.cardYears = getCardYears();

        vm.order = {};
        vm.currentUser = currentUser;
        vm.hasCard = !!+currentUser.stripe_active;
        vm.canDelayedPayment = !!+currentUser.delayed_payment;

        OrderModel.find($stateParams.orderId, {bypassCache: true}).then(function (order) {
            angular.extend(vm.order, order);
        });

        vm.promoCodeApplied = false;

        vm.billingInfo = {
            firstName: currentUser.first_name,
            lastName: currentUser.last_name,
            address1: currentUser.adress_line_1,
            address2: currentUser.adress_line_2,
            city: currentUser.city,
            state: currentUser.state,
            zip: currentUser.zip,
            card: {}
        };

        $scope.$watchCollection("checkoutForm.cardNumber", watchCardType);

        activate();

        /////////////////////////////////

        function activate() {
            statesService.getStates().then(function (states) {
                 vm.states = states;
            });
            getLabels();
            getPaypalLink();

        }

        function getLabels() {
            LabelModel.findAll().then(function (labels) {
                vm.labels = labels;
            });
        }

        function getPaypalLink() {
            $http.get("/api/paypal/" + $stateParams.orderId).then(function (response) {
                vm.paypalUrl = response.data.url;
            });
        }

        function getCardYears() {
            var years = [];

            for(var i = 0; i < 10; i++) {
                years.push(currentYear++);
            }

            return years;
        }

        function getLabelCountInPhotos(label) {
            var labels = $filter("filter")(vm.order.photos, {label: {id: label.id}}, true);

            return (labels) ? labels.length : 0;
        }

        function submit(form, payNow) {
            if (form.$valid) {
                var billingInfo = vm.billingInfo,
                    card = billingInfo.card;

                vm.submitLoader = true;

                if (vm.hasCard) {
                    checkout(billingInfo, payNow);
                } else {
                    stripe.card.createToken({
                        number: card.number,
                        cvc: card.ccv,
                        exp_month: card.month,
                        exp_year: card.year,
                        name: billingInfo.firstName + " " + billingInfo.lastName,
                        address_line1: billingInfo.address1,
                        address_line2: billingInfo.address2,
                        address_city: billingInfo.city,
                        address_state: billingInfo.state,
                        address_zip: billingInfo.zip
                    }).then(function (responce) {
                        checkout(billingInfo, payNow, responce.id);
                    });
                }
            } else {
                form.showValidation = true;
            }

        }

        function returnToEditOrder() {
            $state.go("home.order.edit", {orderId: $stateParams.orderId});
        }

        function watchCardType(newCardType) {
            if (newCardType) {
                vm.billingInfo.card.type = newCardType.$ccType;
            }
        }

        function checkout(billingInfo, payNow, stripeId) {
            var data = (stripeId) ? {token: stripeId} : {},
                userBillinInfo = {
                    first_name: billingInfo.firstName,
                    last_name: billingInfo.lastName,
                    address_line_1: billingInfo.address1,
                    address_line_2: billingInfo.address2,
                    state: billingInfo.state,
                    city: billingInfo.city,
                    zip: billingInfo.zip,
                    license_number: currentUser.license_number,
                    mobile_phone: currentUser.mobile_phone,
                    standard_instructions: currentUser.standard_instructions,
                    work_phone: currentUser.work_phone,
                    pay_now: +payNow
                };

            return $http.post("/api/stripe/" + $stateParams.orderId, angular.extend(data, userBillinInfo))
                .then(function () {
                    $window.location.href = "/dashboard";
                });
        }


        function applyPromoCode() {
            $http.get("/api/promo-code/" + vm.order.orderTransaction.id + "/code/" + vm.billingInfo.promoCode)
                .then(function (response) {
                    vm.promoCodeApplied = true;
                    vm.order.orderTransaction = response.data;
                })
                .catch(function () {
                    toaster.pop('error', "", "Invalid Promo Code");
                });
        }
    }
})();

