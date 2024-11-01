<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PengantarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $vendors = DB::table('vendors')->select()->get();
        $maxId = DB::table('penggunas')->max('id');
        $maxId += 1;
        foreach($vendors as $v){
            for ($i = 0; $i < 3; $i++) {
                DB::table('penggunas')->insert([
    
                    'email' => "email$maxId@email.com",
                    'password' => bcrypt('password'),
                    'nama' => $faker->name,
                    'role' => 'pengantar',
                    'saldo' => $faker->numberBetween(1000, 100000),
                    'nomor_telepon' => $faker->phoneNumber,
                    
                ]);
                $maxId += 1;
            }
        }

        
    }
}