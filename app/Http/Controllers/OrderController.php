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

    // Show order to buyer after creating it
    public function showOrder($id)
    {
        $order = Order::findOrFail($id);
        return view('buyer_order_show', ['order' => $order]);
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

    /**
     * Create a Midtrans Snap transaction for the given order.
     * Returns decoded response array or null on failure.
     */
    public function createMidtransTransaction($order, $itemsForMidtrans = [])
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $clientKey = env('MIDTRANS_CLIENT_KEY');
        if (! $serverKey || ! $clientKey) {
            return null;
        }

        $isProd = env('MIDTRANS_IS_PRODUCTION', false);
        $base = $isProd ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
        $url = $base . '/snap/v1/transactions';

        // Calculate gross_amount and item details
        $gross = 0;
        $itemDetails = [];
        if (is_array($itemsForMidtrans)) {
            foreach ($itemsForMidtrans as $it) {
                // Expect each item to have 'name' and 'price' and optional 'quantity'
                $name = $it['name'] ?? ($it['title'] ?? 'Item');
                $price = isset($it['price']) ? (int)$it['price'] : 0;
                $qty = isset($it['quantity']) ? (int)$it['quantity'] : 1;
                $gross += $price * $qty;
                $itemDetails[] = [
                    'id' => $it['id'] ?? uniqid(),
                    'price' => $price,
                    'quantity' => $qty,
                    'name' => $name,
                ];
            }
        }

        if ($gross <= 0) $gross = 1000; // minimal amount

        $payload = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $gross,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $order->buyer_name ?? 'Pembeli',
                'phone' => $order->buyer_phone ?? '',
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $auth = base64_encode($serverKey . ':');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . $auth,
        ]);

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($result === false || $err) {
            return null;
        }

        $decoded = json_decode($result, true);
        return $decoded ?: null;
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

            // Try create Midtrans transaction and redirect buyer to payment if configured
            $midresp = $this->createMidtransTransaction($order, is_array($items) ? ($items['bouquet'] ? [$items['bouquet']] : $items) : []);
            if ($midresp) {
                // persist transaction id and pending status when available
                if (isset($midresp['transaction_id'])) {
                    $order->payment_transaction_id = $midresp['transaction_id'];
                    $order->payment_status = $midresp['transaction_status'] ?? 'pending';
                    $order->save();
                }

                // Snap API may return redirect_url (for host redirect) or token
                if (isset($midresp['redirect_url'])) {
                    return redirect($midresp['redirect_url']);
                }
                if (isset($midresp['token'])) {
                    // show a view that loads snap.js and calls snap.pay(token)
                    return view('buyer_order_midtrans', ['order' => $order, 'snap_token' => $midresp['token']]);
                }
            }

            // fallback: show local confirmation
            return redirect('/orders/' . $order->id)->with('success', 'Pesanan berhasil dibuat.');
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
        // Try create Midtrans transaction for custom order and redirect to payment
        $itemsForMid = [];
        if (is_array($order->items) && isset($order->items['bouquet'])) {
            $itemsForMid[] = $order->items['bouquet'];
        } elseif (is_array($order->items)) {
            // create a generic item from items payload
            $itemsForMid[] = [
                'id' => $order->id,
                'name' => 'Custom Order',
                'price' => isset($order->items['price']) ? (int)$order->items['price'] : 10000,
                'quantity' => 1,
            ];
        } else {
            $itemsForMid[] = [
                'id' => $order->id,
                'name' => 'Custom Order',
                'price' => 10000,
                'quantity' => 1,
            ];
        }

        $midresp = $this->createMidtransTransaction($order, $itemsForMid);
        if ($midresp) {
            if (isset($midresp['transaction_id'])) {
                $order->payment_transaction_id = $midresp['transaction_id'];
                $order->payment_status = $midresp['transaction_status'] ?? 'pending';
                $order->save();
            }
            if (isset($midresp['redirect_url'])) {
                return redirect($midresp['redirect_url']);
            }
            if (isset($midresp['token'])) {
                return view('buyer_order_midtrans', ['order' => $order, 'snap_token' => $midresp['token']]);
            }
        }

        // fallback: show local confirmation
        return redirect('/orders/' . $order->id)->with('success', 'Pesanan berhasil dibuat. Silakan lanjutkan pembayaran.');
    }

    public function waLink($id)
    {
        // removed: WhatsApp sending disabled
        return redirect('/seller/orders/' . $id);
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

    // Create a Midtrans transaction on-demand for an existing order and show payment
    public function pay(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $itemsForMid = [];
        if (is_array($order->items) && isset($order->items['bouquet'])) {
            $itemsForMid[] = $order->items['bouquet'];
        } elseif (is_array($order->items)) {
            $itemsForMid[] = [
                'id' => $order->id,
                'name' => 'Custom Order',
                'price' => isset($order->items['price']) ? (int)$order->items['price'] : 10000,
                'quantity' => 1,
            ];
        } else {
            $itemsForMid[] = [
                'id' => $order->id,
                'name' => 'Custom Order',
                'price' => 10000,
                'quantity' => 1,
            ];
        }

        $midresp = $this->createMidtransTransaction($order, $itemsForMid);
        if ($midresp) {
            if (isset($midresp['transaction_id'])) {
                $order->payment_transaction_id = $midresp['transaction_id'];
                $order->payment_status = $midresp['transaction_status'] ?? 'pending';
                $order->save();
            }
            if (isset($midresp['redirect_url'])) {
                return redirect($midresp['redirect_url']);
            }
            if (isset($midresp['token'])) {
                return view('buyer_order_midtrans', ['order' => $order, 'snap_token' => $midresp['token']]);
            }
        }

        // If Midtrans did not return a response, show a clear error.
        return redirect('/orders/' . $order->id)->with('error', 'Pembayaran tidak dapat diproses saat ini. Pastikan MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY telah diset di file .env (sandbox keys jika testing).');
    }

    // Midtrans server-to-server notification handler
    public function midtransNotify(Request $request)
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            return response('Invalid payload', 400);
        }

        $serverKey = env('MIDTRANS_SERVER_KEY');
        $orderId = $payload['order_id'] ?? null;
        $statusCode = isset($payload['status_code']) ? (string)$payload['status_code'] : '';
        $gross = isset($payload['gross_amount']) ? (string)$payload['gross_amount'] : '';
        $signature = $payload['signature_key'] ?? '';

        // Validate signature if server key is available
        if ($serverKey && $orderId !== null) {
            $expected = hash('sha512', $orderId . $statusCode . $gross . $serverKey);
            if ($signature !== $expected) {
                return response('Invalid signature', 403);
            }
        }

        $order = Order::where('order_number', $orderId)->first();
        if (!$order) {
            return response('Order not found', 404);
        }

        // Map notification fields
        $transactionId = $payload['transaction_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? ($payload['status_message'] ?? null);

        if ($transactionId) $order->payment_transaction_id = $transactionId;
        if ($transactionStatus) $order->payment_status = $transactionStatus;

        // Map to order->status for internal use
        if (in_array($transactionStatus, ['settlement', 'capture', 'paid'])) {
            $order->status = 'paid';
        } elseif (in_array($transactionStatus, ['pending'])) {
            $order->status = 'pending_payment';
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expired', 'failure'])) {
            $order->status = 'payment_failed';
        }

        $order->save();

        return response('OK', 200);
    }
}
