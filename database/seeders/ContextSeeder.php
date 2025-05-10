<?php

namespace Database\Seeders;

use App\Models\Context;
use Illuminate\Database\Seeder;

class ContextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Context::insert([
            ['name' => 'mobile'],
            ['name' => 'web'],
            ['name' => 'desktop'],
        ]);

    }
}
