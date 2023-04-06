<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * class Config
 */
class Config
{
    const XML_PATH_CUSTOMSHIPPING_ACTIVE = 'carriers/customshipping/active';
    const XML_PATH_CUSTOMSHIPPING_TITLE = 'carriers/customshipping/title';
    const XML_PATH_CUSTOMSHIPPING_NAME = 'carriers/customshipping/name';
    const XML_PATH_CUSTOMSHIPPING_SHIPPING_COST = 'carriers/customshipping/shipping_cost';
    const XML_PATH_CUSTOMSHIPPING_WEEKENDS_COST = 'carriers/customshipping/weekends_cost';
    const XML_PATH_CUSTOMSHIPPING_HOLIDAYS_COST = 'carriers/customshipping/holidays_cost';
    const XML_PATH_CUSTOMSHIPPING_WEEKENDS = 'carriers/customshipping/weekends';
    const XML_PATH_CUSTOMSHIPPING_HOLIDAYS = 'carriers/customshipping/holidays';
    const DAY_MONTH_YEAR_SLASH = 'dd/mm/yy';
    const IS_USUAL_DAY = 1;
    const IS_WEEKEND_DAY = 2;
    const IS_HOLIDAY_DAY = 3;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;


    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface  $serializer,
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_CUSTOMSHIPPING_ACTIVE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMSHIPPING_TITLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMSHIPPING_NAME, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getShippingCost(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMSHIPPING_SHIPPING_COST, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getWeekendsCost(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMSHIPPING_WEEKENDS_COST, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getHolidaysCost(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMSHIPPING_HOLIDAYS_COST, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getWeekends(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMSHIPPING_WEEKENDS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getHolidays(): array
    {
        return json_decode($this->scopeConfig->getValue(self::XML_PATH_CUSTOMSHIPPING_HOLIDAYS, ScopeInterface::SCOPE_STORE), true);
    }

    /**
     * @return mixed|string
     */
    public function getDateFormat()
    {
        return self::DAY_MONTH_YEAR_SLASH;
    }
}
