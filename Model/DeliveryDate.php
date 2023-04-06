<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Model;

use Magento\Framework\Model\AbstractModel;
use Primak\CustomShipping\Api\Data\DeliveryDateInterface;
use Primak\CustomShipping\Model\ResourceModel\DeliveryDateResource;

/**
 * class DeliveryDate
 */
class DeliveryDate extends AbstractModel implements DeliveryDateInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(DeliveryDateResource::class);
    }

    /**
     * @return int
     */
    public function getQuoteAttributeId(): int
    {
        return (int) $this->getData(self::QUOTE_ATTRIBUTE_ID);
    }

    /**
     * @return int
     */
    public function getOrderAttributeId(): int
    {
        return (int) $this->getData(self::ORDER_ATTRIBUTE_ID);
    }

    /**
     * @return string
     */
    public function getDeliveryDate(): string
    {
        return (string) $this->getData(self::DELIVERY_DATE);
    }
}
