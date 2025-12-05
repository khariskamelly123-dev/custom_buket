<?php

namespace App\Http\Controllers;

use App\Models\Bouquet;
use App\Models\User;
use Illuminate\Http\Request;

class AdminBouquetController
{
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

    public function index()
    {
        $this->ensureAdmin();
        $bouquets = Bouquet::orderBy('created_at', 'desc')->get();
        return view('seller.bouquets.index', ['bouquets' => $bouquets]);
    }

    public function create()
    {
        $this->ensureAdmin();
        return view('seller.bouquets.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|string',
            'available' => 'nullable|boolean',
        ]);

        $data['available'] = isset($data['available']) ? (bool)$data['available'] : true;
        Bouquet::create($data);
        return redirect('/seller/manage/bouquets')->with('success', 'Bouquet created');
    }

    public function edit($id)
    {
        $this->ensureAdmin();
        $b = Bouquet::findOrFail($id);
        return view('seller.bouquets.edit', ['bouquet' => $b]);
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdmin();
        $b = Bouquet::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|string',
            'available' => 'nullable|boolean',
        ]);
        $data['available'] = isset($data['available']) ? (bool)$data['available'] : true;
        $b->update($data);
        return redirect('/seller/manage/bouquets')->with('success', 'Bouquet updated');
    }

    public function destroy(\Illuminate\Http\Request $request, $id)
    {
        $this->ensureAdmin();
        $b = Bouquet::findOrFail($id);
        $b->delete();

        $returnEdit = $request->input('return_edit');
        if ($returnEdit) {
            return redirect('/seller/catalog?edit=' . urlencode($returnEdit))->with('success', 'Bouquet deleted');
        }

        return redirect('/seller/catalog')->with('success', 'Bouquet deleted');
    }
}
