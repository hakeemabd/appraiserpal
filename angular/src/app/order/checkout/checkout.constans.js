(function () {
    'use strict';

    angular
        .module('appraiserpal.order.checkout')
        .constant("CARD_TYPES", {
            'VISA': 'Visa',
            'MASTERCARD': 'MasterCard',
            'AMERICAN_EXPRESS': 'American Express',
            'DISCOVER': 'Discover'
        })
})();
