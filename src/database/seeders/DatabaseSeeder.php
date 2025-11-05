<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Models\Phone;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Activities tree
        $food = Activity::create(['name' => 'Еда', 'level' => 1]);
        $meat = Activity::create(['name' => 'Мясная продукция', 'parent_id' => $food->id, 'level' => 2]);
        $dairy = Activity::create(['name' => 'Молочная продукция', 'parent_id' => $food->id, 'level' => 2]);

        $cars = Activity::create(['name' => 'Автомобили', 'level' => 1]);
        $cargo = Activity::create(['name' => 'Грузовые', 'parent_id' => $cars->id, 'level' => 2]);
        $passenger = Activity::create(['name' => 'Легковые', 'parent_id' => $cars->id, 'level' => 2]);
        $parts = Activity::create(['name' => 'Запчасти', 'parent_id' => $passenger->id, 'level' => 3]);
        $acc = Activity::create(['name' => 'Аксессуары', 'parent_id' => $passenger->id, 'level' => 3]);

        // Buildings
        $b1 = Building::create(['address' => 'г. Москва, ул. Ленина 1, офис 3', 'latitude' => 55.755814, 'longitude' => 37.617635]);
        $b2 = Building::create(['address' => 'г. Москва, ул. Блюхера, 32/1', 'latitude' => 55.760000, 'longitude' => 37.610000]);

        // Organizations
        $o1 = Organization::create(['name' => 'ООО "Рога и Копыта"', 'building_id' => $b1->id]);
        Phone::create(['organization_id' => $o1->id, 'phone' => '2-222-222']);
        Phone::create(['organization_id' => $o1->id, 'phone' => '8-923-666-13-13']);
        $o1->activities()->attach([$meat->id, $dairy->id]);

        $o2 = Organization::create(['name' => 'МолокоПлюс', 'building_id' => $b2->id]);
        Phone::create(['organization_id' => $o2->id, 'phone' => '3-333-333']);
        $o2->activities()->attach([$dairy->id]);

        $o3 = Organization::create(['name' => 'АвтоМир', 'building_id' => $b2->id]);
        $o3->activities()->attach([$passenger->id, $parts->id]);
    }
}
