<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255',
            'company_id'   => 'required|exists:companies,id',
            'price'        => 'required|integer|min:0',
            'stock'        => 'required|integer|min:0',
            'comment'      => 'nullable|string|max:10000',
            'img'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }


    public function messages(): array
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
            'img.mimes'             => '画像は jpg / jpeg / png / webp を指定してください。',
            'img.max'               => '画像サイズは5MB以下にしてください。',
        ];
    }


    public function attributes(): array
    {
        return [
            'product_name' => '商品名',
            'company_id'   => 'メーカー',
            'price'        => '価格',
            'stock'        => '在庫数',
            'comment'      => 'コメント',
            'img'          => '商品画像',
        ];
    }
}
