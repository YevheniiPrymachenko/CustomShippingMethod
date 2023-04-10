<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Plugin;

use Magento\Sales\Block\Adminhtml\Order\View\Tab\Info;
use Primak\CustomShipping\Model\Config\Config;

/**
 * Class OrderViewTabInfo
 */
class OrderViewTabInfo
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config,
    )
    {
        $this->config = $config;
    }

    /**
     * @param Info $subject
     * @param $result
     *
     * @return string
     */
    public function afterGetGiftOptionsHtml(Info $subject, $result): string
    {
        if ($this->config->getIsActive()) {
            $result .= $subject->getChildHtml('order_delivery_date');
        }

        return $result;
    }
}
