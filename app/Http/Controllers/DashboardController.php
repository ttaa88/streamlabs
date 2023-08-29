<?php

namespace App\Http\Controllers;

use App\Http\Controllers\EventCacheController;
use App\Models\DisplayMessage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $eventCacheController;
    /**
     * Seed the application's database.
     */

     public function __construct()
     {
         $this->eventCacheController = new EventCacheController();
     }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            'displayMessages' => $this->getDisplayMessages($request),
            'totalFollowers' => $this->eventCacheController->getTotalFollowers('P30D'),
            'totalRevenue' => $this->eventCacheController->getTotalRevenue('P30D'),
            'topMerchSales' => $this->eventCacheController->getTopMerchSales(3, 'P30D'),
        ]);
    }

    private function getDisplayMessages(Request $request) {
        $perPage = 100;

        // Determine the page number from the request
        $page = $request->get('page', 1);
        return DisplayMessage::orderBy('eventTime', 'desc')->paginate($perPage, ['*'], 'page', $page);
    }
}