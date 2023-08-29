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
use Illuminate\Support\Facades\Redis;
use SplObjectStorage;
use SplPriorityQueue;

class EventCacheController
{
    private $followerTag = 'follower';
    private $subscriberTag = 'subscriber';
    private $donationTag = 'donation';
    private $merchSaleTag = 'merchSale';


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
        $result = Redis::hGet($this->getCacheKey($this->merchSaleTag, $date), $merchSale->itemName);
        if ($result == null) {
            $result = 0;
        }

        $result += ($merchSale->amount * $merchSale->price);
        Redis::hSet($this->getCacheKey($this->merchSaleTag, $date), $merchSale->itemName, $result);
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
            $result = Redis::get($key) ?: 0;
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
        $interval = new DateInterval('P1D');
        $datePeriod = new DatePeriod($startDate, $interval, $endDate);

        $totalRevenue = 0;
        foreach ($datePeriod as $date) {
            $key = $this->getCacheKey($this->donationTag, $date->format('Y_m_d'));
            $result = Redis::get($key) ?: 0;
            $totalRevenue += $result;

            $key = $this->getCacheKey($this->subscriberTag, $date->format('Y_m_d'));
            $result = Redis::get($key) ?: 0;
            $totalRevenue += $result;

            $key = $this->getCacheKey($this->merchSaleTag, $date->format('Y_m_d'));
            $sales = Redis::hGetAll($key) ?: [];
            foreach ($sales as $sale) {
                $totalRevenue += $sale;
            }
            $totalRevenue += $result;
        }
        return (int)$totalRevenue;
    }

    public function getTopMerchSales($topN, $days)
    {
        $currentDate = new DateTime();
        $startDate = clone $currentDate;
        $startDate->sub(new DateInterval($days));

        $endDate = $currentDate;
        $interval = new DateInterval('P1D');
        $datePeriod = new DatePeriod($startDate, $interval, $endDate);


        $map = array();
        foreach ($datePeriod as $date) {
            $key = $this->getCacheKey($this->merchSaleTag, $date->format('Y_m_d'));
            $sales = Redis::hGetAll($key) ?: [];
            foreach ($sales as $name => $value) {
                if (array_key_exists($name, $map)) {
                    $map[$name] = $map[$name] + $value;
                } else {
                    $map[$name] = $value;
                }
            }
        }

        // Use priority queue to sort merch sales.
        $pq = new SplPriorityQueue();
        foreach ($map as $name => $value) {
            $pq->insert($name, $value);
        }

        $top = 0;
        $topSales = array();
        while($top < $topN) {
            $topSales[] = $pq->current();
            $pq->next();
            $top++;
        }
        return $topSales;
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
