<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /* ===== 共通：バリデーション ===== */
    private function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255',
            'company_id'   => 'required|exists:companies,id',
            'price'        => 'required|integer|min:0',
            'stock'        => 'required|integer|min:0',
            'comment'      => 'nullable|string|max:10000',
            'img'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
        ];
    }

    private function messages(): array
    {
        return [
            'product_name.required' => '商品名は必須です。',
            'product_name.string'   => '商品名は文字列で入力してください。',
            'product_name.max'      => '商品名は255文字以内で入力してください。',
            'company_id.required'   => 'メーカーを選択してください。',
            'company_id.exists'     => '選択したメーカーは存在しません。',
            'price.required'        => '価格は必須です。',
            'price.integer'         => '価格は数値で入力してください。',
            'price.min'             => '価格は0円以上を入力してください。',
            'stock.required'        => '在庫数は必須です。',
            'stock.integer'         => '在庫数は数値で入力してください。',
            'stock.min'             => '在庫数は0以上を入力してください。',
            'comment.string'        => 'コメントは文字列で入力してください。',
            'comment.max'           => 'コメントは1万文字以内で入力してください。',
            'img.image'             => '画像ファイルを選択してください。',
            'img.mimes'             => '画像は jpg / jpeg / png / webp のいずれかでアップロードしてください。',
            'img.max'               => '画像サイズは5MB以下にしてください。',
        ];
    }

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

        return redirect()->route('products.index')->with('success', '商品を登録しました。');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->withErrors(['error' => '商品登録に失敗しました。'])->withInput();
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

    /* ===== 更新（Step8で使用予定） ===== */
    public function update(ProductUpdateRequest $request, Product $product)
{
    $validated = $request->validated();

    DB::beginTransaction();
    try {
        if ($request->hasFile('img')) {
            if ($product->img_path) {
                Storage::disk('public')->delete($product->img_path);
            }
            $product->img_path = $request->file('img')->store('products', 'public');
        }

        $product->fill([
            'product_name' => $validated['product_name'],
            'company_id'   => $validated['company_id'],
            'price'        => $validated['price'],
            'stock'        => $validated['stock'],
            'comment'      => $validated['comment'] ?? null,
        ])->save();

        DB::commit();
        return redirect()->route('products.show', $product)->with('success', '商品を更新しました。');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->withErrors(['error' => '商品更新に失敗しました。'])->withInput();
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