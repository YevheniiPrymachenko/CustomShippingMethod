<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Api\Data;

/**
 * @api
 */
interface DeliveryDateInterface
{
    public const QUOTE_ATTRIBUTE_ID = 'quote_attribute_id';
    public const ORDER_ATTRIBUTE_ID = 'order_attribute_id';
    public const DELIVERY_DATE = 'delivery_date';

    /**
     * @return int
     */
    public function getQuoteAttributeId(): int;

    /**
     * @return int
     */
    public function getOrderAttributeId(): int;

    /**
     * @return string
     */
    public function getDeliveryDate(): string;
}
