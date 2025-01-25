<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LayananCetakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $layanan = array("Fotokopi","Stiker","Buku","Spanduk","Pakaian","Tas Kertas","Aksesoris","Undangan","Kalender","Kartu Nama","Brosur","Amplop","Casing Ponsel");
        $satuan = array("pcs", "lot", "pcs", "pcs", "pcs", "pcs", "pcs", "lot", "pcs", "pcs", "pcs", "pcs", "pcs");
        $kesetaraan = array(1, 50, 1, 1, 1, 1, 1, 100, 1, 1, 500, 250, 1);

        foreach ($layanan as $key=>$value) {
            $imgnomor = $key +1;
            DB::table('layanan_cetaks')->insert([
                'nama' => $value,
                'satuan' => $satuan[$key],
                'kesetaraan_pcs'=>$kesetaraan[$key],
                'url_image' => "/imagelayanan/$imgnomor.jpg",
            ]);
        }
    }
}
