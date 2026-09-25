<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\WaNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WaNotificationController extends Controller
{
    public function __construct(private readonly WaNotificationService $notifications) {}

    public function store(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('view', $order);
        $result = $this->notifications->prepare($order, $request->user());

        return response()->json([
            'url' => $result['url'],
            'message' => 'WhatsApp siap dibuka. Status pengiriman tetap tidak diketahui sampai dikirim secara manual.',
        ]);
    }
}
