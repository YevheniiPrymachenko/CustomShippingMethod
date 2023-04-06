<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Model\Carrier;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Psr\Log\LoggerInterface;
use Primak\CustomShipping\Model\Config\Config;
use Primak\CustomShipping\Api\DeliveryDateManagementInterface;
use Magento\Checkout\Model\Session;

/**
 * class Calculate
 */
class Calculate
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var DeliveryDateManagementInterface
     */
    private $management;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @param LoggerInterface $logger
     * @param Config $config
     * @param DeliveryDateManagementInterface $management
     * @param TimezoneInterface $timezone
     * @param Session $checkoutSession
     */
    public function __construct(
        LoggerInterface                             $logger,
        Config                                      $config,
        DeliveryDateManagementInterface             $management,
        TimezoneInterface                           $timezone,
        Session $checkoutSession
    )
    {
        $this->logger = $logger;
        $this->config = $config;
        $this->management = $management;
        $this->timezone = $timezone;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return int
     */
    public function calculateRate(): int
    {
        try {
            $quoteId = $this->getQuoteId();
            $deliveryDate = $this->management->getByQuoteAttributeId((int)$quoteId);

            if ($deliveryDate->getDeliveryDate()) {
                return $this->getRate($deliveryDate->getDeliveryDate());
            }

        } catch (\Exception $exception) {
            $this->logger->alert($exception);
        }
        return Config::IS_USUAL_DAY;
    }

    /**
     * @return int|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getQuoteId(): mixed
    {
        $quote = $this->checkoutSession->getQuote();
        return $quote->getId();
    }

    /**
     * @param $deliveryDate
     * @return int
     */
    private function getRate($deliveryDate): int
    {
        if ($this->checkIsHoliday($deliveryDate)) {
            return Config::IS_HOLIDAY_DAY;
        }

        $weekends = $this->config->getWeekends();
        $day = $this->timezone->date($deliveryDate)->format('N');
        if ($day == 7) {
            $day = '0';
        }
        if ($weekends && str_contains($weekends, $day)) {
            return Config::IS_WEEKEND_DAY;
        }
        return Config::IS_USUAL_DAY;
    }

    /**
     * @param $deliveryDate
     * @return bool
     */
    private function checkIsHoliday($deliveryDate): bool
    {
        $selectedYear = $this->timezone->date($deliveryDate)->format('Y');
        $selectedDateNumber = $this->timezone->date($deliveryDate)->format('z');
        $holidays = $this->config->getHolidays();
        foreach ($holidays as $holiday) {
            $holidayDateNumber = $this->timezone->date($holiday['holiday'] . '/' . $selectedYear)->format('z');
            if ($holidayDateNumber == $selectedDateNumber) {
                return true;
            }
        }
        return false;
    }
}
