<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'description' => 'American multinational technology company',
            ],
            [
                'name' => 'Samsung',
                'description' => 'South Korean multinational conglomerate',
            ],
            [
                'name' => 'Nike',
                'description' => 'American multinational sportswear corporation',
            ],
            [
                'name' => 'Adidas',
                'description' => 'German multinational sportswear corporation',
            ],
            [
                'name' => 'Sony',
                'description' => 'Japanese multinational conglomerate corporation',
            ],
            [
                'name' => 'LG',
                'description' => 'South Korean multinational electronics company',
            ],
            [
                'name' => 'Dell',
                'description' => 'American multinational computer technology company',
            ],
            [
                'name' => 'HP',
                'description' => 'American multinational information technology company',
            ],
            [
                'name' => 'Zara',
                'description' => 'Spanish clothing and accessories retailer',
            ],
            [
                'name' => 'H&M',
                'description' => 'Swedish multinational clothing retail company',
            ],
        ];

        foreach ($brands as $brand) {
            $brand['slug'] = Str::slug($brand['name']);
            Brand::create($brand);
        }
    }
}
