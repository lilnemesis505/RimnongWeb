<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\ProductType;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call(AdminSeeder::class);
        $this->call(ProductType::class);
    }
}
