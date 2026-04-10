<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class WebhookEventController extends Controller
{
    public function index(): View
    {
        $events = auth()
            ->user()
            ->webhookEvents()
            ->with('repository')
            ->latest('received_at')
            ->limit(100)
            ->get();

        return view('webhook-events.index', [
            'events' => $events,
        ]);
    }
}