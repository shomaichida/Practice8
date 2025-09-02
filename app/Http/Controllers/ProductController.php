<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $keyword   = $request->input('keyword');      // 商品名の部分一致
        $companyId = $request->input('company_id');   // メーカーID

        $query = \App\Models\Product::with('company');

        if ($keyword) {
            $query->where('product_name', 'like', "%{$keyword}%");
        }
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        // 検索条件を保ったままページング
        $products  = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $companies = \App\Models\Company::orderBy('company_name')->get();

        return view('products.index', compact('products', 'companies', 'keyword', 'companyId'));
    }

    public function create()
    {
        $companies = Company::orderBy('company_name')->get();
        return view('products.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'company_id'   => ['required', \Illuminate\Validation\Rule::exists('companies', 'id')],
            'price'        => ['required', 'integer', 'min:0', 'max:1000000'],
            'stock'        => ['required', 'integer', 'min:0', 'max:1000000'],
            'comment'      => ['nullable', 'string', 'max:10000'],
            'img'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB
        ]);

        $imgPath = null;
        if ($request->hasFile('img')) {
            // storage/app/public/products/xxx.jpg に保存
            $imgPath = $request->file('img')->store('products', 'public');
        }

        $product = \App\Models\Product::create([
            'product_name' => $validated['product_name'],
            'company_id'   => $validated['company_id'],
            'price'        => $validated['price'],
            'stock'        => $validated['stock'],
            'comment'      => $validated['comment'] ?? null,
            'img_path'     => $imgPath,
        ]);

        return redirect()->route('products.show', $product)->with('success', '商品を登録しました。');
    }

    public function show(Product $product)
    {
        $product->load('company');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $companies = Company::orderBy('company_name')->get();
        return view('products.edit', compact('product', 'companies'));
    }

    public function update(Request $request, \App\Models\Product $product)
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'company_id'   => ['required', \Illuminate\Validation\Rule::exists('companies', 'id')],
            'price'        => ['required', 'integer', 'min:0', 'max:1000000'],
            'stock'        => ['required', 'integer', 'min:0', 'max:1000000'],
            'comment'      => ['nullable', 'string', 'max:10000'],
            'img'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('img')) {
            if ($product->img_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($product->img_path);
            $product->img_path = $request->file('img')->store('products', 'public');
        }

        $product->fill([
            'product_name' => $validated['product_name'],
            'company_id'   => $validated['company_id'],
            'price'        => $validated['price'],
            'stock'        => $validated['stock'],
            'comment'      => $validated['comment'] ?? null,
        ])->save();

        return redirect()->route('products.show', $product)->with('success', '商品を更新しました。');
    }

    public function destroy(Product $product)
    {
        if ($product->img_path) Storage::disk('public')->delete($product->img_path);
        $product->delete();
        return redirect()->route('products.index')->with('success', '商品を削除しました。');
    }
}
