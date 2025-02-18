<?php

namespace App\Http\Controllers;

use App\Models\JenisBahanCetak;
use App\Models\Layanan;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LayananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRating($vendor_id, $layanan_id)
    {
        $ratings = DB::table('ratings')
            ->join('notas', 'ratings.notas_id', '=', 'notas.id')
            ->join('pemesanans', 'notas.id', '=', 'pemesanans.notas_id')
            ->join('jenis_bahan_cetaks', 'pemesanans.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->where('pemesanans.vendors_id', '=', $vendor_id)
            ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $layanan_id)
            ->whereNotNull('ratings.nilai')
            ->avg('ratings.nilai');
        return $ratings;
    }

    public function getTotalNota($vendor_id, $layanan_id)
    {
        $total_nota = DB::table('notas')
            ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
            ->join('jenis_bahan_cetaks', 'pemesanans.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->where('pemesanans.vendors_id', '=', $vendor_id)
            ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $layanan_id)
            ->count();
        return $total_nota;
    }

    public function getTotalNotaRating($vendor_id, $layanan_id)
    {
        $notas = DB::table('ratings')
        ->join('notas', 'ratings.notas_id', '=', 'notas.id')
        ->join('pemesanans', 'notas.id', '=', 'pemesanans.notas_id')
        ->join('jenis_bahan_cetaks', 'pemesanans.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->where('pemesanans.vendors_id', '=', $vendor_id)
        ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $layanan_id)
        ->whereNotNull('ratings.nilai')
        ->select('notas.id')
        ->distinct()
        ->get();
        $total = 0;
        foreach($notas as $n){
            $total++;
        }
        return $total;
    }

    public function index()
    {
        $layanans = DB::table('layanan_cetaks')
            ->join('vendors_has_jenis_bahan_cetaks', 'layanan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id')
            ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', Auth::user()->vendors_id)

            ->select('layanan_cetaks.*')->distinct()
            ->get();
        if(count($layanans) == 0){
            return redirect()->route('opensetup', ['vendorid' => Auth::user()->vendors_id]); 
        }
        foreach ($layanans as $l) {
            $ratings = $this->getRating(Auth::user()->vendors_id, $l->id);
            $l->layanan_rating = $ratings;
            $l->total_nota =  $this->getTotalNotaRating(Auth::user()->vendors_id, $l->id);
        }
        $vendor = DB::table('vendors')
            ->where('vendors.id', '=', Auth::user()->vendors_id)
            ->first();

        return view('layanan.layanancetak', compact('layanans', 'vendor'));
    }

    public function detail_layanan($vendor_id, $id_layanan)
    {         
        $jenis_bahan = DB::table('jenis_bahan_cetaks')
        ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
        ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $id_layanan)
        ->where('jenis_bahan_cetaks.deleted_at', '=', null)
        ->select('jenis_bahan_cetaks.id as id_jenis_bahan', 'jenis_bahan_cetaks.nama as nama_jenis_bahan', 'jenis_bahan_cetaks.deskripsi as deskripsi', 'vendors_has_jenis_bahan_cetaks.vendors_id as id_vendor', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id as id_layanan')
        ->get();

        $detail_cetaks = DB::table('detail_cetaks')
        ->join('jenis_bahan_cetaks', 'detail_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->leftJoin('opsi_details', 'detail_cetaks.id', '=', 'opsi_details.detail_cetaks_id') 
        ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
        ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $id_layanan)
        ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $jenis_bahan[0]->id_jenis_bahan)
        ->where('jenis_bahan_cetaks.deleted_at', '=', null)
        ->select('detail_cetaks.*', 'opsi_details.id as id_opsi', 'opsi_details.opsi as opsi', 'opsi_details.biaya_tambahan as biaya_tambahan')
        ->get();

        $opsi_detail = [];
        foreach ($detail_cetaks as $detail) {
            if (!isset($opsi_detail[$detail->id])) {
                $opsi_detail[$detail->id] = [
                    'detail' => $detail,
                    'opsi' => [],
                ];
            }
            if ($detail->id_opsi) { 
                $opsi_detail[$detail->id]['opsi'][] = [
                    'id' => $detail->id_opsi,
                    'opsi' => $detail->opsi, 
                    'biaya_tambahan' => $detail->biaya_tambahan, 
                ];
            }
        } 

        $opsi_detail = array_values($opsi_detail);

        $nama_layanan = DB::table('layanan_cetaks')
        ->where('id', '=', $id_layanan)
        ->select('nama')
        ->first();
        if ($nama_layanan) {
            $layanan = [
                'nama_layanan' => $nama_layanan->nama, 
                'rating' => $this->getRating($vendor_id, $id_layanan),
                'total_nota' => $this->getTotalNotaRating($vendor_id, $id_layanan)
            ];
        } else {
            $layanan = [
                'nama_layanan' => '', 
                'rating' => 0,
                'total_nota' => 0 
            ];
        }
        
        return view('layanan.detail', compact('opsi_detail', 'layanan', 'jenis_bahan'));
    }

    public function detail_layanan_load($vendor_id, $id_layanan, $id_jenis_bahan){
        $detail_cetaks = DB::table('detail_cetaks')
        ->join('jenis_bahan_cetaks', 'detail_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->leftJoin('opsi_details', 'detail_cetaks.id', '=', 'opsi_details.detail_cetaks_id') 
        ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
        ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $id_layanan)
        ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $id_jenis_bahan)
        ->where('jenis_bahan_cetaks.deleted_at', '=', null)
        ->select('detail_cetaks.*', 'opsi_details.id as id_opsi', 'opsi_details.opsi as opsi', 'opsi_details.biaya_tambahan as biaya_tambahan')
        ->get();

        $opsi_detail = [];
        $jenis_bahan = DB::table('jenis_bahan_cetaks')
        ->where('id', '=', $id_jenis_bahan)
        ->first();
        foreach ($detail_cetaks as $detail) {
            
            if (!isset($opsi_detail[$detail->id])) {
                $opsi_detail[$detail->id] = [
                    'detail' => $detail,
                    'opsi' => [],
                ];
            }
            if ($detail->id_opsi) { 
                $opsi_detail[$detail->id]['opsi'][] = [
                    'id' => $detail->id_opsi,
                    'opsi' => $detail->opsi, 
                    'biaya_tambahan' => $detail->biaya_tambahan, 
                ];
            }
        } 

        $opsi_detail = array_values($opsi_detail);

        return json_encode(['result' => 'success', 'data' => $opsi_detail, 'jenis_bahan'=>$jenis_bahan]);
    }

    public function edit_opsi($vendor_id, $id_layanan, $id_jenis_bahan, $id_detail, $id_opsi){
        $detail_cetaks = DB::table('detail_cetaks')
        ->join('jenis_bahan_cetaks', 'detail_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->leftJoin('opsi_details', 'detail_cetaks.id', '=', 'opsi_details.detail_cetaks_id') 
        ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
        ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $id_layanan)
        ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $id_jenis_bahan)
        ->where('vendors_has_jenis_bahan_cetaks.deleted_at', '=', null)
        ->where('detail_cetaks.id', '=', $id_detail)
        ->where('opsi_details.id', '=', $id_opsi)
        ->select('detail_cetaks.*', 'opsi_details.id as id_opsi', 'opsi_details.opsi as opsi', 'opsi_details.biaya_tambahan as biaya_tambahan', 'detail_cetaks.value as nama_detail')
        ->first();

        $layanan = [
            "id_vendor" => $vendor_id,
            "id_layanan" => $id_layanan,
            "id_jenis_bahan" => $id_jenis_bahan,
            "id_detail" => $id_detail,
        ];

        return view('layanan.editoption', compact('detail_cetaks', 'layanan'));
    }

    public function addhargasetup($id_jenis_bahan, $min, $max = null, $harga) {
        DB::table('harga_cetaks')->insert([
            "id_bahan_cetaks" => $id_jenis_bahan,
            "jumlah_cetak_minimum" => $min,
            "jumlah_cetak_maksimum" => $max,
            "harga_satuan" => $harga,
        ]);
        $jenis_bahan = JenisBahanCetak::findOrFail($id_jenis_bahan);
        $jenis_bahan->updated_at = Carbon::now('Asia/Jakarta');
        $jenis_bahan->save();  
    }

    public function addjenisbahansetup($id_layanan, $id_vendor, $array) {
        $array_id_jenis_bahan = [];
        foreach($array as $a) {
            $id_jenis_bahan = DB::table('jenis_bahan_cetaks')->insertGetId([
                'nama' => $a['nama'],
                'gambar'=> "",
                'deskripsi' => "",
            ]);
            array_push($array_id_jenis_bahan, $id_jenis_bahan);
            foreach($a['arrminimum'] as $key => $m) {
                $harga_satuan = $a['arrharga'][$key];
                if ($key === count($a['arrminimum']) - 1) {
                    $max = null; 
                } else {
                    $max = $a['arrminimum'][$key + 1] - 1;
                }
                $this->addhargasetup($id_jenis_bahan, $m, $max, $harga_satuan);
            }
            DB::table('vendors_has_jenis_bahan_cetaks')->insert([
                'layanan_cetaks_id' => $id_layanan,
                'vendors_id' => $id_vendor,
                'jenis_bahan_cetaks_id' => $id_jenis_bahan,
            ]);

            $jenis_bahan = JenisBahanCetak::findOrFail($id_jenis_bahan);
            $jenis_bahan->updated_at = Carbon::now('Asia/Jakarta');
            $jenis_bahan->save();
        }
        
        return $array_id_jenis_bahan;
    }

    public function setupfotokopi($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'HVS A4 70 gsm',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [200, 180, 160],
            ],
            [
                'nama' => 'HVS A4 80 gsm',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [250, 230, 200],
            ],
            [
                'nama' => 'HVS F4 70 gsm',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [300, 280, 250],
            ],
            [
                'nama' => 'HVS F4 80 gsm',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [350, 330, 300],
            ],
            [
                'nama' => 'Kertas Art Paper 120 gsm',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [500, 450, 400],
            ],
            [
                'nama' => 'Kertas Art Paper 150 gsm',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [600, 550, 500],
            ],
            [
                'nama' => 'Kertas Art Carton 190 gsm',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [800, 750, 700],
            ],
            [
                'nama' => 'Kertas Art Carton 230 gsm',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [1000, 950, 900],
            ],
            [
                'nama' => 'Kertas NCR putih',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [700, 650, 600],
            ],
            [
                'nama' => 'Kertas NCR merah',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [700, 650, 600],
            ],
            [
                'nama' => 'Kertas NCR kuning',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [700, 650, 600],
            ],
            [
                'nama' => 'Kertas NCR hijau',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [700, 650, 600],
            ],
            [
                'nama' => 'Kertas Glossy 120 gsm',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [600, 550, 500],
            ],
            [
                'nama' => 'Kertas Dupleks 250 gsm',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [900, 850, 800],
            ],
            [
                'nama' => 'Kertas Transparan (Tracing Paper)',
                'arrminimum' => [1, 51, 1001],
                'arrharga' => [1500, 1400, 1300],
            ],
        ];
        $this->addjenisbahansetup(1, $idvendor, $arrdefaultjenisbahan);
    }
    public function setupstiker($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'Stiker Kertas',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [100000, 90000, 85000, 80000, 75000],
            ],
            [
                'nama' => 'Stiker Vinyl Glossy',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [200000, 190000, 180000, 170000, 160000],
            ],
            [
                'nama' => 'Stiker Vinyl Matte',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [200000, 190000, 180000, 170000, 160000],
            ],
            [
                'nama' => 'Stiker Transparan',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [250000, 240000, 230000, 220000, 210000],
            ],
            [
                'nama' => 'Stiker Chromo',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [120000, 115000, 110000, 105000, 100000],
            ],
            [
                'nama' => 'Stiker Hologram',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [300000, 290000, 280000, 270000, 260000],
            ],
            [
                'nama' => 'Stiker Foil Emas',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [350000, 340000, 330000, 320000, 310000],
            ],
            [
                'nama' => 'Stiker Foil Perak',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [350000, 340000, 330000, 320000, 310000],
            ],
        ];
        $this->addjenisbahansetup(2, $idvendor, $arrdefaultjenisbahan);
    }
    public function setupbuku($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'Cetak Buku A3 Art Paper 120 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000], // Jumlah lembar
                'arrharga' => [1000, 950, 900, 850, 800], // Harga per lembar
            ],
            [
                'nama' => 'Cetak Buku A3 Art Paper 150 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [1200, 1150, 1100, 1050, 1000],
            ],
            [
                'nama' => 'Cetak Buku A3 HVS 100 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [900, 850, 800, 750, 700],
            ],
            [
                'nama' => 'Cetak Buku A3 HVS 80 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [800, 770, 740, 710, 680],
            ],
            [
                'nama' => 'Cetak Buku A3 Art Carton 210 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [1300, 1250, 1200, 1150, 1100],
            ],
            [
                'nama' => 'Cetak Buku A3 Art Carton 260 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [1500, 1450, 1400, 1350, 1300],
            ],
            [
                'nama' => 'Cetak Buku A4 Art Paper 120 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [700, 680, 660, 640, 620],
            ],
            [
                'nama' => 'Cetak Buku A4 Art Paper 150 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [800, 780, 760, 740, 720],
            ],
            [
                'nama' => 'Cetak Buku A4 HVS 100 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [600, 580, 560, 540, 520],
            ],
            [
                'nama' => 'Cetak Buku A4 HVS 80 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [500, 480, 460, 440, 420],
            ],
            [
                'nama' => 'Cetak Buku A4 Art Carton 210 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [1100, 1050, 1000, 950, 900],
            ],
            [
                'nama' => 'Cetak Buku A4 Art Carton 260 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [1300, 1250, 1200, 1150, 1100],
            ],
            [
                'nama' => 'Cetak Buku A5 Art Paper 120 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [600, 580, 560, 540, 520],
            ],
            [
                'nama' => 'Cetak Buku A5 Art Paper 150 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [700, 680, 660, 640, 620],
            ],
            [
                'nama' => 'Cetak Buku A5 HVS 100 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [500, 480, 460, 440, 420],
            ],
            [
                'nama' => 'Cetak Buku A5 HVS 80 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [400, 380, 360, 340, 320],
            ],
            [
                'nama' => 'Cetak Buku A5 Art Carton 210 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [900, 870, 840, 810, 780],
            ],
            [
                'nama' => 'Cetak Buku A5 Art Carton 260 Gsm',
                'arrminimum' => [1, 50, 100, 500, 1000],
                'arrharga' => [1100, 1050, 1000, 950, 900],
            ],
        ];
        $this->addjenisbahansetup(3, $idvendor, $arrdefaultjenisbahan);
    }
    public function setupbanner($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'Spanduk Flexi China 280 Gsm',
                'arrminimum' => [1, 5, 10, 50, 100], // Jumlah meter
                'arrharga' => [30000, 29000, 28000, 27000, 26000], // Harga per meter
            ],
            [
                'nama' => 'Spanduk Flexi Korea 440 Gsm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [35000, 34000, 33000, 32000, 31000],
            ],
            [
                'nama' => 'Spanduk Flexi Jerman 510 Gsm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [38000, 37000, 36000, 35000, 34000],
            ],
            [
                'nama' => 'X Banner Mini',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [45000, 44000, 43000, 42000, 41000],
            ],
            [
                'nama' => 'Banner Foamboard Impraboard A3',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [120000, 115000, 110000, 105000, 100000],
            ],
            [
                'nama' => 'Banner Foamboard Impraboard A2',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [140000, 135000, 130000, 125000, 120000],
            ],
            [
                'nama' => 'Banner Foamboard Impraboard A1',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [160000, 155000, 150000, 145000, 140000],
            ],
            [
                'nama' => 'Custom Foamboard Impraboard',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [180000, 175000, 170000, 165000, 160000],
            ],
            [
                'nama' => 'Tripod Banner A2',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [85000, 82000, 80000, 78000, 76000],
            ],
            [
                'nama' => 'Tripod Banner A1',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [100000, 98000, 96000, 94000, 92000],
            ],
            [
                'nama' => 'Tripod Banner 60x120 cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [75000, 73000, 71000, 69000, 67000],
            ],
            [
                'nama' => 'X Banner Flexi China 280Gsm 60x160cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [50000, 49000, 48000, 47000, 46000],
            ],
            [
                'nama' => 'X Banner Flexi Korea 440Gsm 60x160cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [55000, 54000, 53000, 52000, 51000],
            ],
            [
                'nama' => 'X Banner Albatros 60x160cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [45000, 44000, 43000, 42000, 41000],
            ],
            [
                'nama' => 'X Banner Flexi China 280Gsm 80x180cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [60000, 59000, 58000, 57000, 56000],
            ],
            [
                'nama' => 'X Banner Flexi Jerman 510Gsm 60x160cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [65000, 64000, 63000, 62000, 61000],
            ],
            [
                'nama' => 'X Banner Luster 60x160cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [70000, 69000, 68000, 67000, 66000],
            ],
            [
                'nama' => 'X Banner Photopaper 60x160cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [75000, 74000, 73000, 72000, 71000],
            ],
            [
                'nama' => 'X Banner Flexi Korea 440Gsm 80x180cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [80000, 79000, 78000, 77000, 76000],
            ],
            [
                'nama' => 'X Banner Albatros 80x180cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [85000, 84000, 83000, 82000, 81000],
            ],
            [
                'nama' => 'X Banner Flexi Jerman 510Gsm 80x180cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [90000, 89000, 88000, 87000, 86000],
            ],
            [
                'nama' => 'X Banner Luster 80x180cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [95000, 94000, 93000, 92000, 91000],
            ],
            [
                'nama' => 'X Banner Photopaper 80x180cm',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [100000, 99000, 98000, 97000, 96000],
            ],
            [
                'nama' => 'Roll Banner 60x160cm Flexi 280gsm China',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [120000, 115000, 110000, 105000, 100000],
            ],
            [
                'nama' => 'Roll Banner 60x160cm Flexi 440gsm Korea',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [130000, 125000, 120000, 115000, 110000],
            ],
            [
                'nama' => 'Roll Banner 60x160cm Albatros',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [140000, 135000, 130000, 125000, 120000],
            ],
            [
                'nama' => 'Roll Banner 60x160cm Photopaper',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [150000, 145000, 140000, 135000, 130000],
            ],
            [
                'nama' => 'Roll Banner 60x160cm Flexi 510gsm Jerman',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [160000, 155000, 150000, 145000, 140000],
            ],
            [
                'nama' => 'Roll Banner 60x160cm Luster',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [170000, 165000, 160000, 155000, 150000],
            ],
            [
                'nama' => 'Roll Banner 85x200cm Flexi 280gsm China',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [180000, 175000, 170000, 165000, 160000],
            ],
            [
                'nama' => 'Roll Banner 85x200cm Flexi 440gsm Korea',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [190000, 185000, 180000, 175000, 170000],
            ],
            [
                'nama' => 'Roll Banner 85x200cm Albatros',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [200000, 195000, 190000, 185000, 180000],
            ],
            [
                'nama' => 'Roll Banner 85x200cm Photopaper',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [210000, 205000, 200000, 195000, 190000],
            ],
            [
                'nama' => 'Roll Banner 85x200cm Flexi 510gsm Jerman',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [220000, 215000, 210000, 205000, 200000],
            ],
            [
                'nama' => 'Roll Banner 85x200cm Luster',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [230000, 225000, 220000, 215000, 210000],
            ],
            [
                'nama' => 'Rotary Light Box',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [250000, 240000, 230000, 220000, 210000],
            ],
        ];
        $this->addjenisbahansetup(4, $idvendor, $arrdefaultjenisbahan);
    }
    public function setuppakaian($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'Kaos Pria Sablon (Basic)',
                'arrminimum' => [1, 5, 10, 50, 100], // Jumlah pakaian
                'arrharga' => [50000, 48000, 46000, 44000, 42000], // Harga per pakaian
            ],
            [
                'nama' => 'Kaos Wanita Sablon (Basic)',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [55000, 53000, 51000, 49000, 47000],
            ],
            [
                'nama' => 'Kaos Polos Pria',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [45000, 43000, 41000, 39000, 37000],
            ],
            [
                'nama' => 'Kaos Polos Wanita',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [48000, 46000, 44000, 42000, 40000],
            ],
            [
                'nama' => 'Kemeja Pria (Polos)',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [120000, 115000, 110000, 105000, 100000],
            ],
            [
                'nama' => 'Kemeja Wanita (Polos)',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [130000, 125000, 120000, 115000, 110000],
            ],
            [
                'nama' => 'Jaket Pria (Hoodie)',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [200000, 190000, 180000, 170000, 160000],
            ],
            [
                'nama' => 'Jaket Wanita (Hoodie)',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [210000, 200000, 190000, 180000, 170000],
            ],
            [
                'nama' => 'Sweater Pria',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [170000, 160000, 150000, 140000, 130000],
            ],
            [
                'nama' => 'Sweater Wanita',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [180000, 170000, 160000, 150000, 140000],
            ],
            [
                'nama' => 'Celana Jeans Pria',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [200000, 190000, 180000, 170000, 160000],
            ],
            [
                'nama' => 'Celana Jeans Wanita',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [210000, 200000, 190000, 180000, 170000],
            ],
            [
                'nama' => 'Dress Wanita',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [250000, 240000, 230000, 220000, 210000],
            ],
            [
                'nama' => 'Jaket Kulit Pria',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [500000, 480000, 460000, 440000, 420000],
            ],
            [
                'nama' => 'Jaket Kulit Wanita',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [520000, 500000, 480000, 460000, 440000],
            ],
            [
                'nama' => 'Setelan Pria (Kombinasi)',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [300000, 290000, 280000, 270000, 260000],
            ],
            [
                'nama' => 'Setelan Wanita (Kombinasi)',
                'arrminimum' => [1, 5, 10, 50, 100],
                'arrharga' => [320000, 310000, 300000, 290000, 280000],
            ],
        ]; 
        $this->addjenisbahansetup(5, $idvendor, $arrdefaultjenisbahan);
    }
    public function setuppaperbag($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'Paperbag Kertas Kraft Coklat 100 gsm',
                'arrminimum' => [1, 10, 50, 100, 500], // Jumlah paperbag
                'arrharga' => [3000, 2800, 2600, 2400, 2200], // Harga per paperbag
            ],
            [
                'nama' => 'Paperbag Kertas Kraft Coklat 125 gsm',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [3500, 3300, 3100, 2900, 2700],
            ],
            [
                'nama' => 'Paperbag Kertas Art Paper 150 gsm',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [4000, 3800, 3600, 3400, 3200],
            ],
            [
                'nama' => 'Paperbag Kertas Art Carton 210 gsm',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [4500, 4300, 4100, 3900, 3700],
            ],
            [
                'nama' => 'Paperbag Kertas Ivory 190 gsm',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [5000, 4800, 4600, 4400, 4200],
            ],
            [
                'nama' => 'Paperbag Kertas Ivory 230 gsm',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [5500, 5300, 5100, 4900, 4700],
            ],
            [
                'nama' => 'Paperbag Kertas Duplex 250 gsm',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [6000, 5800, 5600, 5400, 5200],
            ],
            [
                'nama' => 'Paperbag Kertas Laminasi Doff 150 gsm',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [7000, 6800, 6600, 6400, 6200],
            ],
            [
                'nama' => 'Paperbag Kertas Laminasi Glossy 150 gsm',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [7500, 7300, 7100, 6900, 6700],
            ],
            [
                'nama' => 'Paperbag Custom Design (Full Color)',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [8000, 7800, 7600, 7400, 7200],
            ],
        ];
        $this->addjenisbahansetup(6, $idvendor, $arrdefaultjenisbahan);
    }
    public function setupaksesoris($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'Gantungan Kunci Akrilik',
                'arrminimum' => [1, 10, 50, 100, 500], // Jumlah unit
                'arrharga' => [10000, 9500, 9000, 8500, 8000], // Harga per unit
            ],
            [
                'nama' => 'Pin Bulat Diameter 44mm',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [8000, 7500, 7000, 6500, 6000],
            ],
            [
                'nama' => 'Tumbler Custom (350 ml)',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [50000, 48000, 46000, 44000, 42000],
            ],
            [
                'nama' => 'Mug Custom (Full Color)',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [40000, 38000, 36000, 34000, 32000],
            ],
            [
                'nama' => 'Notebook Custom (A5)',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [30000, 28000, 26000, 24000, 22000],
            ],
            [
                'nama' => 'Notebook Spiral Custom (A5)',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [35000, 33000, 31000, 29000, 27000],
            ],
            [
                'nama' => 'Tote Bag Kain Canvas Custom',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [45000, 43000, 41000, 39000, 37000],
            ],
            [
                'nama' => 'Tote Bag Kain Blacu Custom',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [35000, 33000, 31000, 29000, 27000],
            ],
            [
                'nama' => 'Topi Custom (Bordir)',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [50000, 48000, 46000, 44000, 42000],
            ],
            [
                'nama' => 'Flashdisk Custom (8GB)',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [75000, 72000, 69000, 66000, 63000],
            ],
            [
                'nama' => 'Gelang Karet Custom',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [10000, 9500, 9000, 8500, 8000],
            ],
            [
                'nama' => 'Payung Custom (Full Color)',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [60000, 58000, 56000, 54000, 52000],
            ],
            [
                'nama' => 'Sticker Vinyl Die-Cut Custom',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [5000, 4800, 4600, 4400, 4200],
            ],
            [
                'nama' => 'Plakat Akrilik Custom',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [100000, 95000, 90000, 85000, 80000],
            ],
        ];
        
        $this->addjenisbahansetup(7, $idvendor, $arrdefaultjenisbahan);
    }
    public function setupundangan($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'Undangan Kertas Art Paper 150 gsm',
                'arrminimum' => [10, 50, 100, 500, 1000], // Jumlah undangan
                'arrharga' => [4000, 3500, 3200, 3000, 2800], // Harga per undangan
            ],
            [
                'nama' => 'Undangan Kertas Art Carton 210 gsm',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [5000, 4500, 4200, 4000, 3800],
            ],
            [
                'nama' => 'Undangan Kertas Ivory 190 gsm',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [5500, 5000, 4700, 4500, 4300],
            ],
            [
                'nama' => 'Undangan Kertas Ivory 230 gsm',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [6000, 5500, 5200, 5000, 4800],
            ],
            [
                'nama' => 'Undangan Kertas Linen 250 gsm',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [7000, 6500, 6200, 6000, 5800],
            ],
            [
                'nama' => 'Undangan Kertas Jasmine 220 gsm',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [7500, 7000, 6700, 6500, 6300],
            ],
            [
                'nama' => 'Undangan Hard Cover Custom',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [12000, 11000, 10500, 10000, 9500],
            ],
            [
                'nama' => 'Undangan Emboss + Hot Print',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [15000, 14000, 13500, 13000, 12500],
            ],
            [
                'nama' => 'Undangan Lipat 2 Custom',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [8000, 7500, 7200, 7000, 6800],
            ],
            [
                'nama' => 'Undangan Lipat 3 Custom',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [10000, 9500, 9200, 9000, 8800],
            ],
        ];
        $this->addjenisbahansetup(8, $idvendor, $arrdefaultjenisbahan);
    }
    public function setupkalender($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'Kalender Meja 1 Lembar',
                'arrminimum' => [10, 50, 100, 500, 1000], // Jumlah kalender
                'arrharga' => [15000, 14000, 13000, 12000, 11000], // Harga per kalender
            ],
            [
                'nama' => 'Kalender Meja 6 Lembar',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [25000, 24000, 23000, 22000, 21000],
            ],
            [
                'nama' => 'Kalender Meja 12 Lembar',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [30000, 29000, 28000, 27000, 26000],
            ],
            [
                'nama' => 'Kalender Dinding 1 Lembar',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [20000, 19000, 18000, 17000, 16000],
            ],
            [
                'nama' => 'Kalender Dinding 6 Lembar',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [35000, 34000, 33000, 32000, 31000],
            ],
            [
                'nama' => 'Kalender Dinding 12 Lembar',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [45000, 44000, 43000, 42000, 41000],
            ],
            [
                'nama' => 'Kalender Poster A3',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [12000, 11000, 10500, 10000, 9500],
            ],
            [
                'nama' => 'Kalender Poster A2',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [20000, 19000, 18500, 18000, 17500],
            ],
            [
                'nama' => 'Kalender Poster Custom',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [30000, 29000, 28000, 27000, 26000],
            ],
            [
                'nama' => 'Kalender Dinding Hardcover 12 Lembar',
                'arrminimum' => [10, 50, 100, 500, 1000],
                'arrharga' => [60000, 58000, 56000, 54000, 52000],
            ],
        ];
        
        $this->addjenisbahansetup(9, $idvendor, $arrdefaultjenisbahan);
    }
    public function setupkartunama($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'Kartu Nama Art Paper 260 Gsm (1 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000], // Jumlah kartu nama
                'arrharga' => [150, 140, 130, 120, 110], // Harga per kartu nama
            ],
            [
                'nama' => 'Kartu Nama Art Paper 260 Gsm (2 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [170, 160, 150, 140, 130],
            ],
            [
                'nama' => 'Kartu Nama Art Carton 310 Gsm (1 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [200, 190, 180, 170, 160],
            ],
            [
                'nama' => 'Kartu Nama Art Carton 310 Gsm (2 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [220, 210, 200, 190, 180],
            ],
            [
                'nama' => 'Kartu Nama Laminasi Glossy (1 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [250, 240, 230, 220, 210],
            ],
            [
                'nama' => 'Kartu Nama Laminasi Glossy (2 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [270, 260, 250, 240, 230],
            ],
            [
                'nama' => 'Kartu Nama Laminasi Doff (1 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [250, 240, 230, 220, 210],
            ],
            [
                'nama' => 'Kartu Nama Laminasi Doff (2 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [270, 260, 250, 240, 230],
            ],
            [
                'nama' => 'Kartu Nama Emboss + Spot UV',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [500, 480, 460, 440, 420],
            ],
            [
                'nama' => 'Kartu Nama Custom Die-Cut',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [700, 680, 660, 640, 620],
            ],
        ];

        $this->addjenisbahansetup(10, $idvendor, $arrdefaultjenisbahan);
    }
    public function setupbrosur($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'Brosur Art Paper 120 Gsm (1 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [500, 480, 450, 430, 400],
            ],
            [
                'nama' => 'Brosur Art Paper 120 Gsm (2 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [600, 580, 550, 530, 500],
            ],
            [
                'nama' => 'Brosur Art Paper 150 Gsm (1 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [700, 680, 650, 630, 600],
            ],
            [
                'nama' => 'Brosur Art Paper 150 Gsm (2 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [800, 780, 750, 730, 700],
            ],
            [
                'nama' => 'Brosur Art Carton 210 Gsm (1 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [900, 880, 850, 830, 800],
            ],
            [
                'nama' => 'Brosur Art Carton 210 Gsm (2 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [1000, 980, 950, 930, 900],
            ],
            [
                'nama' => 'Brosur Lipat 2 Art Paper 120 Gsm',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [700, 680, 650, 630, 600],
            ],
            [
                'nama' => 'Brosur Lipat 3 Art Paper 150 Gsm',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [1000, 980, 950, 930, 900],
            ],
            [
                'nama' => 'Brosur Custom Die-Cut',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [1200, 1180, 1150, 1130, 1100],
            ],
        ];
        
        $this->addjenisbahansetup(11, $idvendor, $arrdefaultjenisbahan);
    }
    public function setupamplop($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'Amplop Polos Kertas HVS 70 Gsm',
                'arrminimum' => [100, 200, 500, 1000, 5000], 
                'arrharga' => [500, 480, 450, 430, 400], 
            ],
            [
                'nama' => 'Amplop Polos Kertas HVS 80 Gsm',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [600, 580, 550, 530, 500],
            ],
            [
                'nama' => 'Amplop Polos Kertas Art Paper 120 Gsm',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [700, 680, 650, 630, 600],
            ],
            [
                'nama' => 'Amplop Custom Kertas HVS 70 Gsm (1 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [800, 780, 750, 730, 700],
            ],
            [
                'nama' => 'Amplop Custom Kertas HVS 80 Gsm (2 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [900, 880, 850, 830, 800],
            ],
            [
                'nama' => 'Amplop Custom Kertas Art Paper 120 Gsm (1 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [1000, 980, 950, 930, 900],
            ],
            [
                'nama' => 'Amplop Custom Kertas Art Paper 150 Gsm (2 Sisi)',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [1200, 1180, 1150, 1130, 1100],
            ],
            [
                'nama' => 'Amplop Custom Full Color Laminasi Doff',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [1500, 1480, 1450, 1430, 1400],
            ],
            [
                'nama' => 'Amplop Custom Full Color Laminasi Glossy',
                'arrminimum' => [100, 200, 500, 1000, 5000],
                'arrharga' => [1500, 1480, 1450, 1430, 1400],
            ],
        ];
        
        
        $this->addjenisbahansetup(12, $idvendor, $arrdefaultjenisbahan);
    }
    public function setupcasehp($idvendor){
        $arrdefaultjenisbahan = [
            [
                'nama' => 'Case HP Polos Hardcase',
                'arrminimum' => [1, 10, 50, 100, 500], 
                'arrharga' => [25000, 23000, 21000, 19000, 17000], 
            ],
            [
                'nama' => 'Case HP Polos Softcase',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [20000, 19000, 18000, 17000, 15000],
            ],
            [
                'nama' => 'Case HP Custom Hardcase Full Color',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [30000, 28000, 26000, 24000, 22000],
            ],
            [
                'nama' => 'Case HP Custom Softcase Full Color',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [28000, 26000, 24000, 22000, 20000],
            ],
            [
                'nama' => 'Case HP Custom 3D Print',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [35000, 33000, 31000, 29000, 27000],
            ],
            [
                'nama' => 'Case HP Custom Glitter',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [40000, 38000, 36000, 34000, 32000],
            ],
            [
                'nama' => 'Case HP Custom Anti-Shock',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [45000, 43000, 41000, 39000, 37000],
            ],
            [
                'nama' => 'Case HP Custom Flip Cover',
                'arrminimum' => [1, 10, 50, 100, 500],
                'arrharga' => [50000, 48000, 46000, 44000, 42000],
            ],
        ];
        
        
        $this->addjenisbahansetup(13, $idvendor, $arrdefaultjenisbahan);
    }  

    public function dosetup(Request $request){
        try{
            $request->validate([
                'layanans' => 'required|array|min:1',
            ]);
        }
        catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        $idvendor = $request->input('idvendor');
        $idlayanans = $request->input('layanans');
        foreach($idlayanans as $l){
            if($l==1){
                $this->setupfotokopi($idvendor);
            } elseif($l==2){
                $this->setupstiker($idvendor);
            } elseif($l==3){
                $this->setupbuku($idvendor);
            } elseif($l==4){
                $this->setupbanner($idvendor);
            } elseif($l==5){
                $this->setuppakaian($idvendor);
            } elseif($l==6){
                $this->setuppaperbag($idvendor);
            } elseif($l==7){
                $this->setupaksesoris($idvendor);
            } elseif($l==8){
                $this->setupundangan($idvendor);
            } elseif($l==9){
                $this->setupkalender($idvendor);
            } elseif($l==10){
                $this->setupkartunama($idvendor);
            } elseif($l==11){
                $this->setupbrosur($idvendor);
            } elseif($l==12){
                $this->setupamplop($idvendor);
            } elseif($l==13){
                $this->setupcasehp($idvendor);
            }
            
        }
        return redirect()->route('layananindex', [$idvendor]);
    }

    public function create() {
        return view('layanans.create');
    }

    public function store(Request $request) {
        $vendor_id = $request->input('id_vendor');
        $layanan_id = $request->input('id_layanan');
        
        $layanan = new Layanan();
        $layanan->nama = $request->input('nama');
        if($request->input('deskripsi')){
            $layanan->deskripsi = $request->input('deskripsi');
        }
        $layanan->biaya_tambahan = $request->input('biaya_tambahan');
        $layanan->save();
        return redirect()->route('layanan.detail_layanan', [$vendor_id, $layanan_id]);
    }
}
