<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Plugin;

use Magento\Sales\Block\Adminhtml\Order\View\Tab\Info;

/**
 * Class OrderViewTabInfo
 */
class OrderViewTabInfo
{
    /**
     * @param Info $subject
     * @param $result
     *
     * @return string
     */
    public function afterGetGiftOptionsHtml(Info $subject, $result): string
    {
        $result .= $subject->getChildHtml('order_delivery_date');

        return $result;
    }
}
