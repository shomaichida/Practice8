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
            'company_id.exists'     => '選択したメーカーが存在しません。',
            'price.required'        => '価格は必須です。',
            'price.integer'         => '価格は数値で入力してください。',
            'price.min'             => '価格は0以上で入力してください。',
            'stock.required'        => '在庫数は必須です。',
            'stock.integer'         => '在庫数は数値で入力してください。',
            'stock.min'             => '在庫数は0以上で入力してください。',
            'comment.max'           => 'コメントは10000文字以内で入力してください。',
            'img.image'             => '商品画像は画像ファイルを指定してください。',
            'img.mimes'             => '商品画像は jpg / jpeg / png / webp のみです。',
            'img.max'               => '商品画像は 5MB 以下で指定してください。',
        ];
    }
}