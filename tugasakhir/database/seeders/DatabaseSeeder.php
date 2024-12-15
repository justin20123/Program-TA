<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            LayananCetakSeeder::class,
            JenisBahanCetakSeeder::class,
            VendorSeeder::class,
            KontakSeeder::class,
            
            PenggunaSeeder::class,
            
            DetailCetakSeeder::class,
            OpsiDetailSeeder::class,
            GambarSeeder::class,
            HargaCetakSeeder::class,
            NotaSeeder::class,
            PemesananSeeder::class,
            ManajerSeeder::class,
            PengantarSeeder::class,
            PegawaiSeeder::class,

            PemesananaHasOpsiDetailSeeder::class,
            RatingSeeder::class,
            
            
        ]);
    }
}
