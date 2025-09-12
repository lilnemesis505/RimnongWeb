<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Protype;

class ProductType extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // สร้างอาร์เรย์ของประเภทสินค้าที่ต้องการ
        $productTypes = [
            ['type_name' => 'กาแฟ'],
            ['type_name' => 'ชา'],
            ['type_name' => 'น้ำหวาน'],
        ];

        // ใช้คำสั่ง insert เพื่อเพิ่มข้อมูลหลายรายการพร้อมกัน
        Protype::insert($productTypes);
    }
}