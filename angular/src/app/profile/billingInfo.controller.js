(function () {
    "use strict";

    angular
        .module("appraiserpal.profile")
        .controller("BillingInfoController", BillingInfoController);

    BillingInfoController.$inject = [
        "$scope",
        "$state",
        "$stateParams",
        "$windows",
        "$http",
        "CARD_TYPES",
        "StatesService",
        "moment",
        "currentUser",
        "stripe"
    ];

    /* @ngInject */
    function BillingInfoController(
        $scope,
        $state,
        $stateParams,
        $window,
        $http,
        CARD_TYPES,
        statesService,
        moment,
        currentUser,
        stripe
    ) {
        var vm = this,
            currentYear = moment().year();

        vm.submit = submit;

        vm.cardTypes = {
            "visa": CARD_TYPES.VISA,
            "mastercard": CARD_TYPES.MASTERCARD,
            "amex": CARD_TYPES.AMERICAN_EXPRESS,
            "discover": CARD_TYPES.DISCOVER
        };

        vm.cardMonths = moment.monthsShort();
        vm.cardYears = getCardYears();

        vm.currentUser = currentUser;

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

        $scope.$watchCollection("billingInfoForm.cardNumber", watchCardType);

        activate();

        /////////////////////////////////

        function activate() {
            statesService.getStates().then(function (states) {
                vm.states = states;
            });
        }

        function getCardYears() {
            var years = [];

            for(var i = 0; i < 10; i++) {
                years.push(currentYear++);
            }

            return years;
        }

        function submit(form) {
            if (form.$valid) {
                var billingInfo = vm.billingInfo,
                    card = billingInfo.card;

                if (vm.hasCard) {
                    checkout(currentUser.stripe_id);
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
                        checkout(responce.id);
                    });
                }
            } else {
                form.showValidation = true;
            }

        }


        function watchCardType(newCardType) {
            if (newCardType) {
                vm.billingInfo.card.type = newCardType.$ccType;
            }
        }

        function checkout(stripeId) {
            return $http.post("/api/stripe/" + $stateParams.orderId, {
                token: stripeId
            }).then(function () {
                $window.location.href = "/profile";
            });
        }

    }
})();

