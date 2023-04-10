<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Primak\CustomShipping\Api\DeliveryDateManagementInterface;
use Primak\CustomShipping\Model\Config\Config;

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
     * @var Config
     */
    private $config;

    /**
     * @param DeliveryDateManagementInterface $management
     * @param Config $config
     */
    public function __construct(
        DeliveryDateManagementInterface $management,
        Config $config
    )
    {
        $this->management = $management;
        $this->config = $config;
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
        if ($this->config->getIsActive()) {
            $quoteId = $order->getQuoteId();
            if ($quoteId) {
                $this->management->saveOrderIdToSelectDate($order->getEntityId(), $quoteId);
            }
        }
        return $order;
    }
}
