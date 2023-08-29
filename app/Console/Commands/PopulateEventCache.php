<?php

namespace App\Console\Commands;

use App\Http\Controllers\EventCacheController;
use App\Models\Donation;
use App\Models\Follower;
use App\Models\MerchSale;
use App\Models\Subscriber;
use Illuminate\Console\Command;

class PopulateEventCache extends Command
{
    private $eventCacheController;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-event-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
        $this->eventCacheController = new EventCacheController();
    }
   /**

    /**
     * Execute the console command.
     */
    public function handle()
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

    private function populateDisplayMessages($data, $eventName)
    {
        foreach ($data as $item) {
            $this->populateCache($eventName, $item);
        }
    }

    private function populateCache($eventName, $item)
    {
        $formattedDate = $item->eventTime->format('Y_m_d');
        switch ($eventName) {
            case 'follow':
                $this->eventCacheController->addFollower($formattedDate, $item);
                break;
            case 'subscribe':
                $this->eventCacheController->addSubscriber($formattedDate, $item);
                break;
            case 'donate':
                $this->eventCacheController->addDonation($formattedDate, $item);
                break;
            case 'merch_sale':
                $this->eventCacheController->addMerchSale($formattedDate, $item);
                break;
            default:
                break;
        }
    }
}
