<?php

namespace App\Http\Controllers;

use App\Models\JenisBahanCetak;
use App\Models\Nota;
use App\Models\Pemesanan;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Smalot\PdfParser\Parser;

class PemesananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cekperubahan($idpemesanan)
    {
        $pemesanan = Pemesanan::find($idpemesanan);

        $tanggal_pemesanan = $pemesanan->updated_at;
        $jenisbahan = JenisBahanCetak::findOrFail($pemesanan->jenis_bahan_cetaks_id);
        $tanggal_update_detail = $jenisbahan->updated_at;
        // dd("update detail: $tanggal_update_detail - pemesanan: $tanggal_pemesanan");

        if ($tanggal_pemesanan->lt($tanggal_update_detail)) {
            return true;
        } else {
            return false;
        }
    }

    public function index() {}

    public function indexOrder($idvendors)
    {
        $pemesanans = DB::table('pemesanans')
            ->where('penggunas_email', '=', Auth::user()->email)
            ->where('pemesanans.notas_id', '=', null)
            ->where('pemesanans.vendors_id', '=', $idvendors)
            ->get();

        if (count($pemesanans) == 0) {
            return redirect()->route('indexCart');
        }

        $subtotal = 0;
        foreach ($pemesanans as $p) {

            $jenisbahan = DB::table('jenis_bahan_cetaks')
                ->where('id', '=', $p->jenis_bahan_cetaks_id)
                ->select('nama', 'deleted_at')
                ->first();

            if ($jenisbahan->deleted_at) {
                $p->status = 'deleted';
                $layanan = DB::table('vendors_has_jenis_bahan_cetaks')
                    ->join('layanan_cetaks', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', 'layanan_cetaks.id')
                    ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $p->jenis_bahan_cetaks_id)
                    ->select('layanan_cetaks.nama as namalayanan', 'layanan_cetaks.satuan as satuanlayanan')
                    ->first();
                $p->layanan = $layanan->namalayanan;
                $p->satuan = $layanan->satuanlayanan;
                $p->nama_jenis_bahan = $jenisbahan->nama;
            } else {
                $p->status = 'available';
                $harga_cetaks = DB::table('harga_cetaks')
                    ->where('id', '=', $p->harga_cetaks_id)
                    ->first();
                $p->harga_satuan = $harga_cetaks->harga_satuan;

                $layanan = DB::table('vendors_has_jenis_bahan_cetaks')
                    ->join('layanan_cetaks', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', 'layanan_cetaks.id')
                    ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $p->jenis_bahan_cetaks_id)
                    ->select('layanan_cetaks.nama as namalayanan', 'layanan_cetaks.satuan as satuanlayanan')
                    ->first();
                $p->layanan = $layanan->namalayanan;
                $p->satuan = $layanan->satuanlayanan;

                $subtotal += $p->subtotal_pesanan;
            }
        }
        // dd($pemesanans);


        return view('cart.pesanan', compact('pemesanans', 'subtotal'));
    }

    public function openeditpesanan($idpemesanan)
    {
        $pemesanan = Pemesanan::find($idpemesanan);

        $count = 0;
        $filePath = public_path('uploads/' . $idpemesanan . '.pdf');
        if (file_exists($filePath)) {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $count = count($pdf->getPages());
        }

        $jumlahcopy = $pemesanan->jumlah / $count;


        $layanan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->join('layanan_cetaks', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', 'layanan_cetaks.id')
            ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $pemesanan->jenis_bahan_cetaks_id)
            ->select('layanan_cetaks.id as id', 'layanan_cetaks.nama as nama', 'layanan_cetaks.satuan as satuan', 'layanan_cetaks.kesetaraan_pcs as kesetaraan_pcs')
            ->first();




        $vendor_id = $pemesanan->vendors_id;
        $idlayanan = $layanan->id;
        $idjenisbahan = $pemesanan->jenis_bahan_cetaks_id;

        $jenisbahan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->join('jenis_bahan_cetaks', 'jenis_bahan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id')
            ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
            ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $idlayanan)
            ->select('jenis_bahan_cetaks.*', 'vendors_has_jenis_bahan_cetaks.vendors_id as idvendor')
            ->where('jenis_bahan_cetaks.deleted_at', '=', null)
            ->get();

        $detailcetaks = DB::table('detail_cetaks')
            ->join('jenis_bahan_cetaks', 'detail_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->leftJoin('opsi_details', 'detail_cetaks.id', '=', 'opsi_details.detail_cetaks_id')
            ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
            ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $idlayanan)
            ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $idjenisbahan)
            ->where('jenis_bahan_cetaks.deleted_at', '=', null)
            ->select('detail_cetaks.*', 'jenis_bahan_cetaks.nama', 'opsi_details.id as idopsi', 'opsi_details.opsi as opsi', 'opsi_details.biaya_tambahan as biaya_tambahan')
            ->get();
        // dd($detailcetaks);

        $opsidetail = [];
        foreach ($detailcetaks as $detail) {
            if (!isset($opsidetail[$detail->id])) {
                $opsidetail[$detail->id] = [
                    'detail' => $detail,
                    'opsi' => [],
                ];
            }
            if ($detail->idopsi) {
                $opsidetail[$detail->id]['opsi'][] = [
                    'id' => $detail->idopsi,
                    'opsi' => $detail->opsi,
                    'biaya_tambahan' => $detail->biaya_tambahan,
                ];
            }
        }

        $opsidetail = array_values($opsidetail);
        // dd($opsidetail);

        $hargacetaks = DB::table('harga_cetaks')
            ->where('id_bahan_cetaks', '=', $idjenisbahan)
            ->get();

        return view('pesanan.editpesanan', compact('pemesanan', 'jumlahcopy', 'idjenisbahan', 'jenisbahan', 'opsidetail', 'hargacetaks', 'layanan'));
    }

    public function load_editpesanan($idjenisbahan)
    {



        $layanan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->join('layanan_cetaks', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', 'layanan_cetaks.id')
            ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $idjenisbahan)
            ->select('layanan_cetaks.id as id', 'layanan_cetaks.nama as nama', 'layanan_cetaks.satuan as satuan', 'layanan_cetaks.kesetaraan_pcs as kesetaraan_pcs', 'vendors_has_jenis_bahan_cetaks.vendors_id as idvendor')
            ->first();

        $vendor_id = $layanan->idvendor;
        $idlayanan = $layanan->id;

        $detailcetaks = DB::table('detail_cetaks')
            ->join('jenis_bahan_cetaks', 'detail_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->leftJoin('opsi_details', 'detail_cetaks.id', '=', 'opsi_details.detail_cetaks_id')
            ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
            ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $idlayanan)
            ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $idjenisbahan)
            ->where('jenis_bahan_cetaks.deleted_at', '=', null)
            ->select('detail_cetaks.*', 'jenis_bahan_cetaks.nama', 'opsi_details.id as idopsi', 'opsi_details.opsi as opsi', 'opsi_details.biaya_tambahan as biaya_tambahan')
            ->get();
        // dd($detailcetaks);

        $opsidetail = [];
        foreach ($detailcetaks as $detail) {
            if (!isset($opsidetail[$detail->id])) {
                $opsidetail[$detail->id] = [
                    'detail' => $detail,
                    'opsi' => [],
                ];
            }
            if ($detail->idopsi) {
                $opsidetail[$detail->id]['opsi'][] = [
                    'id' => $detail->idopsi,
                    'opsi' => $detail->opsi,
                    'biaya_tambahan' => $detail->biaya_tambahan,
                ];
            }
        }

        $opsidetail = array_values($opsidetail);
        // dd($opsidetail);

        $hargacetaks = DB::table('harga_cetaks')
            ->where('id_bahan_cetaks', '=', $idjenisbahan)
            ->get();

        $response = [
            'result' => 'success',
            'data' => [
                'opsidetail' => $opsidetail, // This should be an array of options details
                'listharga' => $hargacetaks, // This should be an array of price lists
                'deskripsi' => $layanan, // Description or any other relevant data
            ],
        ];

        return response()->json($response);
    }

    public function submitpesanan(Request $request)
    {
        try {
            $request->validate([
                'jumlah' => 'required|integer|min:1',
                'jenis_bahan_cetaks_id' => 'required',
                'vendors_id' => 'required',
                'file' => 'required|file|mimes:pdf|max:20480' //max 20mb
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with("error", 'Terjadi kesalahan saat melakukan pemesanan');
        }

        $idopsidetail = null;
        if ($request->input('idopsidetail')) {
            $idopsidetail = explode(",", $request->input('idopsidetail'));
        }
        $hargacetakcontroller = new HargaCetakController();
        $idhargacetak = $hargacetakcontroller->cekHarga($request->jumlah, $request->jenis_bahan_cetaks_id, true);
        $hargasatuan = $hargacetakcontroller->cekHarga($request->jumlah, $request->jenis_bahan_cetaks_id);
        $harga = $hargasatuan * $request->input('jumlah');
        $biaya_tambahan = 0;
        if ($request->input('idopsidetail')) {

            foreach ($idopsidetail as $key => $id) {

                $p = DB::table('opsi_details')
                    ->where('id', '=', $id)
                    ->first();
                if ($p->biaya_tambahan > 0) {
                    if ($p->tipe == 'tambahan') {
                        $biaya_tambahan = $p->biaya_tambahan;
                    } elseif ($p->tipe == 'satuan') {
                        $biaya_tambahan = $p->biaya_tambahan * $request->input('jumlah');
                    } elseif ($p->tipe == 'jumlah') {
                        $biaya_tambahan = $p->biaya_tambahan * $request->input('jumlahcopy');
                    }
                    $harga += $biaya_tambahan;
                }
            }
        }

        $perlu_verifikasi = 0;
        if ($harga > 200000) {
            $perlu_verifikasi  = 1;
        }

        $id = DB::table('pemesanans')->insertGetId([
            'penggunas_email' => Auth::user()->email,
            'jumlah' => $request->input('jumlah'),
            'subtotal_pesanan' => $harga,
            'biaya_tambahan' => $biaya_tambahan,
            'url_file' => '',
            'catatan' => $request->input('catatan'),
            'perlu_verifikasi' => $perlu_verifikasi,
            'harga_cetaks_id' => $idhargacetak,
            'jenis_bahan_cetaks_id' => $request->input('jenis_bahan_cetaks_id'),
            'vendors_id' => $request->input('vendors_id'),
            'updated_at' => Carbon::now('Asia/Jakarta'),
            'created_at' => Carbon::now('Asia/Jakarta'),
        ]);
        if ($request->input('idopsidetail')) {
            foreach ($idopsidetail as $od) {
                DB::table('pemesanans_has_opsi_details')->insert([
                    'pemesanans_id' => $id,
                    'opsi_details_id' => $od,
                ]);
            }
        }

        $file = $request->file('file');
        $fileName = $id . '.pdf';

        $directory = base_path('../pemesanan');

        $file->move($directory, $fileName);
        $relativePath = 'pemesanan/' . $fileName;
        $pemesanan = Pemesanan::find($id);
        $pemesanan->url_file = $relativePath;
        $pemesanan->save();

        $filePath = $directory . '/' . $fileName;
        $uploadDirectory = 'uploads';

        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        copy($filePath, $uploadDirectory . '/' . $fileName);





        return ["idpemesanan" => $id, "idvendor" => $request->input('vendors_id'), 'message' => "Pesanan dimasukkan ke dalam cart"];
    }

    public function updatepesanantanpafile(Request $request)
    {
        try {

            $request->validate([
                'jumlah' => 'required|integer|min:1',

            ]);
        } catch (Exception $e) {
            return redirect()->back()->with("error", 'Jumlah tidak mencukupi minimum (1)');
        }

        $idopsidetail = null;
        if ($request->input('idopsidetail')) {
            $idopsidetail = explode(",", $request->input('idopsidetail'));
        }
        $jumlah = $request->input('jumlah');

        $hargacetakcontroller = new HargaCetakController();
        $idhargacetak = $hargacetakcontroller->cekHarga($request->jumlah, $request->jenis_bahan_cetaks_id, true);
        $hargasatuan = $hargacetakcontroller->cekHarga($request->jumlah, $request->jenis_bahan_cetaks_id);
        $harga = $hargasatuan * $jumlah;
        $biaya_tambahan = 0;

        DB::table('pemesanans_has_opsi_details')
            ->where('pemesanans_id', '=', $request->input('idpemesanan'))
            ->delete();


        $arrdetail = array();
        // $pees = DB::table('pemesanans')
        //     ->where('id', '=', $request->input('idpemesanan'))
        //     ->first();

        // $jenisbahan = DB::table('jenis_bahan_cetaks')
        //     ->where('id', '=', $request->input('jenis_bahan_cetaks_id'))
        //     ->first();
        // array_push($arrdetail, "jenis:" . $jenisbahan->nama);
        // $layanan_id = DB::table('vendors_has_jenis_bahan_cetaks')
        //     ->where('jenis_bahan_cetaks_id', '=', $request->input('jenis_bahan_cetaks_id'))
        //     ->first();
        // $layanan_nama = DB::table('layanan_cetaks')
        //     ->where('id', '=', $layanan_id->layanan_cetaks_id)
        //     ->first();
        // array_push($arrdetail, "layanan:" . $layanan_nama->nama);
        // dd($arrdetail);

        if ($idopsidetail) {
            foreach ($idopsidetail as $key => $id) {
                // dd($id);
                // cek info opsi

                // $p = DB::table('opsi_details')
                //     ->where('id', '=', $id)
                //     ->first();
                // // dd($p);
                // $detail = DB::table('detail_cetaks')
                //     ->where('id', '=', $p->detail_cetaks_id)
                //     ->first();
                // $jenisbahan = DB::table('jenis_bahan_cetaks')
                //     ->where('id', '=', $detail->jenis_bahan_cetaks_id)
                //     ->first();
                // $detail->jenisbahan = $jenisbahan->nama;
                // $layanan_id = DB::table('vendors_has_jenis_bahan_cetaks')
                //     ->where('jenis_bahan_cetaks_id', '=', $jenisbahan->id)
                //     ->first();
                // $layanan_nama = DB::table('layanan_cetaks')
                //     ->where('id', '=', $layanan_id->layanan_cetaks_id)
                //     ->first();
                // $detail->layanan = $layanan_nama->nama;
                // array_push($arrdetail, $detail);
                // // }

                // dd($arrdetail);

                $p = DB::table('opsi_details')
                    ->where('id', '=', $id)
                    ->first();

                DB::table('pemesanans_has_opsi_details')->insert([
                    'pemesanans_id' => $request->input('idpemesanan'),
                    'opsi_details_id' => $id,
                ]);

                if ($p->biaya_tambahan > 0) {
                    if ($p->tipe == 'tambahan') {
                        $biaya_tambahan = $p->biaya_tambahan;
                    } elseif ($p->tipe == 'satuan') {
                        $biaya_tambahan = $p->biaya_tambahan * $request->input('jumlah');
                    } elseif ($p->tipe == 'jumlah') {
                        $biaya_tambahan = $p->biaya_tambahan * $request->input('jumlahcopy');
                    }
                    $harga += $biaya_tambahan;
                }
            }
        }

        $perlu_verifikasi = 0;
        if ($harga > 200000) {
            $perlu_verifikasi  = 1;
        }
        $pemesanan = Pemesanan::find($request->input('idpemesanan'));
        $pemesanan->jumlah = $jumlah;
        $pemesanan->subtotal_pesanan = $harga;
        $pemesanan->biaya_tambahan = $biaya_tambahan;
        $pemesanan->perlu_verifikasi = $perlu_verifikasi;
        if ($request->input('catatan')) {
            $pemesanan->catatan = $request->input('catatan');
        }
        $pemesanan->harga_cetaks_id = $idhargacetak;
        $pemesanan->jenis_bahan_cetaks_id = $request->input('jenis_bahan_cetaks_id');
        $pemesanan->vendors_id = $request->input('vendors_id');
        $pemesanan->updated_at = Carbon::now('Asia/Jakarta');

        $pemesanan->save();

        // dd($pemesanan);

        $opsidetail = DB::table('pemesanans_has_opsi_details')
            ->where('pemesanans_id', '=', $request->input('idpemesanan'))
            ->get();

        // dd($opsidetail);

        return ["idpemesanan" => $request->input('idpemesanan'), "idvendor" => $request->input('vendors_id')];
    }

    public function updatepesanandenganfile(Request $request)
    {


        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf|max:20480',
            ], [
                'file.required' => 'File wajib diunggah.',
                'file.file' => 'Input harus berupa file.',
                'file.mimes' => 'File harus berupa PDF.',
                'file.max' => 'Ukuran file tidak boleh lebih dari 20MB.',
            ]);
        } catch (exception $e) {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:pdf|max:20480',
            ]);

            if ($validator->fails()) {
                $errorMessage = $validator->errors()->first();
                return back()->with('error', $errorMessage);
            }
        }
        // dd('a');

        try {
            $request->validate([
                'jumlah' => 'required|integer|min:1',

            ]);
        } catch (Exception $e) {
            return redirect()->back()->with("error", 'Jumlah tidak mencukupi minimum (1)');
        }

        $idopsidetail = null;
        if ($request->input('idopsidetail')) {
            $idopsidetail = explode(",", $request->input('idopsidetail'));
        }
        $hargacetakcontroller = new HargaCetakController();
        $idhargacetak = $hargacetakcontroller->cekHarga($request->jumlah, $request->jenis_bahan_cetaks_id, true);
        $hargasatuan = $hargacetakcontroller->cekHarga($request->jumlah, $request->jenis_bahan_cetaks_id);
        $harga = $hargasatuan * $request->input('jumlah');
        $biaya_tambahan = 0;
        DB::table('pemesanans_has_opsi_details')
            ->where('pemesanans_id', '=', $request->input('idpemesanan'))
            ->delete();
        // dd($idopsidetail);
        if ($idopsidetail) {
            foreach ($idopsidetail as $key => $id) {
                $p = DB::table('opsi_details')
                    ->where('id', '=', $id)
                    ->first();
                if ($p->biaya_tambahan > 0) {
                    if ($p->tipe == 'tambahan') {
                        $biaya_tambahan = $p->biaya_tambahan;
                    } elseif ($p->tipe == 'satuan') {
                        $biaya_tambahan = $p->biaya_tambahan * $request->input('jumlah');
                    } elseif ($p->tipe == 'jumlah') {
                        $biaya_tambahan = $p->biaya_tambahan * $request->input('jumlahcopy');
                    }
                    $harga += $biaya_tambahan;
                }
                DB::table('pemesanans_has_opsi_details')->insert([
                    'pemesanans_id' => $request->input('idpemesanan'),
                    'opsi_details_id' => $id,
                ]);
            }
        }

        $perlu_verifikasi = 0;
        if ($harga > 200000) {
            $perlu_verifikasi  = 1;
        }



        $id = DB::table('pemesanans')->insertGetId([
            'penggunas_email' => Auth::user()->email,
            'jumlah' => $request->input('jumlah'),
            'subtotal_pesanan' => $harga,
            'biaya_tambahan' => $biaya_tambahan,
            'url_file' => '',
            'catatan' => $request->input('catatan'),
            'perlu_verifikasi' => $perlu_verifikasi,
            'harga_cetaks_id' => $idhargacetak,
            'jenis_bahan_cetaks_id' => $request->input('jenis_bahan_cetaks_id'),
            'vendors_id' => $request->input('vendors_id'),
            'updated_at' => Carbon::now('Asia/Jakarta'),
            'created_at' => Carbon::now('Asia/Jakarta'),
        ]);
        if ($request->input('idopsidetail')) {
            foreach ($idopsidetail as $od) {
                DB::table('pemesanans_has_opsi_details')->insert([
                    'pemesanans_id' => $id,
                    'opsi_details_id' => $od,
                ]);
            }
        }

        $file = $request->file('file');
        $fileName = $id . '.pdf';

        $directory = base_path('../pemesanan');

        $file->move($directory, $fileName);
        $relativePath = 'pemesanan/' . $fileName;
        $newpemesanan = Pemesanan::find($id);
        $newpemesanan->url_file = $relativePath;
        $newpemesanan->save();

        $filePath = $directory . '/' . $fileName;
        $uploadDirectory = 'uploads';

        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }


        $deletefilepath = public_path('uploads/' . $request->input('idpemesanan') . '.pdf');
        DB::table('pemesanans_has_opsi_details')->where('pemesanans_id', $request->input('idpemesanan'))->delete();
        DB::table('pemesanans')->where('id', $request->input('idpemesanan'))->delete();
        unlink($deletefilepath);

        copy($filePath, $uploadDirectory . '/' . $fileName);

        return ["idpemesanan" => $id, "idvendor" => $request->input('vendors_id')];
    }

    public function lihatcatatan($idpemesanan)
    {
        $pemesanan = Pemesanan::find($idpemesanan);
        $layanan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->join('layanan_cetaks', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', 'layanan_cetaks.id')
            ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $pemesanan->jenis_bahan_cetaks_id)
            ->select('layanan_cetaks.nama as namalayanan', 'layanan_cetaks.satuan as satuanlayanan')
            ->first();
        return ["jumlah" => $pemesanan->jumlah, "catatan" => $pemesanan->catatan, "layanan" => $layanan->namalayanan, "satuan" => $layanan->satuanlayanan];
    }

    public function bukacheckout(Request $request)
    {
        $request->validate([
            'idpemesanans' => 'required|array',
            'subtotal' => 'required|gt:0',
        ]);

        // dd($request);
        $idpemesanans = $request->input('idpemesanans');

        $idperubahan = [];

        foreach ($idpemesanans as $ip) {
            $adaperubahan = $this->cekperubahan($ip);
            if ($adaperubahan) {
                $pesanan = DB::table('pemesanans')
                    ->where('id', '=', $ip)
                    ->select('jumlah', 'jenis_bahan_cetaks_id',)
                    ->first();

                $layanan = DB::table('vendors_has_jenis_bahan_cetaks')
                    ->join('layanan_cetaks', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', 'layanan_cetaks.id')
                    ->where('jenis_bahan_cetaks_id', '=', $pesanan->jenis_bahan_cetaks_id)
                    ->select('layanan_cetaks.nama', 'layanan_cetaks.satuan')
                    ->first();
                $perubahan = [
                    "jumlah" => $pesanan->jumlah,
                    'layanan' => $layanan->nama,
                    'satuan' => $layanan->satuan
                ];
                array_push($idperubahan, $perubahan);
            }
        }

        if (count($idperubahan) > 0) {

            $message = "Terdapat perubahan detil pada pesanan:\n";
            foreach ($idperubahan as $iper) {
                $message .= "• " . $iper["layanan"] . ": " . $iper["jumlah"] . " " . $iper["satuan"] . "\n";
            }
            $message .= "Silakan lakukan perubahan pada detil pesanan tersebut sebelum melakukan checkout.";

            return back()->with('message', $message);
        }

        $biaya_tambahan = $request->input('biaya_tambahan');
        $pemesanans = [];
        $subtotal = $request->subtotal;

        $idvendor = DB::table('pemesanans')
            ->where('id', '=', $idpemesanans)
            ->select('vendors_id')
            ->first();

        $pengantars = DB::table('penggunas')
            ->where('vendors_id', '=', $idvendor->vendors_id)
            ->where('role', '=', 'pengantar')
            ->count();


        $adapengantar = true;

        if ($pengantars == 0) {
            $adapengantar = false;
        }

        foreach ($idpemesanans as $key => $id) {
            $p = DB::table('pemesanans')
                ->where('id', '=', $id)
                ->first();

            $harga_cetaks = DB::table('harga_cetaks')
                ->where('id', '=', $p->harga_cetaks_id)
                ->first();
            $p->harga_satuan = $harga_cetaks->harga_satuan;

            $p->biaya_tambahan = $biaya_tambahan[$key];



            $layanan = DB::table('vendors_has_jenis_bahan_cetaks')
                ->join('layanan_cetaks', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', 'layanan_cetaks.id')
                ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $p->jenis_bahan_cetaks_id)
                ->select('layanan_cetaks.nama as namalayanan', 'layanan_cetaks.satuan as satuanlayanan')
                ->first();
            $p->layanan = $layanan->namalayanan;
            $p->satuan = $layanan->satuanlayanan;
            array_push($pemesanans, $p);
        }

        // dd($pemesanans);
        // return ['subtotal'=>$subtotal, 'pemesanans'=>$pemesanans];
        return view('cart.checkout', compact('pemesanans', 'subtotal', 'adapengantar'));
    }

    public function deletepesanan(Request $request)
    {
        $idpemesanan = $request->input('idpemesanan');
        $pemesanan = Pemesanan::find($idpemesanan);

        $filePath = base_path('../Program TA/' . $pemesanan->url_file);

        $filekosong = base_path('public/assets/0byte.txt');
        if (file_exists($filePath)) {
            try {
                copy($filekosong, $filePath);
            } catch (Exception $e) {
                return back()->with('message', 'Gagal menghapus file pemesanan');
            }
        }
        DB::table('pemesanans_has_opsi_details')->where('pemesanans_id', $idpemesanan)->delete();


        DB::table('pemesanans')->where('id', $idpemesanan)->delete();
        $idvendor = $request->input('idvendor');
        return redirect()->route('indexOrder', ['idvendor' => $idvendor]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $notas = Nota::all();
        return view('pemesanans.create', compact('notas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'penggunas_email' => 'required',
            'jumlah' => 'required|integer|min:1',
            'url_file' => 'required',
            'harga_cetaks_id' => 'required',
            'harga_cetaks_id_jenis_bahan_cetaks' => 'required',
            'vendors_id' => 'required',
            'notas_id' => 'required',
        ]);

        Pemesanan::create($request->all());

        return redirect()->route('pemesanans.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Debugging to see if the method is hit
        $url = '/pesanancetak/' . $id;
        return redirect($url); // This will show the ID passed in the URL
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $notas = Nota::all();
        return view('pemesanans.edit', compact('pemesanan', 'notas'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pemesanan $pemesanan)
    {
        $request->validate([
            'penggunas_email' => 'required',
            'jumlah' => 'required|integer|min:1',
            'url_file' => 'required',
            'harga_cetaks_id' => 'required',
            'harga_cetaks_id_jenis_bahan_cetaks' => 'required',
            'vendors_id' => 'required',
            'notas_id' => 'required',
        ]);

        $pemesanan->update($request->all());

        return redirect()->route('pemesanans.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pemesanan $pemesanan)
    {
        $pemesanan->delete();
        return redirect()->route('pemesanans.index');
    }
}
