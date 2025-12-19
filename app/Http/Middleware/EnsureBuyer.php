<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class EnsureBuyer
{
    public function handle($request, Closure $next)
    {
        $id = session('buyer_user_id');
        if (! $id) {
            return redirect('/buyer/login');
        }

        $user = User::find($id);
        if (! $user || ($user->role ?? null) !== 'buyer') {
            session()->forget('buyer_user_id');
            return redirect('/buyer/login');
        }

        return $next($request);
    }
}
