<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class KontakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 11; $i++) {
            DB::table('kontaks')->insert([
                'vendors_id' => $faker->numberBetween(1, 10),
                'platform' => $faker->randomElement(['WhatsApp', 'Instagram', 'Facebook']),
                'link' => $faker->url,
                'username_kontak' => $faker->userName,
            ]);
        }
    }
}
