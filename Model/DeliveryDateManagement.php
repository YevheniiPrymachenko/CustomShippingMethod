<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Model;

use Primak\CustomShipping\Api\Data\DeliveryDateInterface;
use Primak\CustomShipping\Api\DeliveryDateManagementInterface;
use Primak\CustomShipping\Model\DeliveryDateFactory;
use Primak\CustomShipping\Model\ResourceModel\DeliveryDateResource;

/**
 * class DeliveryDateManagement
 */
class DeliveryDateManagement implements DeliveryDateManagementInterface
{
    /**
     * @var DeliveryDateResource
     */
    private DeliveryDateResource $resource;

    /**
     * @var DeliveryDateFactory
     */
    private DeliveryDateFactory $factory;

    /**
     * @param DeliveryDateResource $resource
     * @param DeliveryDateFactory $attributesFactory
     */
    public function __construct(
        DeliveryDateResource $resource,
        DeliveryDateFactory  $attributesFactory
    )
    {
        $this->resource = $resource;
        $this->factory = $attributesFactory;
    }

    /**
     * @param int $quoteId
     * @return DeliveryDateInterface
     */
    public function getByQuoteAttributeId(int $quoteId): DeliveryDateInterface
    {
        $object = $this->getNewInstance();
        $this->resource->load($object, $quoteId, 'quote_attribute_id');

        return $object;
    }

    /**
     * @param int $orderId
     * @return DeliveryDateInterface
     */
    public function getByOrderAttributeId(int $orderId): DeliveryDateInterface
    {
        $object = $this->getNewInstance();
        $this->resource->load($object, $orderId, 'order_attribute_id');

        return $object;
    }

    /**
     * @return DeliveryDateInterface
     */
    public function getNewInstance(): DeliveryDateInterface
    {
        return $this->factory->create();
    }

    /**
     * @param $quoteId
     * @param $selectDate
     * @return void
     */
    public function saveSelectDateToQuote($quoteId, $selectDate): void
    {
        $data[] = ['quote_attribute_id' => $quoteId, 'delivery_date' => (string)$selectDate];

        $this->resource->getConnection()->insertMultiple($this->resource->getTable('primak_date_attribute'), $data);
    }

    /**
     * @param $quoteId
     * @param $selectDate
     * @return void
     */
    public function updateSelectDateToQuote($quoteId, $selectDate): void
    {
        $data = ['delivery_date' => (string)$selectDate];

        $this->resource->getConnection()->update($this->resource->getTable('primak_date_attribute'), $data, ['quote_attribute_id = ?' => $quoteId]);
    }

    /**
     * @param $orderId
     * @param $quoteId
     * @return void
     */
    public function saveOrderIdToSelectDate($orderId, $quoteId): void
    {
        $data = ['order_attribute_id' => (int)$orderId];

        $this->resource->getConnection()->update($this->resource->getTable('primak_date_attribute'), $data, ['quote_attribute_id = ?' => $quoteId]);
    }
}
