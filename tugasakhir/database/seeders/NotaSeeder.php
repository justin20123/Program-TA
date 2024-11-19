<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class NotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 30; $i++) {
            DB::table('notas')->insert([
                'waktu_transaksi' => $faker->dateTimeThisYear,
                'status' => $faker->randomElement(["proses", "sedang diantar", "menunggu diambil", "selesai", "dibatalkan", "menunggu pembayaran"]),
                'opsi_pengambilan' => $faker->randomElement(["diambil", "diantar"]),
                'tanggal_selesai' => $faker->date(),
                'ulasan' => $faker->sentence,
            ]);
            
        }
    }
}
