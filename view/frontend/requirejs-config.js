var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Primak_CustomShipping/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/model/shipping-rates-validation-rules': {
                'Primak_CustomShipping/js/checkout/model/shipping-rates-validation-rules-mixin': true
            },
            'Magento_Checkout/js/model/shipping-rate-processor/new-address': {
                'Primak_CustomShipping/js/model/shipping-rate-processor/new-address-mixin': true
            }
        }
    }
};
