<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;

class ProductController extends Controller
{

    /* ===== 一覧 ===== */
    public function index(Request $request)
    {
        $keyword   = (string) $request->get('keyword', '');
        $companyId = (string) $request->get('company_id', '');

        $query = Product::with('company');

        if ($keyword !== '') {
            $query->where('product_name', 'like', "%{$keyword}%");
        }
        if ($companyId !== '') {
            $query->where('company_id', $companyId);
        }

        $products  = $query->orderBy('id')->paginate(10)->withQueryString();
        $companies = Company::orderBy('company_name')->get();

        return view('products.index', compact('products', 'companies', 'keyword', 'companyId'));
    }

    /* ===== 新規作成フォーム ===== */
    public function create()
    {
        $companies = Company::orderBy('company_name')->get();
        $product   = new Product(); // form の old() と共存のため
        return view('products.create', compact('companies', 'product'));
    }

    /* ===== 登録 ===== */
    public function store(ProductStoreRequest $request)
{
    $validated = $request->validated();

    DB::beginTransaction();
    try {
        $product = new Product($validated);

        if ($request->hasFile('img')) {
            $product->img_path = $request->file('img')->store('products', 'public');
        }

        $product->save();
        DB::commit();

        return redirect()->route('products.index')
            ->with('success', '商品を登録しました。');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => '商品登録に失敗しました。']);
    }
}

    /* ===== 詳細 ===== */
    public function show(Product $product)
    {
        $product->load('company');
        return view('products.show', compact('product'));
    }

    /* ===== 編集フォーム（Step8で使用予定） ===== */
    public function edit(Product $product)
    {
        $companies = Company::orderBy('company_name')->get();
        return view('products.edit', compact('product', 'companies'));
    }

    /* ===== 更新 ===== */
    public function update(ProductUpdateRequest $request, Product $product)
{
    $validated = $request->validated();

    DB::beginTransaction();
    try {
        // 画像差し替え
        if ($request->hasFile('img')) {
            if ($product->img_path) {
                Storage::disk('public')->delete($product->img_path);
            }
            $validated['img_path'] = $request->file('img')->store('products', 'public');
        }

        $product->update($validated);

        DB::commit();

        return redirect()
            ->route('products.show', $product)
            ->with('success', '商品を更新しました。');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => '商品更新に失敗しました。']);
    }
}
    /* ===== 削除 ===== */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            if ($product->img_path) {
                Storage::disk('public')->delete($product->img_path);
            }
            $product->delete();

            DB::commit();
            return redirect()->route('products.index')->with('success', '商品を削除しました。');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => '商品削除に失敗しました。']);
        }
    }
}