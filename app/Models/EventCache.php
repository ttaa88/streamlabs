<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCache extends Model
{
    use HasFactory;

    public function addFollower($key, Follower $follower) {
        if (!isset($this->cachedFollowers[$key])) {
            $this->cachedFollowers = [];
        } 
        array_push($this->cachedFollowers[$key], $follower);
    }

    public function addSubscriber($key, Subscriber $subscriber) {
        if (!isset($this->cachedSubscribers[$key])) {
            $this->cachedSubscribers = [];
        } 
        array_push($this->cachedSubscribers[$key], $subscriber);
    }

    public function addDonation($key, Donation $donation) {
        if (!isset($this->cachedDonations[$key])) {
            $this->cachedDonations = [];
        } 
        array_push($this->cachedDonations[$key], $donation);
    }

    public function addMerchSale($key, MerchSale $merchSale) {
        if (!isset($this->cachedMerchSales[$key])) {
            $this->cachedMerchSales = [];
        } 
        array_push($this->cachedMerchSales[$key], $merchSale);
    }

    public function getFollowers($key, Follower $follower) {
        if (isset($this->cachedFollowers[$key])) {
            return $this->cachedFollowers[$key];
        }
        return null;
    }

    public function getSubscribers($key, Subscriber $subscriber) {
        if (isset($this->cachedSubscribers[$key])) {
            return $this->cachedSubscribers[$key];
        }
        return null;
    }

    public function getDonations($key, Donation $donation) {
        if (isset($this->cachedDonations[$key])) {
            return $this->cachedDonations[$key];
        }
        return null;
    }

    public function getMerchSales($key, MerchSale $merchSale) {
        if (isset($this->cachedMerchSales[$key])) {
            return $this->cachedMerchSales[$key];
        }
        return null;
    }
}