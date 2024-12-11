<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $layanan = array("Fotokopi","Stiker","Buku","Spanduk/Banner","Pakaian","Paper Bag","Aksesoris","Undangan","Kalender","Kartu Nama","Brosur","Amplop","Case HP");
        $satuan = array("pcs", "lot", "pcs", "pcs", "pcs", "pcs", "pcs", "lot", "pcs", "pcs", "pcs", "pcs", "pcs");
        $kesetaraan = array(1, 50, 1, 1, 1, 1, 1, 100, 1, 1, 500, 250, 1);
        foreach ($layanan as $key=>$value) {
            DB::table('layanan_cetaks')->insert([
                'nama' => $value,
                'satuan' => $satuan[$key],
                'kesetaraan_pcs'=>$kesetaraan[$key],
                'url_image' => "https://picsum.photos/id/". $faker->numberBetween(1, 300) . "/200/300",
            ]);
        }
        for ($i = 0; $i < 11; $i++) {
            $vendor = DB::table('vendors')->insertGetId([
                'nama' => $faker->company,
                'status' => $faker->randomElement(['active', 'inactive']),
                'foto_lokasi' => "https://picsum.photos/id/". $faker->numberBetween(1, 300) . "/200/300",
                'longitude' => $faker->longitude(112.7, 112.8),
                'latitude' => $faker->latitude(-7.3, -7.2),
            ]);
            foreach ($layanan as $key=>$value) {
                for ($j = 0; $j < 11; $j++) {
                    $deskripsi = $faker->paragraph(mt_rand(3, 25), true);
                    
                    $jenis_bahan = DB::table('jenis_bahan_cetaks')->insertGetId([
                        'nama' => $faker->word,
                        'gambar' => $faker->imageUrl(640, 480),
                        'deskripsi' => substr($deskripsi, 0, 250),
                    ]);

                    DB::table('vendors_has_jenis_bahan_cetaks')->insert([
                        'vendors_id' => $vendor,
                        'layanan_cetaks_id' => $key + 1,
                        'jenis_bahan_cetaks_id' => $jenis_bahan,
                        'url_image_replace' => null
                    ]);
                    for ($k = 0; $k < $faker->numberBetween(1, 3); $k++) {
                        $detail_cetak = DB::table('detail_cetaks')->insertGetId([
                            'value' => $faker->word,
                            'jenis_bahan_cetaks_id' => $jenis_bahan,
                        ]);
                        for ($l = 0; $l < $faker->numberBetween(1, 5); $l++) {
                            DB::table('opsi_details')->insert([
                                'opsi' => $faker->word,
                                'tipe' => $faker->randomElement(['satuan','tambahan','jumlah']),
                                'biaya_tambahan' => $faker->numberBetween(500, 5000),
                                'detail_cetaks_id' => $detail_cetak,
                            ]);
                        }
                    }
                }
                
                
            }
        }
    }
}
