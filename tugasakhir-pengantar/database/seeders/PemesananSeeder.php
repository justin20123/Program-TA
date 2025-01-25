<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PemesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $idnota = 1;
        for ($i = 0; $i < 10; $i++) {
            $idpemesan = $faker->numberBetween(1, 10);
            $idvendor = $i+1;
            for($j = 0; $j<$faker->numberBetween(1, 3);$j++){
                for($k = 0; $k < $faker->numberBetween(1, 4); $k++){
                    $data = [];
    
                    $list_jenis_bahan = DB::table("vendors_has_jenis_bahan_cetaks")
                    ->where("vendors_id", "=" , $idvendor)
                    ->select('jenis_bahan_cetaks_id as id')
                    ->get();
                    
                    $idjenisbahancetak = $list_jenis_bahan[$faker->numberBetween(1, count($list_jenis_bahan) -1)]->id;

                    $list_harga_cetaks = DB::table("harga_cetaks")
                    ->where("id_bahan_cetaks", "=" , $idjenisbahancetak)
                    ->select('id')
                    ->get();

                    $idhargacetak = $list_harga_cetaks[$faker->numberBetween(1, count($list_harga_cetaks) -1)]->id;

                    $idpemesanan = DB::table('pemesanans')->insertGetId([
                        'penggunas_email' => "email$idpemesan@email.com",
                        'jumlah' => $faker->numberBetween(1, 100),
                        'url_file' => $faker->filePath(),
                        'harga_cetaks_id' => $idhargacetak,
                        'jenis_bahan_cetaks_id' => $idjenisbahancetak,
                        'vendors_id' =>  $idvendor,
                        'notas_id' =>  $idnota,
                        'perlu_verifikasi' => $faker->numberBetween(0, 1),
                    ]);
                    $numprogress = $faker->numberBetween(1,2);
                    $startDate = Carbon::createFromFormat('Y-m-d',$faker->date());
                    for($j=0; $j<(($numprogress*3) + 1); $j++){
                        $progress = "";
                        
                        if($j % 3 == 0 && $j != (($numprogress*3))){
                            $progress = "proses";
                        }
                        else if($j % 3 == 1 && $j != (($numprogress*3))){
                            $progress = "menunggu verifikasi";
                        }
                        else if($j % 3 == 2 && $j != (($numprogress*3))){
                            $progress = "memperbaiki";
                        }
                        else{
                            $progress = "terverifikasi";
                        }
                        $updatedDate =  $startDate->copy()->addDays($faker->numberBetween(0,1))->addMinutes(30);
                        $startDate = $updatedDate;
                        DB::table('notas_progress')->insert([
                            'pemesanans_id' => $idpemesanan,
                            'notas_id' => $idnota,
                            'urutan_progress' => $j,
                            'waktu_progress' => $updatedDate,
                            'progress' => $progress,
                            'url_ubah_file' => null,
                            'terverifikasi' => null,

                        ]); 
                    
                        
                    }
                }
                $idnota++; 
            }
            
            
        }
    }
}
