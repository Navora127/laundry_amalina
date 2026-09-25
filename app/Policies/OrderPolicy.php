<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isStaff() || $user->isCustomer();
    }

    public function view(User $user, Order $order): bool
    {
        return $user->isStaff() || $this->belongsToCustomer($user, $order);
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    public function update(User $user, Order $order): bool
    {
        return $user->isStaff();
    }

    public function delete(User $user, Order $order): bool
    {
        return false;
    }

    public function restore(User $user, Order $order): bool
    {
        return false;
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }

    public function manageStatus(User $user, Order $order): bool
    {
        return $user->isStaff();
    }

    public function approvePrice(User $user, Order $order): bool
    {
        return $user->isStaff()
            || ($this->belongsToCustomer($user, $order) && $order->status === OrderStatus::AwaitingPriceApproval);
    }

    public function cancel(User $user, Order $order): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        return $this->belongsToCustomer($user, $order)
            && $order->status === OrderStatus::AwaitingConfirmation;
    }

    private function belongsToCustomer(User $user, Order $order): bool
    {
        return $user->customer !== null && $order->isOwnedBy($user->customer);
    }
}
