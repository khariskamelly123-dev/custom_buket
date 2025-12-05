<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Bouquet;

class OrderController
{
    // ----- Seller management (merged from AdminController) -----
    public function manageDashboard()
    {
        return view('seller.dashboard');
    }

    public function manageIndex(\Illuminate\Http\Request $request)
    {
        $q = $request->query('q');

        $query = Order::orderBy('created_at', 'desc');
        if ($q) {
            $query->where('order_number', 'like', "%{$q}%")
                  ->orWhere('buyer_name', 'like', "%{$q}%");
        }

        $orders = $query->paginate(15)->withQueryString();
        return view('seller_orders', ['orders' => $orders, 'q' => $q]);
    }

    public function manageShow($order_number)
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();
        return view('seller_order_show', ['order' => $order]);
    }

    public function manageUpdate(Request $request, $id)
    {
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

    public function manageDestroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect('/seller/manage/orders')->with('success', 'Order deleted');
    }

    // ----- end seller management -----
    public function index(\Illuminate\Http\Request $request)
    {
        $q = $request->query('q');

        $orders = Order::when($q, function ($query) use ($q) {
            return $query->where('order_number', 'like', '%' . $q . '%');
        })->orderBy('created_at', 'desc')->get();

        return view('seller_orders', ['orders' => $orders, 'q' => $q]);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return view('seller_order_show', ['order' => $order]);
    }

    // Seller catalog listing
    public function sellerCatalog()
    {
        $bouquets = Bouquet::orderBy('created_at', 'desc')->get();
        return view('seller_catalog', ['bouquets' => $bouquets]);
    }

    // Show edit form for a bouquet (seller)
    public function sellerEditBouquet($id)
    {
        $b = Bouquet::findOrFail($id);
        return view('seller_edit_bouquet', ['bouquet' => $b]);
    }

    // Show create form for seller to add bouquet
    public function sellerCreateBouquet()
    {
        return view('seller_create_bouquet');
    }

    // Store new bouquet from seller (with optional image upload)
    public function sellerStoreBouquet(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,svg',
            'available' => 'nullable',
        ]);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = uniqid('bouq_') . '.' . $file->getClientOriginalExtension();
            $dest = public_path('images/bouquets');
            if (!file_exists($dest)) mkdir($dest, 0755, true);
            $file->move($dest, $filename);
            $data['image'] = '/images/bouquets/' . $filename;
        }

        $data['available'] = isset($data['available']) ? (bool)$data['available'] : true;

        $b = Bouquet::create($data);
        return redirect('/seller/catalog')->with('success', 'Produk baru ditambahkan');
    }

    // Handle update from seller edit form
    public function sellerUpdateBouquet(Request $request, $id)
    {
        $b = Bouquet::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|string',
            'image_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,svg',
            'available' => 'nullable',
        ]);
        $data['available'] = isset($data['available']) ? (bool)$data['available'] : $b->available;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = uniqid('bouq_') . '.' . $file->getClientOriginalExtension();
            $dest = public_path('images/bouquets');
            if (!file_exists($dest)) mkdir($dest, 0755, true);
            $file->move($dest, $filename);
            $data['image'] = '/images/bouquets/' . $filename;
        }
        $b->update($data);
        return redirect('/seller/catalog')->with('success', 'Produk diperbarui');
    }

    // Render product editor for Steps 1-5
    public function productEditor()
    {
        // Load existing options from storage if present
        $path = storage_path('app/product_options.json');
        $options = [];
        if (file_exists($path)) {
            $options = json_decode(file_get_contents($path), true) ?: [];
        }

        // Also load existing bouquets so editor can show current products
        $bouquets = Bouquet::orderBy('created_at', 'desc')->get();

        return view('seller_product_editor', ['options' => $options, 'bouquets' => $bouquets]);
    }

    // Save product options posted from editor
    public function saveProductOptions(Request $request)
    {
        $step = $request->input('step');
        $payload = $request->input('payload');
        $payload_colors = $request->input('payload_colors');

        $path = storage_path('app/product_options.json');
        $options = [];
        if (file_exists($path)) {
            $options = json_decode(file_get_contents($path), true) ?: [];
        }
        // Accept either structured form submissions (human-friendly) or legacy JSON payloads
        if ($request->has('structured')) {
            // Handle structured saves per step
            if ($step === 'bouquetTypes') {
                $keys = $request->input('bouquet_keys', []);
                $labels = $request->input('bouquet_labels', []);
                $bt = [];
                for ($i = 0; $i < count($keys); $i++) {
                    $k = trim($keys[$i] ?? '');
                    $l = trim($labels[$i] ?? '');
                    if ($k !== '') $bt[$k] = $l;
                }
                $options['bouquetTypes'] = $bt;
            } elseif ($step === 'paper') {
                $keys = $request->input('paper_keys', []);
                $labels = $request->input('paper_labels', []);
                $images = $request->file('paper_images', []);
                $paperArr = [];
                $dest = public_path('images/step2'); if (!file_exists($dest)) mkdir($dest, 0755, true);
                for ($i = 0; $i < count($keys); $i++) {
                    $k = trim($keys[$i] ?? '');
                    $l = trim($labels[$i] ?? '');
                    $imgPath = null;
                    if (!empty($images) && isset($images[$i]) && $images[$i] != null) {
                        $f = $images[$i];
                        $fname = uniqid('p_') . '.' . $f->getClientOriginalExtension();
                        $f->move($dest, $fname);
                        $imgPath = '/images/step2/' . $fname;
                    }
                    $paperArr[] = ['key' => $k, 'label' => $l, 'image' => $imgPath];
                }
                $colorsRaw = $request->input('paper_colors', '');
                $colors = array_values(array_filter(array_map('trim', explode(',', $colorsRaw))));
                $options['paper'] = ['paperTypes' => $paperArr, 'paperColors' => $colors];
            } elseif ($step === 'fillings') {
                $uang_den = array_values(array_filter(array_map('trim', explode(',', $request->input('uang_denominations', '')))));
                $uang_counts = array_values(array_filter(array_map('trim', explode(',', $request->input('uang_counts', '')))));
                $fillings = ['uang' => ['denominations' => array_map('intval', $uang_den), 'counts' => array_map('intval', $uang_counts)]];

                // bunga files
                $bungaFiles = $request->file('fillings_bunga_files', []);
                $bungaArr = [];
                $destB = public_path('images/step3_bunga'); if (!file_exists($destB)) mkdir($destB, 0755, true);
                if (!empty($bungaFiles)) {
                    foreach ($bungaFiles as $f) {
                        if ($f == null) continue;
                        $fname = uniqid('jb_') . '.' . $f->getClientOriginalExtension();
                        $f->move($destB, $fname);
                        $bungaArr[] = '/images/step3_bunga/' . $fname;
                    }
                }
                $fillings['bunga'] = $bungaArr;

                // jajan files
                $jajanFiles = $request->file('fillings_jajan_files', []);
                $jajanArr = [];
                $destJ = public_path('images/step3_jajan'); if (!file_exists($destJ)) mkdir($destJ, 0755, true);
                if (!empty($jajanFiles)) {
                    foreach ($jajanFiles as $f) {
                        if ($f == null) continue;
                        $fname = uniqid('jj_') . '.' . $f->getClientOriginalExtension();
                        $f->move($destJ, $fname);
                        $jajanArr[] = '/images/step3_jajan/' . $fname;
                    }
                }
                $fillings['jajan'] = $jajanArr;

                $options['fillings'] = $fillings;
            } elseif ($step === 'extras') {
                $keys = $request->input('extras_keys', []);
                $labels = $request->input('extras_labels', []);
                $images = $request->file('extras_images', []);
                $extrasArr = [];
                $dest = public_path('images/step4'); if (!file_exists($dest)) mkdir($dest, 0755, true);
                for ($i = 0; $i < count($keys); $i++) {
                    $k = trim($keys[$i] ?? '');
                    $l = trim($labels[$i] ?? '');
                    $imgPath = null;
                    if (!empty($images) && isset($images[$i]) && $images[$i] != null) {
                        $f = $images[$i];
                        $fname = uniqid('ex_') . '.' . $f->getClientOriginalExtension();
                        $f->move($dest, $fname);
                        $imgPath = '/images/step4/' . $fname;
                    }
                    $extrasArr[] = ['key' => $k, 'label' => $l, 'image' => $imgPath];
                }
                $options['extras'] = $extrasArr;
            }
        } else {
            // Parse JSON payload from textarea (expect valid JSON)
            $data = null;
            try {
                $data = json_decode($payload, true);
            } catch (\Throwable $e) {
                $data = null;
            }

            if ($data === null) {
                return redirect('/seller/product-editor')->with('error', 'Payload JSON tidak valid untuk langkah ' . $step);
            }

            // Map step name into options key
            $key = $step;
            if ($key === 'paper') {
                // paper expects paperTypes + paperColors
                $colors = [];
                if ($payload_colors) {
                    $colors = json_decode($payload_colors, true) ?: [];
                }
                $options['paper'] = ['paperTypes' => $data, 'paperColors' => $colors];
            } else {
                $options[$key] = $data;
            }
        }

        file_put_contents($path, json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return redirect('/seller/product-editor')->with('success', 'Options untuk ' . $step . ' disimpan.');
    }

    // Update order status (seller action)
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // If explicit status provided, use it; otherwise toggle between 'new' and 'created'
        $status = $request->input('status');
        if (!$status) {
            $status = ($order->status === 'created') ? 'new' : 'created';
        }

        $order->status = $status;
        $order->save();

        return redirect('/seller/orders')->with('success', 'Status pesanan diperbarui: ' . $order->status);
    }

    public function store(Request $request)
    {
        // If bouquet_id provided, this is an order from catalog
        if ($request->has('bouquet_id')) {
            $data = $request->validate([
                'bouquet_id' => 'required|integer',
                'buyer_name' => 'required|string',
                'buyer_phone' => 'required|string',
                'address' => 'nullable|string',
                'payment_method' => 'required|string',
            ]);

            $bouquet = Bouquet::findOrFail($data['bouquet_id']);

            $items = [
                'bouquet' => $bouquet->only(['id','name','price']),
                'address' => $data['address'] ?? null,
            ];

            $order = Order::create([
                'buyer_name' => $data['buyer_name'],
                'buyer_phone' => $data['buyer_phone'],
                'order_number' => uniqid('ORD'),
                'items' => $items,
                'payment_method' => $data['payment_method'],
                'status' => 'new',
            ]);

            // Send WhatsApp message to configured recipient (seller/admin).
            $recipient = env('WA_RECIPIENT', '083104866204');

            // normalize recipient: remove non-digits, convert leading 0 to country code 62
            $r = preg_replace('/[^0-9+]/', '', $recipient);
            if (strpos($r, '+') === 0) {
                $r = ltrim($r, '+');
            }
            if (strpos($r, '0') === 0) {
                $r = '62' . substr($r, 1);
            }

            $message = "Nama: {$order->buyer_name}\nNomor Pesanan: {$order->order_number}\nMetode Pembayaran: {$order->payment_method}";
            return redirect('https://wa.me/'.$r.'?text='.rawurlencode($message));
        }

        $data = $request->validate([
            'buyer_name' => 'required|string',
            'buyer_phone' => 'required|string',
            'items' => 'nullable',
            'payment_method' => 'nullable|string',
        ]);

        $order = Order::create([
            'buyer_name' => $data['buyer_name'],
            'buyer_phone' => $data['buyer_phone'],
            'order_number' => uniqid('ORD'),
            'items' => $data['items'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'status' => 'new',
        ]);

        // After creating a custom order, redirect buyer to WhatsApp to notify seller
        $recipient = env('WA_RECIPIENT', '083104866204');
        $r = preg_replace('/[^0-9+]/', '', $recipient);
        if (strpos($r, '+') === 0) {
            $r = ltrim($r, '+');
        }
        if (strpos($r, '0') === 0) {
            $r = '62' . substr($r, 1);
        }

        $message = "Nama: {$order->buyer_name}\nNomor Pesanan: {$order->order_number}\n";
        if (!empty($order->payment_method)) {
            $message .= "Metode Pembayaran: {$order->payment_method}\n";
        }
        if (!empty($order->items)) {
            $itemsText = is_string($order->items) ? $order->items : json_encode($order->items, JSON_UNESCAPED_UNICODE);
            $message .= "Items: " . $itemsText;
        }

        return redirect('https://wa.me/'.$r.'?text='.rawurlencode($message));
    }

    public function waLink($id)
    {
        $order = Order::findOrFail($id);

        // Use configured recipient instead of buyer phone for automatic sending
        $recipient = env('WA_RECIPIENT', '083104866204');
        $r = preg_replace('/[^0-9+]/', '', $recipient);
        if (strpos($r, '+') === 0) {
            $r = ltrim($r, '+');
        }
        if (strpos($r, '0') === 0) {
            $r = '62' . substr($r, 1);
        }

        $message = "Nama: {$order->buyer_name}\nNomor Pesanan: {$order->order_number}\nMetode Pembayaran: " . ($order->payment_method ?? '-');
        $encoded = rawurlencode($message);

        $url = "https://wa.me/{$r}?text={$encoded}";

        return redirect($url);
    }

    public function buyer()
    {
        $bouquets = \App\Models\Bouquet::where('available', true)->get();
        return view('buyer_catalog', ['bouquets' => $bouquets]);
    }

    public function dashboard()
    {
        $bouquets = \App\Models\Bouquet::where('available', true)->get();
        return view('buyer_dashboard', ['bouquets' => $bouquets]);
    }

    public function createFromBouquet($id)
    {
        $bouquet = Bouquet::findOrFail($id);
        return view('order_from_bouquet', ['bouquet' => $bouquet]);
    }
}
