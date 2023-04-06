<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * class DeliveryDateResource
 */
class DeliveryDateResource extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('primak_date_attribute', 'entity_id');
    }
}
