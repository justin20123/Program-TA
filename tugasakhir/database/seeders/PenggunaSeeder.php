<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

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
                'password' => bcrypt('password'),
                'nama' => $faker->name,
                'role' => $role,
                'saldo' => $faker->numberBetween(1000, 100000),
                'nomor_telepon' => $faker->phoneNumber,
                
            ]);
            if($role == "manager"){
                DB::table('vendors_has_penggunas')->insert([
                    'vendors_id' => $faker->numberBetween(1, 10),
                    'penggunas_email' => "email$i@email.com",
                    'penggunas_id' => $i,
                    'role' => $faker->randomElement(['manajer', 'pegawai']),
                ]);
            }
        }
    }
}
