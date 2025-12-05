<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SellerAuthController
{
    public function showLogin()
    {
        return view('seller_login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return redirect()->back()->withErrors(['email' => 'Credentials not match']);
        }

        // require seller role (previously admin)
        if (! isset($user->role) || $user->role !== 'seller') {
            return redirect()->back()->withErrors(['email' => 'User is not a seller']);
        }

        // simple session-based seller
        session(['seller_user_id' => $user->id]);

        return redirect('/seller/manage/orders');
    }

    public function logout()
    {
        session()->forget('seller_user_id');
        return redirect('/seller/login');
    }

    protected function ensureAdmin()
    {
        $id = session('seller_user_id');
        if (! $id) {
            abort(403, 'Unauthorized');
        }

        $user = User::find($id);
        if (! $user || $user->role !== 'seller') {
            abort(403, 'Unauthorized');
        }
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $this->ensureAdmin();

        $q = $request->query('q');

        $query = Order::orderBy('created_at', 'desc');
        if ($q) {
            $query->where('order_number', 'like', "%{$q}%")
                  ->orWhere('buyer_name', 'like', "%{$q}%");
        }

        $orders = $query->paginate(15)->withQueryString();
        return view('seller_orders', ['orders' => $orders, 'q' => $q]);
    }
    
    public function dashboard()
    {
        $this->ensureAdmin();
        return view('seller.dashboard');
    }

    public function show($order_number)
    {
        $this->ensureAdmin();
        $order = Order::where('order_number', $order_number)->firstOrFail();
        return view('seller_order_show', ['order' => $order]);
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdmin();
        $order = Order::findOrFail($id);
        $data = $request->validate([
            'buyer_name' => 'required|string',
            'buyer_phone' => 'required|string',
            'payment_method' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        $order->update($data);
        return redirect()->back()->with('success', 'Order updated');
    }

    public function destroy($id)
    {
        $this->ensureAdmin();
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect('/admin/orders')->with('success', 'Order deleted');
    }
}
