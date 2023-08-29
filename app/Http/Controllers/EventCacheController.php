<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Follower;
use App\Models\Subscriber;
use App\Models\MerchSale;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Predis\Client;

class EventCacheController
{
    private $followerTag = 'follower';
    private $subscriberTag = 'subscriber';
    private $donationTag = 'donation';
    private $merchSaleTag = 'merchSale';

    private $merchTag = 'merchSale';

    private $redisClient;

    public function __construct()
    {
        $this->redisClient = new Client([
            'host'   => 'redis',
            'port'   => 6379,
        ]);
    }

    public function addFollower($date, Follower $follower)
    {
        $result = Redis::get($this->getCacheKey($this->followerTag, $date)) ?: 0;
        $result += 1;
        Redis::set($this->getCacheKey($this->followerTag, $date), $result);
    }

    public function addSubscriber($date, Subscriber $subscriber)
    {
        $result = Redis::get($this->getCacheKey($this->subscriberTag, $date)) ?: 0;
        $result += ($subscriber->tier * 5);
        Redis::set($this->getCacheKey($this->subscriberTag, $date), $result);
    }

    public function addDonation($date, Donation $donation)
    {
        $result = Redis::get($this->getCacheKey($this->donationTag, $date)) ?: 0;
        $result += $donation->amount;
        Redis::set($this->getCacheKey($this->donationTag, $date), $result);
    }

    public function addMerchSale($date, MerchSale $merchSale)
    {
        // A sorted set of merch, score represents total sales.
        $result = Redis::zScore($this->getCacheKey($this->merchSaleTag, $date), $merchSale->itemName) ?: 0;
        $result += ($merchSale->amount * $merchSale->price);
        if ($result != 0) {
            Redis::zRem($this->getCacheKey($this->merchSaleTag, $date), $merchSale->itemName);
        }
        Redis::zAdd($this->getCacheKey($this->merchSaleTag, $date), $result, $merchSale->itemName);

        $result = Redis::get($this->getCacheKey($this->merchTag, $date)) ?: 0;
        $result += ($merchSale->amount * $merchSale->price);
        Redis::set($this->getCacheKey($this->merchTag, $date), $result);
    }

    public function getValues($key)
    {
        return Redis::get($key) ?: [];
    }

    public function getTotalFollowers($days)
    {
        $currentDate = new DateTime();
        $startDate = clone $currentDate;
        $startDate->sub(new DateInterval($days));

        $endDate = $currentDate;

        // Define the step interval (1 day)
        $interval = new DateInterval('P1D');

        // Create a DatePeriod object to iterate through the time differences
        $datePeriod = new DatePeriod($startDate, $interval, $endDate);

        $totalCount = 0;
        // Iterate through the time differences
        foreach ($datePeriod as $date) {
            $key = $this->getCacheKey($this->followerTag, $date->format('Y_m_d'));
            $result = (int)$this->redisClient->get($key) ?: 0;
            $totalCount += $result;
        }
        return $totalCount;
    }

    public function getTotalRevenue($days)
    {
        $currentDate = new DateTime();
        $startDate = clone $currentDate;
        $startDate->sub(new DateInterval($days));

        $endDate = $currentDate;

        // Define the step interval (1 day)
        $interval = new DateInterval('P1D');

        // Create a DatePeriod object to iterate through the time differences
        $datePeriod = new DatePeriod($startDate, $interval, $endDate);

        $totalRevenue = 0;
        // Iterate through the time differences
        foreach ($datePeriod as $date) {
            $key = $this->getCacheKey($this->donationTag, $date->format('Y_m_d'));
            $result = (int)$this->redisClient->get($key) ?: 0;
            $totalRevenue += $result;

            $key = $this->getCacheKey($this->subscriberTag, $date->format('Y_m_d'));
            $result = (int)$this->redisClient->get($key) ?: 0;
            $totalRevenue += $result;

            $key = $this->getCacheKey($this->merchSaleTag, $date->format('Y_m_d'));
            $result = (int)$this->redisClient->get($key) ?: 0;
            $totalRevenue += $result;
        }
        return $totalRevenue;
    }

    public function getTopMerchSales($topN, $days)
    {
        $currentDate = new DateTime();
        $startDate = clone $currentDate;
        $startDate->sub(new DateInterval($days));

        $endDate = $currentDate;

        // Define the step interval (1 day)
        $interval = new DateInterval('P1D');

        // Create a DatePeriod object to iterate through the time differences
        $datePeriod = new DatePeriod($startDate, $interval, $endDate);

        $totalRevenue = 0;
        // Iterate through the time differences
        foreach ($datePeriod as $date) {
            $key = $this->getCacheKey($this->merchSaleTag, $date->format('Y_m_d'));
            $result = (int)$this->redisClient->zRange($key, -1, -3) ?: 0;
        }
        return $totalRevenue;
    }

    private function getCacheKey($tag, $date)
    {
        return "{$tag}_{$date}";
    }

    private function calMinuteDifferent($eventTime)
    {
        $formattedEventTime = $eventTime->format('Y_m_d');
        $currentDate = Carbon::now();
        $formattedCurrentTime = $currentDate->format('Y_m_d');

        $startDate = DateTime::createFromFormat('Y_m_d', $formattedEventTime);
        $endDate = DateTime::createFromFormat('Y_m_d', $formattedCurrentTime);

        $timeDifference = $endDate->diff($startDate);
        return ($timeDifference->days * 24 * 60);
    }
}
