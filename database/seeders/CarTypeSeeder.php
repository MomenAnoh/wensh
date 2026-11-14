<?php

namespace Database\Seeders;

use App\Models\CarType;
use Illuminate\Database\Seeder;

class CarTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carTypes = [
            ['name_ar' => 'ملاكي', 'name_en' => 'Passenger Car'],
            ['name_ar' => 'جامبو', 'name_en' => 'Jumbo'],
            ['name_ar' => 'نص نقل', 'name_en' => 'Pickup Truck'],
            ['name_ar' => 'تروسيكل', 'name_en' => 'Tricycle'],
            ['name_ar' => 'توك توك', 'name_en' => 'Tuk-tuk'],
            ['name_ar' => 'ميكروباص', 'name_en' => 'Microbus'],
            ['name_ar' => 'مني باص', 'name_en' => 'Minibus'],
            ['name_ar' => 'باص', 'name_en' => 'Bus'],
            ['name_ar' => 'موتسيكل', 'name_en' => 'Motorcycle'],
        ];

        foreach ($carTypes as $carType) {
            CarType::firstOrCreate(['name_ar' => $carType['name_ar']], $carType);
        }
    }
}
