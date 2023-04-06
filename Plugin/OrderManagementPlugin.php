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
