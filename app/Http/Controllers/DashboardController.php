<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isStaff()) {
            return redirect()->route('admin.dashboard');
        }

        $orders = Order::query()
            ->forCustomer($user->customer)
            ->with('items')
            ->latest()
            ->limit(5)
            ->get();

        return view('customer.dashboard', compact('orders'));
    }
}
