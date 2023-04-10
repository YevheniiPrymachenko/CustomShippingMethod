<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Primak\CustomShipping\Model\Config\Config;

/**
 * class LayoutProcessor
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config,
    )
    {
        $this->config = $config;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if ($this->config->getIsActive()) {
            $attributeCode = 'delivery_date';
            $customField = [
                'component' => 'Magento_Ui/js/form/element/date',
                'config' => [
                    'customScope' => 'shippingAddress.custom_attributes',
                    'customEntry' => null,
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/date',
                    'tooltip' => [
                        'description' => 'Delivery date',
                    ],
                ],
                'dataScope' => 'shippingAddress.custom_attributes' . '.' . $attributeCode,
                'label' => 'Delivery date',
                'provider' => 'checkoutProvider',
                'sortOrder' => 180,
                'validation' => [
//                'required-entry' => true,
                    'validate-date-range' => true,
                    'validate-date' => true
                ],
                'options' => ['minDate' => 0],
                'filterBy' => null,
                'customEntry' => null,
                'visible' => true,
                'value' => '' // value field is used to set a default value of the attribute
            ];

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'][$attributeCode] = $customField;

        }
        return $jsLayout;
    }
}
