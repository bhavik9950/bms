<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Garment;

class GarmentSeeder extends Seeder
{
    public function run()
    {
        $garments = [
            ['name' => 'T-Shirt', 'description' => 'Casual cotton t-shirt, available in various colors.'],
            ['name' => 'Jeans', 'description' => 'Denim jeans with slim fit design.'],
            ['name' => 'Jacket', 'description' => 'Lightweight bomber jacket, perfect for layering.'],
            ['name' => 'Dress', 'description' => 'Summer floral dress, knee length.'],
            ['name' => 'Sweater', 'description' => 'Wool blend sweater, warm and cozy.'],
            ['name' => 'Shorts', 'description' => 'Casual shorts with elastic waistband.'],
            ['name' => 'Shirt', 'description' => 'Formal cotton shirt with button-down collar.'],
            ['name' => 'Skirt', 'description' => 'A-line skirt, suitable for office wear.'],
            ['name' => 'Blazer', 'description' => 'Tailored blazer for formal occasions.'],
            ['name' => 'Hoodie', 'description' => 'Fleece hoodie with front pocket.'],
        ];

        foreach ($garments as $garment) {
            if (!Garment::where('name', $garment['name'])->exists()) {
                Garment::create($garment);
            }
        }
    }
}
