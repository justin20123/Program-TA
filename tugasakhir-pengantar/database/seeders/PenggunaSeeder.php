<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
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
            $role = $faker->randomElement(['admin', 'pemesan']);

            DB::table('penggunas')->insert([

                'email' => "email$i@email.com",
                'password' => Hash::make("user$i"),
                'nama' => $faker->name,
                'role' => $role,
                'saldo' => $faker->numberBetween(1000, 100000),
                'nomor_telepon' => $faker->phoneNumber,
                'vendors_id' => null,
                
            ]);
        }
    }
}
