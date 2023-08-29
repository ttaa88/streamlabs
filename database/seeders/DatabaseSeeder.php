<?php

namespace Database\Seeders;

use App\Models\Follower;
use App\Models\Donation;
use App\Models\Subscriber;
use App\Models\MerchSale;
use App\Models\DisplayMessage;


use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //$this->populateFollowersTable();
        //$this->populateSubscribersTable();
        //$this->populateDonationsTable();
        //$this->populateMerchSalesTable();
        $this->populateDisplayMessagesTable();
    }

    private function populateFollowersTable(): void
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 400; $i++) {
            Follower::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail,
                'eventTime' => $this->generateRandomTimestamp(),
                'isRead' => 0,
            ]);
        }
    }

    private function populateSubscribersTable(): void
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 400; $i++) {
            Subscriber::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail,
                'tier' => rand(1, 3),
                'eventTime' => $this->generateRandomTimestamp(),
                'isRead' => 0,
            ]);
        }
    }

    private function populateDonationsTable(): void
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 400; $i++) {
            Donation::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail,
                'amount' => rand(1, 100),
                'currency' => 'USD',
                'message' => 'Thank you for being awesome',
                'eventTime' => $this->generateRandomTimestamp(),
                'isRead' => 0,
            ]);
        }
    }

    private function populateMerchSalesTable(): void
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 400; $i++) {
            MerchSale::create([
                'name' => $faker->name(),
                'itemName' => $faker->name(),
                'amount' => rand(1, 100),
                'price' => rand(100, 1000) / rand(1, 10),
                'eventTime' => $this->generateRandomTimestamp(),
                'isRead' => 0,
            ]);
        }
    }

    private function populateDisplayMessagesTable(): void
    {
        $followers = Follower::all();
        $subscribers = Subscriber::all();
        $donations = Donation::all();
        $merchSales = MerchSale::all();

        $this->populateDisplayMessages($followers, 'follow');
        $this->populateDisplayMessages($subscribers, 'subscribe');
        $this->populateDisplayMessages($donations, 'donate');
        $this->populateDisplayMessages($merchSales, 'merch_sale');
    }

    private function generateRandomTimestamp()
    {
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();

        $timestamp = mt_rand($startDate->getTimestamp(), $endDate->getTimestamp());

        return Carbon::createFromTimestamp($timestamp);
    }

    private function populateDisplayMessages($data, $eventName)
    {
        foreach ($data as $item) {
            $formattedMessage = $this->formatMessage($eventName, $item);
            DisplayMessage::create([
                'eventName' => $eventName,
                'eventTime' => $item->eventTime,
                'formattedMessage' => $formattedMessage,
            ]);
        }
    }

    private function formatMessage($eventName, $item)
    {
        switch ($eventName) {
            case 'follow':
                return "{$item->name} followed you!";
            case 'subscribe':
                return "{$item->name} (Tier{$item->tier}) subscribed to you!";
            case 'donate':
                return "{$item->name} donated {$item->amount} {$item->currency} to you!\n{$item->message}";
            case 'merch_sale':
                $totalAmount = $item->amount * $item->price;
                return "{$item->name} bought some fancy {$item->itemName} from you for {$totalAmount} USD!";
            default:
                return '';
        }
    }
}