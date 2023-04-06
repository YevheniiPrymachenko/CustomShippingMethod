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
