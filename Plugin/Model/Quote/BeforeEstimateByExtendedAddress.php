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
