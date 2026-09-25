<?php

namespace Database\Seeders;

use App\Enums\ServiceUnit;
use App\Models\LaundryService;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Kiloan', 'description' => 'Layananuci berdasarkan berat.', 'sort_order' => 1],
            ['name' => 'Satuan', 'description' => 'Layanan per item.', 'sort_order' => 2],
            ['name' => 'Express', 'description' => 'Layanan prioritas dengan estimasi lebih cepat.', 'sort_order' => 3],
            ['name' => 'Setrika', 'description' => 'Layanan setrika satuan.', 'sort_order' => 4],
            ['name' => 'Bed Cover', 'description' => 'Layanan untuk ukuran besar seperti bed cover.', 'sort_order' => 5],
        ];

        foreach ($categories as $data) {
            ServiceCategory::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                $data + ['slug' => Str::slug($data['name']), 'is_active' => true],
            );
        }

        $services = [
            ['category' => 'Kiloan', 'name' => 'Cuci Regular', 'unit' => ServiceUnit::Kilogram, 'price' => 8000, 'duration_minutes' => 1440],
            ['category' => 'Kiloan', 'name' => 'Cuci+HTTPS', 'unit' => ServiceUnit::Kilogram, 'price' => 12000, 'duration_minutes' => 1440],
            ['category' => 'Satuan', 'name' => 'Kemeja', 'unit' => ServiceUnit::Piece, 'price' => 15000, 'duration_minutes' => 720],
            ['category' => 'Satuan', 'name' => 'Celana', 'unit' => ServiceUnit::Piece, 'price' => 15000, 'duration_minutes' => 720],
            ['category' => 'Express', 'name' => 'Express 6 Jam', 'unit' => ServiceUnit::Kilogram, 'price' => 18000, 'duration_minutes' => 360],
            ['category' => 'Setrika', 'name' => 'Setrika Steambot', 'unit' => ServiceUnit::Piece, 'price' => 12000, 'duration_minutes' => 360],
            ['category' => 'Bed Cover', 'name' => 'Cuci Bed Cover', 'unit' => ServiceUnit::Piece, 'price' => 35000, 'duration_minutes' => 2880],
        ];

        foreach ($services as $data) {
            $category = ServiceCategory::where('slug', Str::slug($data['category']))->firstOrFail();

            LaundryService::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'description' => null,
                    'unit' => $data['unit'],
                    'price' => $data['price'],
                    'duration_minutes' => $data['duration_minutes'],
                    'is_active' => true,
                ],
            );
        }
    }
}
