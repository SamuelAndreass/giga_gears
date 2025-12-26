<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;
use Carbon\Carbon;

class OrderPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Order $order)
    {
        if ($user->is_admin) return true;
        if ($user->id === $order->user_id) return true; // pelanggan
        if ($user->is_seller) {
            return $order->items()
                ->whereHas('product', fn($q) => $q->where('store_id', $user->store_id))
                ->exists();
        }
        return false;
    }

    public function ship(User $user, Order $order)
    {

        if (! $user->is_seller) return false;

        $hasItemFromSeller = $order->items()
            ->whereHas('product', fn($q) => $q->where('store_id', $user->store_id))
            ->exists();

        return $hasItemFromSeller && $order->status === 'processing';
    }

    public function cancelBySeller(User $user, Order $order)
    {
        if (! $user->is_seller) return false;

        $hasItemFromSeller = $order->items()
            ->whereHas('product', fn($q) => $q->where('store_id', $user->store_id))
            ->exists();

        return $hasItemFromSeller && in_array($order->status, ['pending', 'processing']);
    }

    // Admin-only actions
    public function markProcessing(User $user, Order $order)
    {
        return $user->is_admin && $order->status === 'pending';
    }

    public function markCancelled(User $user, Order $order)
    {
        return $user->is_admin && in_array($order->status, ['pending', 'processing']);
    }

    public function markDelivered(User $user, Order $order)
    {
        if ($user->is_admin) return $order->status === 'shipped';
        if ($user->id === $order->user_id) return $order->status === 'shipped';
        return false;
    }

    public function markCompleted(User $user, Order $order)
    {
        if ($user->is_admin) return $order->status === 'delivered';
        if ($user->id === $order->user_id) return $order->status === 'delivered';
        return false;
    }
}
