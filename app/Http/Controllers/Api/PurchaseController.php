<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $productId = (int)$validated['product_id'];
        $qty       = (int)$validated['quantity'];

        // 2) トランザクションで在庫チェック → 売上登録 → 在庫更新
        try {
            $result = DB::transaction(function () use ($productId, $qty) {

                // 対象商品を行ロックして取得
                $product = Product::where('id', $productId)->lockForUpdate()->first();

                // 念のため（existsで基本は来ないはず）
                if (!$product) {
                    return [
                        'ok' => false,
                        'status' => 404,
                        'body' => ['message' => 'Product not found.'],
                    ];
                }

                // 在庫不足チェック（在庫0もここで弾ける）
                if ($product->stock < $qty) {
                    return [
                        'ok' => false,
                        'status' => 422, // 422に寄せるのが分かりやすい
                        'body' => [
                            'message' => '在庫が不足しています。',
                            'errors' => [
                                'quantity' => ['在庫数を超える購入はできません。'],
                            ],
                        ],
                    ];
                }

                $price = (int)$product->price;
                $total = $price * $qty;

                // 売上登録（sales）
                $sale = Sale::create([
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'price'      => $price,
                    'total'      => $total,
                ]);

                // 在庫減算
                $product->decrement('stock', $qty);
                $product->refresh();

                return [
                    'ok' => true,
                    'status' => 200,
                    'body' => [
                        'message' => 'purchase success',
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'remaining_stock' => $product->stock - $qty, // decrement前のstockなので注意
                    ],
                ];
            });

            return response()->json($result['body'], $result['status']);
        } catch (\Throwable $e) {
            // 想定外エラー
            DB::rollBack();
            logger()->error($e);
            return response()->json([
                'message' => 'purchase failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function purchase(Request $request)
    {
        \Log::debug('purchase start', $request->all());
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        try {
            return DB::transaction(function () use ($productId, $quantity) {
                $product = Product::where('id', $productId)->lockForUpdate()->first();

                if (!$product) {
                    return response()->json(['message' => 'product not found'], 404);
                }
                if ($product->stock < $quantity) {
                    return response()->json(['message' => 'out of stock'], 400);
                }

                // sales作成（SalesモデルがあるならそれでOK。無ければDB::tableでもOK）
                DB::table('sales')->insert([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'price'      => $product->price,                 // 仕様に合わせて
                    'total'      => $product->price * $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $product->decrement('stock', $quantity);

                return response()->json([
                    'message' => 'purchase success',
                    'stock'   => $product->fresh()->stock,
                ], 200);
            });
        } catch (\Throwable $e) {
            // まずは原因見える化（後でログにする）
            return response()->json([
                'message' => 'purchase failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
