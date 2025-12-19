<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class CustomController extends Controller
{
    // Jenis buket default
    protected $bouquetTypes = [
        'buket_uang' => 'Buket Uang',
        'buket_bunga' => 'Buket Bunga',
        'buket_jajan' => 'Buket Jajan',
    ];

    // Load product options dari file jika ada
    protected function getOptions()
    {
        $path = storage_path('app/product_options.json');
        $opts = [];
        if (file_exists($path)) {
            $opts = json_decode(file_get_contents($path), true) ?: [];
        }

        // Default values
        $defaults = [
            'bouquetTypes' => [
                'buket_uang' => 'Buket Uang',
                'buket_bunga' => 'Buket Bunga',
                'buket_jajan' => 'Buket Jajan',
            ],
            'paper' => [
                'paperTypes' => [
                    ['key' => 'a', 'label' => 'Lis Cloud', 'image' => '/images/step2/a.liscloud.png'],
                    ['key' => 'b', 'label' => 'Polos', 'image' => '/images/step2/b.polos.png'],
                    ['key' => 'c', 'label' => 'Marbel', 'image' => '/images/step2/c.marbel.png'],
                    ['key' => 'd', 'label' => 'Lis Gold', 'image' => '/images/step2/d.lisgold.png'],
                    ['key' => 'e', 'label' => 'Lis Warna', 'image' => '/images/step2/e.liswarna.png'],
                ],
                'paperColors' => [
                    'Merah',
                    'Biru',
                    'Kuning',
                    'Hijau',
                    'Putih',
                    'Hitam',
                    'Pink',
                    'Ungu',
                    'Coklat',
                    'Abu-abu'
                ]
            ],
            'fillings' => [
                'uang' => [
                    'denominations' => [2000, 5000, 10000, 20000, 50000, 100000],
                    'counts' => [10, 20, 30, 40, 50]
                ],
                'bunga' => [],
                'jajan' => []
            ],
            'extras' => [
                ['key' => 'e1', 'label' => 'Aksesoris 1', 'image' => '/images/step4/acc_a_boneka.jpg'],
                ['key' => 'e2', 'label' => 'Aksesoris 2', 'image' => '/images/step4/acc_b_stick.jpg'],
                ['key' => 'e3', 'label' => 'Aksesoris 3', 'image' => '/images/step4/acc_c_topper.jpg'],
            ]
        ];

        return array_replace_recursive($defaults, $opts);
    }

    // 5 macam jenis kertas fallback
    protected function getPaperTypes()
    {
        return [
            ['key' => 'a', 'label' => 'Lis Cloud', 'image' => asset('images/step2/a.liscloud.png')],
            ['key' => 'b', 'label' => 'Polos', 'image' => asset('images/step2/b.polos.png')],
            ['key' => 'c', 'label' => 'Marbel', 'image' => asset('images/step2/c.marbel.png')],
            ['key' => 'd', 'label' => 'Lis Gold', 'image' => asset('images/step2/d.lisgold.png')],
            ['key' => 'e', 'label' => 'Lis Warna', 'image' => asset('images/step2/e.liswarna.png')],
        ];
    }

    // 10 warna kertas fallback
    protected $paperColors = [
        'Merah',
        'Biru',
        'Kuning',
        'Hijau',
        'Putih',
        'Hitam',
        'Pink',
        'Ungu',
        'Coklat',
        'Abu-abu'
    ];

    // ================================
    //               STEPS
    // ================================
    public function step(Request $request, $step)
    {
        $step = (int) $step;

        switch ($step) {
            case 1:
                $opts = $this->getOptions();
                return view('custom.step1', ['types' => $opts['bouquetTypes']]);

            case 2:
                $opts = $this->getOptions();
                return view('custom.step2', [
                    'paperTypes' => $opts['paper']['paperTypes'],
                    'paperColors' => $opts['paper']['paperColors']
                ]);

            case 3:
                $order = session('custom_order', []);
                $bt = $order['bouquet_type'] ?? null;

                if ($bt === 'buket_uang') {
                    $opts = $this->getOptions();
                    return view('custom.step3_uang', [
                        'denominations' => $opts['fillings']['uang']['denominations'],
                        'counts' => $opts['fillings']['uang']['counts']
                    ]);
                }

                if ($bt === 'buket_bunga') {
                    $files = $this->scanImages('images/step3_bunga');
                    return view('custom.step3_bunga', ['images' => $files]);
                }

                if ($bt === 'buket_jajan') {
                    $files = $this->scanImages('images/step3_jajan');
                    return view('custom.step3_jajan', ['images' => $files]);
                }

                $opts = $this->getOptions();
                return view('custom.step3', ['fillings' => $opts['fillings']]);

            case 4:
                $files = $this->scanImages('images/step4');
                return view('custom.step4', ['images' => $files]);

            case 5:
                return view('custom.step5', ['data' => session('custom_order', [])]);

            default:
                return redirect('/buyer');
        }
    }

    // Helper scan image folder
    private function scanImages($path)
    {
        $files = [];
        $dir = public_path($path);
        if (is_dir($dir)) {
            foreach (scandir($dir) as $f) {
                if ($f !== '.' && $f !== '..')
                    $files[] = $f;
            }
        }
        return $files;
    }

    // ================================
    //        POST HANDLER
    // ================================
    public function postStep(Request $request, $step)
    {
        $step = (int) $step;
        $data = session('custom_order', []);

        if ($step === 1) {
            $request->validate(['bouquet_type' => 'required|string']);
            $data['bouquet_type'] = $request->bouquet_type;
            session(['custom_order' => $data]);
            return redirect('/custom/step/2');
        }

        if ($step === 2) {
            $request->validate([
                'paper_type' => 'required|string',
                'paper_color' => 'required|string'
            ]);
            $data['paper'] = $request->only(['paper_type', 'paper_color']);
            session(['custom_order' => $data]);
            return redirect('/custom/step/3');
        }

        if ($step === 3) {
            $bt = $data['bouquet_type'] ?? null;

            if ($bt === 'buket_uang') {
                $request->validate([
                    'denomination' => 'required|integer',
                    'count' => 'required|integer'
                ]);
                $data['fillings'] = [
                    'type' => 'uang',
                    'denomination' => (int) $request->input('denomination'),
                    'count' => (int) $request->input('count')
                ];
            } elseif ($bt === 'buket_bunga' || $bt === 'buket_jajan') {
                $request->validate(['filling_choice' => 'required|string']);
                $data['fillings'] = [
                    'type' => ($bt === 'buket_bunga') ? 'bunga' : 'jajan',
                    'choice' => $request->input('filling_choice')
                ];
            } else {
                $request->validate(['fillings' => 'nullable|array']);
                $data['fillings'] = $request->fillings ?? [];
            }

            session(['custom_order' => $data]);
            return redirect('/custom/step/4');
        }

        if ($step === 4) {
            $request->validate(['extras' => 'nullable|array']);
            $data['extras'] = $request->extras ?? [];
            session(['custom_order' => $data]);
            return redirect('/custom/step/5');
        }

        if ($step === 5) {
            $request->validate([
                'buyer_name' => 'required|string',
                'buyer_phone' => 'required|string',
                'address' => 'nullable|string',
                'payment_method' => 'required|string',
            ]);

            $data['identity'] = $request->only(['buyer_name', 'buyer_phone', 'address', 'payment_method']);
            $order = Order::create([
                'buyer_name' => $data['identity']['buyer_name'],
                'buyer_phone' => $data['identity']['buyer_phone'],
                'order_number' => uniqid('ORD'),
                'items' => $data,
                'payment_method' => $data['identity']['payment_method'],
                'status' => 'new',
            ]);

            session()->forget('custom_order');

            // Prepare items for Midtrans (use bouquet entry if present)
            $itemsForMid = [];
            if (is_array($order->items) && isset($order->items['bouquet'])) {
                $itemsForMid[] = $order->items['bouquet'];
            } elseif (is_array($order->items)) {
                $itemsForMid[] = [
                    'id' => $order->id,
                    'name' => 'Custom Order',
                    'price' => isset($order->items['identity']['price']) ? (int)$order->items['identity']['price'] : 10000,
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

            // Create Midtrans transaction and redirect buyer to payment
            $oc = new \App\Http\Controllers\OrderController();
            $midresp = $oc->createMidtransTransaction($order, $itemsForMid);
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

            return redirect('/orders/' . $order->id)->with('success', 'Pesanan dibuat. Silakan lanjutkan pembayaran.');
        }

        return redirect('/buyer');
    }

    public function reset()
    {
        session()->forget('custom_order');
        return redirect('/buyer');
    }
}
