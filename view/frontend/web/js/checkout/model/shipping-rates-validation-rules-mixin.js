define([
    'jquery',
    'mage/utils/wrapper'
],function ($, Wrapper) {
    "use strict";

    return function (origRules)
    {
        origRules.getObservableFields = Wrapper.wrap(
            origRules.getObservableFields,
            function (originalAction)
            {
                let fields = originalAction();
                fields.push('delivery_date');

                return fields;
            }
        );

        return origRules;
    };
});
