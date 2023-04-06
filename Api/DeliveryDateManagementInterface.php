<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Api;

use Primak\CustomShipping\Api\Data\DeliveryDateInterface;

/**
 * @api
 */
interface DeliveryDateManagementInterface
{

    /**
     * @param int $quoteId
     * @return DeliveryDateInterface
     */
    public function getByQuoteAttributeId(int $quoteId): DeliveryDateInterface;

    /**
     * @param int $orderId
     * @return DeliveryDateInterface
     */
    public function getByOrderAttributeId(int $orderId): DeliveryDateInterface;

    /**
     * @param $quoteId
     * @param $selectDate
     * @return void
     */
    public function saveSelectDateToQuote($quoteId, $selectDate): void;

    /**
     * @param $orderId
     * @param $quoteId
     * @return void
     */
    public function saveOrderIdToSelectDate($orderId, $quoteId): void;

    /**
     * @param $quoteId
     * @param $selectDate
     * @return void
     */
    public function updateSelectDateToQuote($quoteId, $selectDate): void;
}
