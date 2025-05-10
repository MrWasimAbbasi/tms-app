<?php

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Locale::insert([
            ['name' => 'en', 'description' => 'English'],
            ['name' => 'fr', 'description' => 'French'],
            ['name' => 'ar', 'description' => 'Arabic'],
        ]);
    }
}
