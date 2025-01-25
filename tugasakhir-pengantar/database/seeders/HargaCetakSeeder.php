<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
class HargaCetakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $jenis_bahan_cetaks = DB::table('jenis_bahan_cetaks')->get();

        foreach ($jenis_bahan_cetaks as $j) {
            $min = 1;
            $max = $faker->numberBetween(50, 100);
            for($i=0;$i<$faker->numberBetween(3, 6);$i++){
                
                DB::table('harga_cetaks')->insert([
                    'id_bahan_cetaks' => $j->id,
                    'harga_satuan' => $faker->numberBetween(1000, 10000),
                    'jumlah_cetak_maksimum' => $max,
                    'jumlah_cetak_minimum' => $min,

                ]);
                $min = $max + 1;
                $max = $min + $faker->numberBetween(50, 100);
            }
           
        }
    }
}
