<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class EnsureSeller
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        $id = session('seller_user_id');
        if (! $id) {
            return redirect('/seller/login');
        }

        $user = User::find($id);
        if (! $user || ($user->role ?? null) !== 'seller') {
            session()->forget('seller_user_id');
            return redirect('/seller/login');
        }

        return $next($request);
    }
}
