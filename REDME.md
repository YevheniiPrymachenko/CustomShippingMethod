# Custom Shipping Method with Delivery Date. Module for Magento 2

This module helps the buyer choose the desired delivery date and the store to set extra prices for delivery in weekends and holidays.

- [Setup](#setup)
    - [Composer installation](#composer-installation)
    - [Setup the module](#setup-the-module)
- [Features](#features)
- [Settings](#settings)
- [Documentation](#documentation)

## Setup

Magento 2 Open Source or Commerce edition is required. At this moment this module for Magento 2.4 version

### Composer installation

Run the following composer command:

```
composer require primak/custom-shipping
```

### Setup the module

Run the following magento command:

```
bin/magento setup:upgrade
```

**If you are in production mode, do not forget to recompile and redeploy the static resources.**

## Features

This module helps the buyer choose the desired delivery date and the store to set extra prices for delivery in weekends and holidays in Magento 2 store.

Administrator can configure delivery module from admin panel and set special prices for specific date.

- Administrator can:
    - select weekends day and set extra price for weekends
    - select holidays and set extra price for holidays (holidays price more priority the weekends)
    - select the countries in which this method will be available.

## Documentation

### How to configure a delivery method from admin panel

The configuration can be found in the Magento 2 admin panel under:

Store->Configuration->Sales->Delivery Methods->Custom Shipping Module

- Here base settings:
  - Enabled - enable delivery method.
  - Title - set title for method.
  - Method Name - set name for method.
  - Default shipping Cost - set default price for usual days.
  - Weekends shipping Cost - set extra price for weekends.
  - Holidays shipping Cost - set holidays extra price.
  - Weekends - select weekends.
  - Holidays - add holidays.

<h3>Shipping method config</h3>
![delivery method admin](https://raw.githubusercontent.com/YevheniiPrymachenko/-/main/admin_panel_module_config.png)
![delivery method admin_calendar](https://raw.githubusercontent.com/YevheniiPrymachenko/-/main/admin_panel_add_holiday.png)


### How it looks to the customer side

- At the checkout, an additional field appears in the address entry form in which you can select the desired delivery date:
    - If the customer selects weekend date, then the shipping price for the method changes based on the settings.
    - If the customer selects holiday date, then the shipping price for the method changes based on the settings for holiday.
    - Holidays have more priority then regular days and weekends.
    - The administrator can see the selected date on the admin order detail page.
    - The customer can see the selected date in customer account on order detail page
    - 
<h3>Checkout Address Page</h3>
![delivery method admin](https://raw.githubusercontent.com/YevheniiPrymachenko/-/main/address_select_calendar.png)
![delivery method admin](https://raw.githubusercontent.com/YevheniiPrymachenko/-/main/address_select_holiday.png)
![delivery method admin](https://raw.githubusercontent.com/YevheniiPrymachenko/-/main/address_select_weekend.png)

<h3>Order Summary</h3>
![delivery method admin](https://raw.githubusercontent.com/YevheniiPrymachenko/-/main/order_summary.png)

<h3>Admin Sales Order Detail Page</h3>
![delivery method admin](https://raw.githubusercontent.com/YevheniiPrymachenko/-/main/admin_sales_order_view.png)

<h3>Customer Account Order Detail Page</h3>
![delivery method admin](https://raw.githubusercontent.com/YevheniiPrymachenko/-/main/customer_account_order_detail.png)



### How it in the code work


For delivery date use extension_attribute `delivery_date` for customer address. Declare in file `extension_attributes.xml`:

file `etc/extension_attributes.xml`
```xml
...
<extension_attributes for="Magento\Quote\Api\Data\AddressInterface">
    <attribute code="delivery_date" type="string"/>
</extension_attributes>
...
```

For this attribute created `db_schema.xml`:

file `etc/db_schema.xml`
```xml
<?xml version="1.0"?>
<schema xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd">
    <table name="primak_date_attribute" resource="default" engine="innodb" comment="Date Attributes Table">
        <column xsi:type="int" name="entity_id" padding="10" unsigned="true" nullable="false" identity="true"
                comment="Entity ID"/>
        <column xsi:type="int" name="quote_attribute_id" padding="10" unsigned="true" nullable="true" identity="false"
                comment="Quote ID"/>
        <column xsi:type="int" name="order_attribute_id" padding="10" unsigned="true" nullable="true" identity="false"
                comment="Order ID"/>
        <column xsi:type="varchar" name="delivery_date" nullable="false" length="255" comment="delivery_date"/>
        <constraint xsi:type="primary" referenceId="PRIMARY">
            <column name="entity_id"/>
        </constraint>
        <constraint xsi:type="foreign" referenceId="PRIMAK_DATE_ATTRS_QUOTE_ATTRIBUTE_ID_QUOTE_ENTT_ID"
                    table="primak_date_attribute" column="quote_attribute_id" referenceTable="quote"
                    referenceColumn="entity_id" onDelete="CASCADE"/>
        <constraint xsi:type="foreign" referenceId="PRIMAK_DATE_ATTRS_ORDER_ATTRIBUTE_ID_SALES_ORDER_ENTT_ID"
                    table="primak_date_attribute" column="order_attribute_id" referenceTable="sales_order"
                    referenceColumn="entity_id" onDelete="CASCADE"/>
    </table>
</schema>
```

Field add in Layout Processor `LayoutProcessor.php`, and declare this processor in `di.xml`:

file `etc/frontend/di.xml`
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="Magento\Checkout\Block\Onepage">
        <arguments>
            <argument name="layoutProcessors" xsi:type="array">
                <item name="checkoutComment" xsi:type="object">Primak\CustomShipping\Block\Checkout\LayoutProcessor</item>
            </argument>
        </arguments>
    </type>
</config>
```

file `Block\Checkout\LayoutProcessor.php`
```php
<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * class LayoutProcessor
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
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
                'validate-date-range' => true,
                'validate-date'=> true
            ],
            'options' => ['minDate'=> 0],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'value' => '' // value field is used to set a default value of the attribute
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'][$attributeCode] = $customField;

        return $jsLayout;
    }
}
```

For delivery method declare `system.xml`, and in `config.xml` file add model `<model>Primak\CustomShipping\Model\Carrier\Customshipping</model>`:

file `adminhtml/system.xml`
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Config:etc/system_file.xsd">
    <system>
        <section id="carriers" translate="label" type="text" sortOrder="320" showInDefault="1" showInWebsite="1" showInStore="1">
            <group id="customshipping" translate="label" type="text" sortOrder="900" showInDefault="1" showInWebsite="1" showInStore="1">
                <label>Custom Shipping Module</label>
                <field id="active" translate="label" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="0" canRestore="1">
                    <label>Enabled</label>
                    <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                </field>
                <field id="title" translate="label" type="text" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="0">
                    <label>Title</label>
                </field>
                <field id="name" translate="label" type="text" sortOrder="30" showInDefault="1" showInWebsite="1" showInStore="0">
                    <label>Method Name</label>
                </field>
                <field id="shipping_cost" translate="label" type="text" sortOrder="40" showInDefault="1" showInWebsite="1" showInStore="0" >
                    <label>Default shipping Cost</label>
                    <validate>validate-number validate-zero-or-greater</validate>
                </field>
                <field id="weekends_cost" translate="label" type="text" sortOrder="42" showInDefault="1" showInWebsite="1" showInStore="0" >
                    <label>Weekends shipping Cost</label>
                    <validate>validate-number validate-zero-or-greater</validate>
                </field>
                <field id="holidays_cost" translate="label" type="text" sortOrder="43" showInDefault="1" showInWebsite="1" showInStore="0" >
                    <label>Holidays shipping Cost</label>
                    <validate>validate-number validate-zero-or-greater</validate>
                </field>
                <field id="weekends" translate="label comment" type="multiselect" sortOrder="45" showInDefault="1" showInWebsite="1" showInStore="1" canRestore="1">
                    <label>Weekends</label>
                    <source_model>Magento\Config\Model\Config\Source\Locale\Weekdays</source_model>
                    <can_be_empty>1</can_be_empty>
                    <comment>Allows admins to choose the weekends.</comment>
                </field>
                <field id="holidays" translate="label comment" type="select" sortOrder="50" showInDefault="1" showInWebsite="1" showInStore="1">
                    <label>Holidays</label>
                    <frontend_model>Primak\CustomShipping\Block\Adminhtml\Config\Backend\Holidays</frontend_model>
                    <backend_model>Magento\Config\Model\Config\Backend\Serialized\ArraySerialized</backend_model>
                    <comment>Allows admins to choose any holidays.</comment>
                </field>
                <field id="sallowspecific" translate="label" type="select" sortOrder="60" showInDefault="1" showInWebsite="1" showInStore="0" canRestore="1">
                    <label>Ship to Applicable Countries</label>
                    <frontend_class>shipping-applicable-country</frontend_class>
                    <source_model>Magento\Shipping\Model\Config\Source\Allspecificcountries</source_model>
                </field>
                <field id="specificcountry" translate="label" type="multiselect" sortOrder="70" showInDefault="1" showInWebsite="1" showInStore="0">
                    <label>Ship to Specific Countries</label>
                    <source_model>Magento\Directory\Model\Config\Source\Country</source_model>
                    <can_be_empty>1</can_be_empty>
                </field>
                <field id="showmethod" translate="label" type="select" sortOrder="80" showInDefault="1" showInWebsite="1" showInStore="0">
                    <label>Show Method if Not Applicable</label>
                    <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                    <frontend_class>shipping-skip-hide</frontend_class>
                </field>
                <field id="sort_order" translate="label" type="text" sortOrder="90" showInDefault="1" showInWebsite="1" showInStore="0">
                    <label>Sort Order</label>
                </field>
            </group>
        </section>
    </system>
</config>

```

file `Model/Carrier/Customshipping.php`
```php
<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Primak\CustomShipping\Model\Config\Config;
use Psr\Log\LoggerInterface;

/**
 * Custom shipping model
 */
class Customshipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'customshipping';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    private $rateMethodFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Calculate
     */
    private $calculate;


    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param Config $config
     * @param Calculate $calculate
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory,
        \Psr\Log\LoggerInterface                                    $logger,
        \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        Config                                                      $config,
        Calculate                                                   $calculate,
        array                                                       $data = []
    )
    {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->config = $config;
        $this->calculate = $calculate;
    }

    /**
     * @param RateRequest $request
     * @return false|\Magento\Shipping\Model\Rate\Result
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $shippingCost = $this->getRate();

        $method->setPrice($shippingCost);
        $method->setCost($shippingCost);

        $result->append($method);

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return [$this->_code => $this->getConfigData('name')];
    }
...
```

Also in file `Customshipping.php` add method `getRate()` for adding shipping price, based on selected date

method `getRate()` in file `Model/Carrier/Customshipping.php`
```php
...
    /**
    * @return float
    */
    private function getRate(): float
    {
    $rate = $this->calculate->calculateRate();
    if ($rate == Config::IS_HOLIDAY_DAY) {
    return (float)$this->config->getHolidaysCost();
    } elseif ($rate == Config::IS_WEEKEND_DAY) {
    return (float)$this->config->getWeekendsCost();
    } else {
    return (float)$this->getConfigData('shipping_cost');
    }
    }
```

Calculation shipping price do in file `Calculate.php`:

file `Model/Carrier/Calculate.php`
```php
<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Model\Carrier;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Psr\Log\LoggerInterface;
use Primak\CustomShipping\Model\Config\Config;
use Primak\CustomShipping\Api\DeliveryDateManagementInterface;
use Magento\Checkout\Model\Session;

/**
 * class Calculate
 */
class Calculate
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var DeliveryDateManagementInterface
     */
    private $management;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @param LoggerInterface $logger
     * @param Config $config
     * @param DeliveryDateManagementInterface $management
     * @param TimezoneInterface $timezone
     * @param Session $checkoutSession
     */
    public function __construct(
        LoggerInterface                             $logger,
        Config                                      $config,
        DeliveryDateManagementInterface             $management,
        TimezoneInterface                           $timezone,
        Session $checkoutSession
    )
    {
        $this->logger = $logger;
        $this->config = $config;
        $this->management = $management;
        $this->timezone = $timezone;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return int
     */
    public function calculateRate(): int
    {
        try {
            $quoteId = $this->getQuoteId();
            $deliveryDate = $this->management->getByQuoteAttributeId((int)$quoteId);

            if ($deliveryDate->getDeliveryDate()) {
                return $this->getRate($deliveryDate->getDeliveryDate());
            }

        } catch (\Exception $exception) {
            $this->logger->alert($exception);
        }
        return Config::IS_USUAL_DAY;
    }

    /**
     * @return int|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getQuoteId(): mixed
    {
        $quote = $this->checkoutSession->getQuote();
        return $quote->getId();
    }

    /**
     * @param $deliveryDate
     * @return int
     */
    private function getRate($deliveryDate): int
    {
        if ($this->checkIsHoliday($deliveryDate)) {
            return Config::IS_HOLIDAY_DAY;
        }

        $weekends = $this->config->getWeekends();
        $day = $this->timezone->date($deliveryDate)->format('N');
        if ($day == 7) {
            $day = '0';
        }
        if ($weekends && str_contains($weekends, $day)) {
            return Config::IS_WEEKEND_DAY;
        }
        return Config::IS_USUAL_DAY;
    }

    /**
     * @param $deliveryDate
     * @return bool
     */
    private function checkIsHoliday($deliveryDate): bool
    {
        $selectedYear = $this->timezone->date($deliveryDate)->format('Y');
        $selectedDateNumber = $this->timezone->date($deliveryDate)->format('z');
        $holidays = $this->config->getHolidays();
        foreach ($holidays as $holiday) {
            $holidayDateNumber = $this->timezone->date($holiday['holiday'] . '/' . $selectedYear)->format('z');
            if ($holidayDateNumber == $selectedDateNumber) {
                return true;
            }
        }
        return false;
    }
}
```

For check selected date and save selected date and `quote_id` to db used Plugin `BeforeEstimateByExtendedAddress.php`:

file `Plugin/Model/Quote/BeforeEstimateByExtendedAddress.php`
```php
<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Plugin\Model\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\ShipmentEstimationInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use Primak\CustomShipping\Api\DeliveryDateManagementInterface;
use Primak\CustomShipping\Model\Config\Config;
use Psr\Log\LoggerInterface;

/**
 * class ShipmentEstimation
 */
class BeforeEstimateByExtendedAddress
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var DeliveryDateManagementInterface
     */
    private $management;

    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private $maskedQuoteIdToQuoteId;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ShippingInformationManagement constructor.
     *
     * @param Config $config
     * @param DeliveryDateManagementInterface $management
     * @param MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config                          $config,
        DeliveryDateManagementInterface $management,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        LoggerInterface                 $logger
    )
    {
        $this->config = $config;
        $this->management = $management;
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->logger = $logger;
    }

    /**
     * @param ShipmentEstimationInterface $subject
     * @param $cartId
     * @param AddressInterface $address
     * @return mixed
     * @throws LocalizedException
     */
    public function beforeEstimateByExtendedAddress(
        \Magento\Quote\Api\ShipmentEstimationInterface $subject,
                                                       $cartId,
        \Magento\Quote\Api\Data\AddressInterface       $address
    )
    {
        if ($this->config->getIsActive()) {
            $quoteId = $cartId;

            if (!is_numeric($quoteId)) {
                $quoteId = $this->getQuoteIdByMask($quoteId);
            }

            if ($address->getExtensionAttributes()->getDeliveryDate())
                try {
                    $deliveryDateFromDatabase = $this->management->getByQuoteAttributeId((int)$quoteId);

                    if (!$deliveryDateFromDatabase->getQuoteAttributeId()) {
                        $this->management->saveSelectDateToQuote((int)$quoteId, $address->getExtensionAttributes()->getDeliveryDate());
                    } else {
                        $this->management->updateSelectDateToQuote((int)$quoteId, $address->getExtensionAttributes()->getDeliveryDate());
                    }
                } catch (\Exception $e) {
                    throw new LocalizedException(__($e->getMessage()));
                }

        }
        return [$cartId, $address];
    }

    /**
     * @param string $mask
     * @return int|null
     */
    private function getQuoteIdByMask(string $mask): ?int
    {
        try {
            $id = $this->maskedQuoteIdToQuoteId->execute($mask);
        } catch (\Exception $exception) {
            $this->logger->alert($exception);

            return null;
        }

        return $id;
    }
}
```

Plugin `OrderManagementPlugin.php` save after order place `order_id` for `delivery_date` to db:

file `Plugin/OrderManagementPlugin.php`
```php
<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Primak\CustomShipping\Api\DeliveryDateManagementInterface;

/**
 * Class OrderManagementPlugin
 */
class OrderManagementPlugin
{
    /**
     * @var DeliveryDateManagementInterface
     */
    protected DeliveryDateManagementInterface $management;

    /**
     * @param DeliveryDateManagementInterface $management
     */
    public function __construct(
        DeliveryDateManagementInterface $management
    )
    {
        $this->management = $management;
    }

    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface $order
     *
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPlace(
        OrderManagementInterface $subject,
        OrderInterface           $order
    ): object
    {
        $quoteId = $order->getQuoteId();
        if ($quoteId) {
            $this->management->saveOrderIdToSelectDate($order->getEntityId(), $quoteId);
        }
        return $order;
    }
}

```



For display delivery date on admin sales order view page added block in layout `sales_order_view.xml`, template `delivery-information.phtml`: 

file `view/adminhtml/layout/sales_order_view.xml`
```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceBlock name="order_tab_info">
            <block class="Magento\Framework\View\Element\Template" name="order_delivery_date"
                   template="Primak_CustomShipping::order/view/delivery-information.phtml">
                <arguments>
                    <argument name="viewModelDeliveryDate" xsi:type="object">
                        Primak\CustomShipping\ViewModel\DeliveryDate
                    </argument>
                </arguments>
            </block>
        </referenceBlock>
    </body>
</page>

```

file `view/adminhtml/templates/order/view/delivery-information.phtml`
```php
<?php
/** @var Primak\CustomShipping\ViewModel\DeliveryDate $viewModelDeliveryDate */
$viewModelDeliveryDate = $block->getData('viewModelDeliveryDate');
$orderId = $this->getRequest()->getParam('order_id');
if (isset($viewModelDeliveryDate)) {
    if ($deliveryDate = $viewModelDeliveryDate->getOrderDeliveryDate((int)$orderId)) { ?>
        <div class="admin__page-section-item-content delivery-information">
            <br><strong><?= $block->escapeHtml(__('Delivery On:')) ?> </strong>
            <span><?= $block->escapeHtml($deliveryDate) ?></span>
        </div>

        <script type="text/javascript">
            require(['jquery'], function ($) {
                $('.order-shipping-method').append($('.delivery-information'));
            });
        </script>
        <?php
    }
}

```

For display delivery date on customer account order detail page added block in layout `sales_order_view.xml`, template `delivery-information.phtml`:

file `etc/frontend/di.xml`
```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceBlock name="content">
            <block class="Magento\Framework\View\Element\Template" name="order_delivery_date"
                   template="Primak_CustomShipping::order/view/delivery-information.phtml" after="sales.order.info">
                <arguments>
                    <argument name="viewModelDeliveryDate" xsi:type="object">
                        Primak\CustomShipping\ViewModel\DeliveryDate
                    </argument>
                </arguments>
            </block>
        </referenceBlock>
    </body>
</page>
```

file `etc/frontend/di.xml`
```php
<?php
/** @var Primak\CustomShipping\ViewModel\DeliveryDate $viewModelDeliveryDate */
$viewModelDeliveryDate = $block->getData('viewModelDeliveryDate');
$orderId = $this->getRequest()->getParam('order_id');
$deliveryDate = '';

if (isset($viewModelDeliveryDate)) {
    if ($getDeliveryDate = $viewModelDeliveryDate->getOrderDeliveryDate((int)$orderId)) {
        $deliveryDate = $getDeliveryDate;
    }
}
?>

<div class="box-content delivery-information">
    <?php if ($deliveryDate) : ?>
        <br><strong><?= $block->escapeHtml(__('Delivery On:')) ?></strong>
        <br><span><?= $block->escapeHtml($deliveryDate) ?></span>
    <?php endif; ?>
</div>

<script type="text/javascript">
    require(['jquery'], function ($) {
        $('.box-order-shipping-method').append($('.delivery-information'));
    });
</script>

```

For both block used ViewModel `DeliveryDate.php` and plugin `OrderViewTabInfo.php`:

file `ViewModel/DeliveryDate.php`
```php
<?php

declare(strict_types=1);

namespace Primak\CustomShipping\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Primak\CustomShipping\Api\DeliveryDateManagementInterface;

/**
 * class DeliveryDate
 */
class DeliveryDate implements ArgumentInterface
{
    /**
     * @var DeliveryDateManagementInterface
     */
    protected DeliveryDateManagementInterface $management;

    /**
     * @param DeliveryDateManagementInterface $management
     */
    public function __construct(
        DeliveryDateManagementInterface $management
    )
    {
        $this->management = $management;
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function getOrderDeliveryDate(int $orderId): string
    {
        $orderDate = $this->management->getByOrderAttributeId($orderId);
        if ($deliveryDate = $orderDate->getDeliveryDate()) {
            return $deliveryDate;
        }
        return '';
    }
}
```

file `Plugin/OrderViewTabInfo.php`
```php
<?php

declare(strict_types=1);

namespace Primak\CustomShipping\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Primak\CustomShipping\Api\DeliveryDateManagementInterface;

/**
 * class DeliveryDate
 */
class DeliveryDate implements ArgumentInterface
{
    /**
     * @var DeliveryDateManagementInterface
     */
    protected DeliveryDateManagementInterface $management;

    /**
     * @param DeliveryDateManagementInterface $management
     */
    public function __construct(
        DeliveryDateManagementInterface $management
    )
    {
        $this->management = $management;
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function getOrderDeliveryDate(int $orderId): string
    {
        $orderDate = $this->management->getByOrderAttributeId($orderId);
        if ($deliveryDate = $orderDate->getDeliveryDate()) {
            return $deliveryDate;
        }
        return '';
    }
}
```
