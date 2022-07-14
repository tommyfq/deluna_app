<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $gender = $faker->randomElement(['male', 'female']);
    	foreach (range(1,200) as $index) {
            DB::table('ms_vendors')->insert([
                'name' => $faker->name($gender),
                'slug' => $faker->name($gender),
                'description' => $faker->text,
                'phone' => $faker->phoneNumber,
                'status' => true,
                'created_at' => $faker->date($format = 'Y-m-d', $max = 'now'),
                'created_by' => 'admin'
            ]);
        }
    }
}
