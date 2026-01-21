<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Company;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first(); // すでに作ったテストメーカー

        Product::insert([
            [
                'company_id' => $company->id,
                'product_name' => 'りんごジュース',
                'price' => 120,
                'stock' => 30,
            ],
            [
                'company_id' => $company->id,
                'product_name' => 'オレンジジュース',
                'price' => 150,
                'stock' => 10,
            ],
            [
                'company_id' => $company->id,
                'product_name' => 'コーラ',
                'price' => 180,
                'stock' => 50,
            ],
            [
                'company_id' => $company->id,
                'product_name' => '水',
                'price' => 80,
                'stock' => 100,
            ],
        ]);
    }
}
