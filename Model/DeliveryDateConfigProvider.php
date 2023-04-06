<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Primak\CustomShipping\Model\Config\Config;

/**
 * Class DeliveryDateConfigProvider
 */
class DeliveryDateConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * DeliveryDateConfigProvider constructor.
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config                $config,
        StoreManagerInterface $storeManager
    )
    {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array|array[]
     */
    public function getConfig(): array
    {
        if (!$this->config->getIsActive()) {
            return [];
        }

        return ['deliveryDateConfig' => $this->getDeliveryConfig()];
    }

    /**
     * @return array
     */
    private function getDeliveryConfig(): array
    {
        return [
            'deliveryDateFormat' => $this->config->getDateFormat(),
            'deliveryDaysWeekends' => $this->config->getWeekends(),
            'deliveryDateHolidays' => $this->config->getHolidays()
        ];
    }
}
