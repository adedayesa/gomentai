<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // 1. ISI CATEGORIES
        $dimsum = DB::table('categories')->insertGetId(['name' => 'Dimsum Mentai', 'slug' => 'dimsum-mentai']);
        $spaghetti = DB::table('categories')->insertGetId(['name' => 'Spaghetti Mentai', 'slug' => 'spaghetti-mentai']);
        $kentang = DB::table('categories')->insertGetId(['name' => 'French Fries Mentai', 'slug' => 'frenchfries-mentai']);

        // 2. ISI OPTIONS (Jenis Kustomisasi)
        $pedas = DB::table('options')->insertGetId(['name' => 'Level Kepedasan', 'type' => 'radio']);
        $topping = DB::table('options')->insertGetId(['name' => 'Topping Tambahan', 'type' => 'radio']);

        // 3. ISI OPTION VALUES (Nilai Kustomisasi)
        DB::table('option_values')->insert([
            // Nilai untuk Level Kepedasan
            ['option_id' => $pedas, 'name' => 'Level 0 (Original)', 'price_modifier' => 0],
            ['option_id' => $pedas, 'name' => 'Level 1 (Mild)', 'price_modifier' => 1000],
            ['option_id' => $pedas, 'name' => 'Level 2 (Hot)', 'price_modifier' => 2000],

            // Nilai untuk Topping Tambahan
            ['option_id' => $topping, 'name' => 'Tanpa Topping', 'price_modifier' => 0],
            ['option_id' => $topping, 'name' => 'Extra Saus', 'price_modifier' => 2000],
            ['option_id' => $topping, 'name' => 'Keju Mozarella', 'price_modifier' => 4000],
        ]);
        
        // 4. ISI PRODUCTS dan HUBUNGKAN DENGAN OPTIONS
        $product_dimsum = DB::table('products')->insertGetId([
            'category_id' => $dimsum,
            'name' => 'Dimsum Mentai Original',
            'base_price' => 30000,
            'image' => 'images/dimsum_mentai.jpeg',
            'is_available' => true,
        ]);
        
        DB::table('product_option')->insert([
            ['product_id' => $product_dimsum, 'option_id' => $pedas],
            ['product_id' => $product_dimsum, 'option_id' => $topping],
        ]);

        $product_spaghetti = DB::table('products')->insertGetId([
            'category_id' => $spaghetti,
            'name' => 'Spaghetti Mentai Original',
            'base_price' => 35000,
            'image' => 'images/spaghetti_mentai.jpeg',
            'is_available' => true,
        ]);

        DB::table('product_option')->insert([
            ['product_id' => $product_spaghetti, 'option_id' => $pedas],
            ['product_id' => $product_spaghetti, 'option_id' => $topping],
        ]);

        $product_kentang = DB::table('products')->insertGetId([
            'category_id' => $kentang,
            'name' => 'French Fries Mentai Original',
            'base_price' => 25000,
            'image' => 'images/kentang_mentai.jpeg',
            'is_available' => true,
        ]);

        DB::table('product_option')->insert([
            ['product_id' => $product_kentang, 'option_id' => $pedas],
            ['product_id' => $product_kentang, 'option_id' => $topping],
        ]);
    }
}
