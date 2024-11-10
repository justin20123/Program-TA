<?php

namespace Database\Seeders;

use App\Models\Pemesanan;
use App\Models\Pengguna;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PemesananaHasOpsiDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $pemesananas = Pemesanan::all();
        foreach ($pemesananas as $p){
            $details = DB::table('detail_cetaks')
            ->join('jenis_bahan_cetaks', 'detail_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->where('jenis_bahan_cetaks.id', '=', $p->jenis_bahan_cetaks_id)
            ->select('detail_cetaks.id')
            ->get();

            foreach ($details as $d){
                $opsidetails = DB::table('opsi_details')
                ->where('detail_cetaks_id','=', $d->id)
                ->select('id')
                ->get();
                $idopsidetail = $faker->numberBetween(1, count($opsidetails));
                DB::table('pemesanans_has_opsi_details')->insert([
                    'pemesanans_id' => $p->id,
                    'opsi_details_id' => $idopsidetail,
                ]);
            }
            
        }
    }
}
