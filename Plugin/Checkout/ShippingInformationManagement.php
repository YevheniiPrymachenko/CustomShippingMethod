<?php

namespace Primak\CustomShipping\Plugin\Checkout;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Primak\CustomShipping\Model\Config\Config;
use Primak\CustomShipping\Api\DeliveryDateManagementInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ShippingInformationManagement
 */
class ShippingInformationManagement
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var DeliveryDateManagementInterface
     */
    private DeliveryDateManagementInterface $management;

    /**
     * ShippingInformationManagement constructor.
     *
     * @param Config $config
     * @param DeliveryDateManagementInterface $management
     */
    public function __construct(
        Config                          $config,
        DeliveryDateManagementInterface $management
    )
    {
        $this->config = $config;
        $this->management = $management;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     *
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
                                                              $cartId,
        ShippingInformationInterface                          $addressInformation
    )
    {
        $extensionAttributes = $addressInformation->getShippingAddress()->getExtensionAttributes();

        if (!$extensionAttributes || !$this->config->getIsActive()) {
            return [$cartId, $addressInformation];
        }

        try {
            $deliveryDateFromDatabase = $this->management->getByQuoteAttributeId($cartId);

            if (!$deliveryDateFromDatabase->getQuoteAttributeId()) {
                $this->management->saveSelectDateToQuote($cartId, $extensionAttributes->getDeliveryDate());
            } else {
                $this->management->updateSelectDateToQuote($cartId, $extensionAttributes->getDeliveryDate());
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }

        return [$cartId, $addressInformation];
    }
}
