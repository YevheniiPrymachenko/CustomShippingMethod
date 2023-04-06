<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Model\ResourceModel\DeliveryDate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Primak\CustomShipping\Model\DeliveryDate;
use Primak\CustomShipping\Model\ResourceModel\DeliveryDateResource;

/**
 * class Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(DeliveryDate::class, DeliveryDateResource::class);
    }
}
