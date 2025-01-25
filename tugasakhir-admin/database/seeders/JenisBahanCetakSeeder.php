<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class JenisBahanCetakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // for ($i = 0; $i < 11; $i++) {
        //     $deskripsi = $faker->paragraph(mt_rand(3, 25), true);
            
        //     DB::table('jenis_bahan_cetaks')->insert([
        //         'nama' => $faker->word,
        //         'ukuran' => $faker->randomElement(['A4', 'A3', 'A2']),
        //         'gambar' => $faker->imageUrl(640, 480),
        //         'deskripsi' => substr($deskripsi, 0, 250),
        //     ]);
        // }
    }
}
