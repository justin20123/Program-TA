<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\NotaProgress;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class NotaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getStatus($nota)
    {
        if (!$nota->waktu_menerima_pesanan) {
            $status_nota = "Menunggu diterima vendor";
        } elseif (!$nota->waktu_diantar && !$nota->waktu_tunggu_diambil) {
            $status_nota = "Pesanan diterima";
        } else if (($nota->waktu_diantar || $nota->waktu_tunggu_diambil) && !$nota->waktu_selesai) {
            if ($nota->waktu_diantar) {
                $status_nota = "Diantar";
            } else {
                $status_nota = "Menunggu diambil";
            }
        } else if ($nota->waktu_selesai) {
            $status_nota = "Selesai";
        }
        return $status_nota;
    }


    public function placeorder(Request $request)
    {
        $idnota = 0;
        $idpemesanansstring = $request->idpemesanans;
        $idpemesanans = explode(",", $idpemesanansstring);
        $pemesanan = Pemesanan::findOrFail($idpemesanans[0]);
        $vendorid = $pemesanan->vendors_id;

        if ($request->opsiantar == "diambil") {
            $request->validate([
                'idpemesanans' => 'required',
            ]);

            $idnota = DB::table('notas')->insertGetid([
                'harga_total' => $request->harga_total,
                'waktu_transaksi' => Carbon::now('Asia/Jakarta'),
                'opsi_pengambilan' => "diambil",
                'waktu_menerima_pesanan' => Carbon::now('Asia/Jakarta'),
                'waktu_diantar' => null,
                'waktu_tunggu_diambil' => null,
                'waktu_selesai' => null,
                'ulasan' => "",
                'catatan_antar' => $request->input('catatan_antar'),
            ]);
        } else {
            $request->validate([
                'idpemesanans' => 'required',
                'latitude' => 'required|',
                'longitude' => 'required|'
            ]);


            $pengantars = DB::table('penggunas')
                ->where('vendors_id', '=', $vendorid)
                ->where('role', '=', 'pengantar')
                ->count();

            if ($pengantars == 0) {
                return back()->withInput()->with('error', 'Vendor ini belum menyediakan fitur pengantaran.');
            }

            $idnota = DB::table('notas')->insertGetid([
                'harga_total' => $request->harga_total,
                'waktu_transaksi' => Carbon::now('Asia/Jakarta'),
                'opsi_pengambilan' => "diantar",
                'waktu_menerima_pesanan' => Carbon::now('Asia/Jakarta'),
                'waktu_diantar' => null,
                'waktu_tunggu_diambil' => null,
                'waktu_selesai' => null,
                'longitude_pengambilan' => $request->longitude,
                'latitude_pengambilan' => $request->latitude,
                'ulasan' => "",
                'catatan_antar' => $request->catatan_antar,
            ]);
        }
        foreach ($idpemesanans as $id) {
            $pemesanan = Pemesanan::findOrFail($id);
            $pemesanan->notas_id = $idnota;
            $pemesanan->save();

            DB::table('notas_progress')->insert([
                'pemesanans_id' => $id,
                'notas_id' => $idnota,
                'urutan_progress' => 0,
                'waktu_progress' => Carbon::now('Asia/Jakarta'),
                'progress' => "proses",
            ]);
        }

        $pengguna = Pengguna::findOrFail(Auth::user()->id);
        $saldo = $pengguna->saldo;
        $saldobaru = $saldo - $request->harga_total;
        $pengguna->saldo = $saldobaru;
        $pengguna->save();

        return ["idnota" => $idnota, "idvendor" => $vendorid, "message" => "Pesanan berhasil dibuat, silahkan menunggu diproses"];
    }

    public function index()
    {
        $notas = Nota::all();
        return view('notas.index', compact('notas'));
    }

    public function indexPesanan()
    {
        if(!Auth::user()){
            return redirect('login');
        }

        $notas = DB::table('notas')
            ->join('pemesanans', 'notas.id', '=', 'pemesanans.notas_id')
            ->where('penggunas_email', '=', Auth::user()->email)
            ->select('notas.id as idnota', 'notas.waktu_transaksi as waktu_transaksi', 'pemesanans.vendors_id as idvendor', 'notas.waktu_menerima_pesanan', 'notas.waktu_diantar', 'notas.waktu_tunggu_diambil', 'notas.waktu_selesai', DB::raw('COUNT(pemesanans.id) as jumlah_pesanan'))
            ->groupBy('notas.id', 'notas.waktu_transaksi', 'pemesanans.vendors_id', 'notas.waktu_menerima_pesanan', 'notas.waktu_diantar', 'notas.waktu_tunggu_diambil', 'notas.waktu_selesai')
            ->orderBy('notas.waktu_transaksi', 'desc')
            ->get();

        foreach ($notas as $n) {

            $n->status = $this->getStatus($n);


            $vendor = DB::table('vendors')
                ->where('id', $n->idvendor)
                ->first();
            $n->nama_vendor = $vendor->nama;
            $n->foto_lokasi = $vendor->foto_lokasi;

            $liststatus = DB::table('notas_progress')
                ->where('notas_id', '=', $n->idnota)
                ->select('progress')
                ->get();
        }

        return view('pesanan.vendors', compact('notas'));
    }



    public function formatDateTime($datetime)
    {
        $dateTime = new DateTime($datetime);
        $formattedDate = $dateTime->format('d F, Y H:i');

        return $formattedDate;
    }
    public function formatDate($datetime)
    {
        $dateTime = new DateTime($datetime);
        $formattedDate = $dateTime->format('d F, Y');

        return $formattedDate;
    }

    public function showDetailPesanan($idnota)
    {


        $pesanan_verifikasi = DB::table('pemesanans')
            ->where('notas_id', '=', $idnota)
            ->where('perlu_verifikasi','=',1)
            ->get();

        $arrSummary = [];
        $arrProgress = [];
        $maxPrioritas = 0;
        $totalproses = 0;

        $nota = DB::table('notas')
            ->join('pemesanans', 'notas.id', '=', 'pemesanans.notas_id')
            ->where('notas.id', '=', $idnota)
            ->select('notas.*', 'pemesanans.vendors_id')
            ->first();

        $status_antar = $nota->opsi_pengambilan;

        $notas_time = null;
        
        if($status_antar == 'diambil'){
            $notas_time = DB::table('pemesanans')
            ->join('notas', 'notas.id', '=', 'pemesanans.notas_id')
            ->where('pemesanans.vendors_id', '=', $nota->vendors_id)
            ->where('notas.waktu_tunggu_diambil', '!=', null)
            ->where('notas.opsi_pengambilan', '=', 'diambil')
            ->select('notas.waktu_transaksi', 'notas.waktu_tunggu_diambil as waktu_selesai')
            ->get();
            //jika diambil, maka hanya dihitung waktu pengerjaan (hingga tunggu diambil)
        }
        else{
            $notas_time = DB::table('pemesanans')
            ->join('notas', 'notas.id', '=', 'pemesanans.notas_id')
            ->where('pemesanans.vendors_id', '=', $nota->vendors_id)
            ->where('notas.waktu_selesai', '!=', null)
            ->where('notas.opsi_pengambilan', '=', 'diantar')
            ->select('notas.waktu_transaksi', 'notas.waktu_selesai')
            ->get();
            //jika diantar, maka dihitung waktu pengerjaan dan waktu pengantaran (hingga selesai)
        }

        

        $prediksi_selesai = null;

        if (count($notas_time)>0) {
            $totaldiff = 0;
            $count = 0;

            foreach ($notas_time as $n) {
                if ($n->waktu_transaksi && $n->waktu_selesai) {
                    $waktutransaksi = Carbon::parse($n->waktu_transaksi);
                    $waktuselesai = Carbon::parse($n->waktu_selesai);

                    $diff = $waktutransaksi->diff($waktuselesai);

                    $totaldiff += $diff->h;
                    $count++;
                }
            }
            $avgdiff = round($totaldiff/$count);
            $waktutransaksinota = Carbon::parse($nota->waktu_transaksi);
            $waktuprediksi = $waktutransaksinota->addHours($avgdiff);
            $datetimeprediksi = new DateTime($waktuprediksi->toDateTimeString());
            $prediksi_selesai = $datetimeprediksi->format('d F, Y');
        }

        

        $transaksi = [
            'waktu_progress_format' => $this->formatDateTime($nota->waktu_transaksi),
            'progress' => 'Transaksi berhasil'
        ];
        $totalproses += 1;
        array_push($arrSummary, $transaksi);

        $terima = [
            'waktu_progress_format' => $this->formatDateTime($nota->waktu_menerima_pesanan),
            'progress' => 'Pesanan diterima'
        ];
        $totalproses += 1;
        array_push($arrSummary, $terima);

        $proses = [
            'waktu_progress_format' => $this->formatDateTime($nota->waktu_menerima_pesanan),
            'progress' => 'Pesanan diproses'
        ];
        $totalproses += 1;
        array_push($arrSummary, $proses);

        $notas_progress = [];
        foreach($pesanan_verifikasi as $pv){

            $notas_progress_pesanan = DB::table('notas_progress')
            ->where('pemesanans_id', '=', $pv->id)
            ->orderBy('waktu_progress', 'desc')
            ->first();
            array_push($notas_progress,$notas_progress_pesanan);
        }

        $jumlah_selesai = 0;
        
        $waktu_progress = "";
        foreach ($notas_progress as $key => $np) {
   
         if ($np->progress == 'menunggu verifikasi') {
                $array = [
                    'waktu_progress_format' => $this->formatDateTime($np->waktu_progress),
                    'progress' => 'Menunggu verifikasi',
                    'pemesanans_id' => $np->pemesanans_id,
                    'notas_id' => $np->notas_id,
                    'urutan_progress' => $np->urutan_progress,
                ];
                array_push($arrProgress, $array);
            } else if ($np->progress == 'memperbaiki') {
                $array = [
                    'waktu_progress_format' => $this->formatDateTime($np->waktu_progress),
                    'progress' => 'Memperbaiki cetakan',
                ];
                array_push($arrProgress, $array);
            } else if ($np->progress == 'terverifikasi') {
                $array = [
                    'waktu_progress_format' => $this->formatDateTime($np->waktu_progress),
                    'progress' => 'Verifikasi Pesanan Selesai',
                ];
                array_push($arrProgress, $array);
                $jumlah_selesai++;
            }
            $waktu_progress = $np->waktu_progress;
            // dd($arrProgress);
        }

        if ($jumlah_selesai == count($pesanan_verifikasi)) {
            $verifikasi_selesai = [
                'waktu_progress_format' => $this->formatDateTime($waktu_progress),
                'progress' => 'Pesanan selesai diverifikasi'
            ];
            array_push($arrSummary, $verifikasi_selesai);
            $proses = [
                'waktu_progress_format' => $this->formatDateTime($waktu_progress),
                'progress' => 'Pesanan sedang diselesaikan sesuai verifikasi'
            ];
            array_push($arrSummary, $proses);
            $arrProgress = null;
            $arrProgressReverse = [];
            $array = [];
        } else {
            $arrProgressReverse = [];

            for ($i = count($arrProgress) - 1; $i >= 0; $i--) {
                array_push($arrProgressReverse, $arrProgress[$i]);
            }
        }
        $arrSummaryReverse = [];

        if ($nota->waktu_tunggu_diambil || $nota->waktu_diantar) {
            if ($status_antar == 'diambil') {
                $array = [
                    'waktu_progress_format' => $this->formatDateTime($nota->waktu_tunggu_diambil),
                    'progress' => 'Menunggu diambil'
                ];
            } else if ($status_antar == 'diantar') {
                $array = [
                    'waktu_progress_format' => $this->formatDateTime($nota->waktu_diantar),
                    'progress' => 'Pesanan sedang diantar'
                ];
            }
            $totalproses += 1;
            array_push($arrSummary, $array);
        }


        if ($nota->waktu_selesai) {
            $selesai = [
                'waktu_progress_format' => $this->formatDateTime($nota->waktu_selesai),
                'tanggal_selesai' => $this->formatDate($nota->waktu_selesai),
                'progress' => 'Pesanan sudah selesai'
            ];
            array_push($arrSummary, $selesai);
            $totalproses += 1;
        }

        for ($i = count($arrSummary) - 1; $i >= 0; $i--) {
            array_push($arrSummaryReverse, $arrSummary[$i]);
        }

        $harga_total = $nota->harga_total;

        $jumlah_pesanan = DB::table('pemesanans')
            ->where('notas_id', '=', $nota->id)
            ->count();


        $waktustart = $arrSummary[0]["waktu_progress_format"];

        $ratingcount = DB::table('ratings')
        ->where('notas_id', '=', $nota->id)
        ->count();

        $israted = false;
        if($ratingcount > 0){
            $israted = true;
        }
        // dd($arrProgressReverse);
        return view('pesanan.orderinfo', compact('arrProgressReverse', 'arrSummaryReverse', 'harga_total', 'jumlah_pesanan', 'waktustart', 'prediksi_selesai', 'status_antar', 'nota', 'israted', 'totalproses'));
    }

    public function bukaverifikasi($idpemesanan, $idnota, $urutanprogress)
    {
        $notaProgress = DB::table('notas_progress')
            ->where('pemesanans_id', '=', $idpemesanan)
            ->where('notas_id', '=', $idnota)
            ->where('urutan_progress', '=', $urutanprogress)
            ->first();

        // dd($notaProgress);
        $pemesanan = DB::table('pemesanans')
            ->where('id', '=', $idpemesanan)
            ->first();


        return view('pesanan.verifikasi', compact('notaProgress', 'pemesanan'));
    }

    public function verifikasipesanan(Request $request)
    {

        $notas_progress_latest = DB::table('notas_progress')
            ->where('pemesanans_id', '=', $request->idpemesanan)
            ->where('notas_id', '=', $request->idnota)
            ->where('progress', '!=', 'terverifikasi')
            ->orderBy('urutan_progress', 'desc')
            ->select('urutan_progress')
            ->first();

        // dd($notas_progress_latest);



        $latest_progress = $notas_progress_latest->urutan_progress + 1;

        $existing_progress = DB::table('notas_progress')
            ->where('pemesanans_id', '=', $request->idpemesanan)
            ->where('notas_id', '=', $request->idnota)
            ->where('urutan_progress', '=', $latest_progress)
            ->exists();

        if ($existing_progress) {
            return redirect()->to('/pesanan/'. $request->idnota);
        }

        $nota_progress = new NotaProgress();
        $nota_progress->pemesanans_id = $request->idpemesanan;
        $nota_progress->notas_id = $request->idnota;
        $nota_progress->urutan_progress = $latest_progress;
        $nota_progress->waktu_progress = Carbon::now('Asia/Jakarta');
        $nota_progress->progress = 'terverifikasi';

        $nota_progress->save();

        return redirect()->to('/pesanan/'. $request->idnota);
    }
    public function ajukanperubahan(Request $request)
    {
        Lang::setLocale('id');

        $validator = Validator::make($request->all(), [
            'perubahan' => 'required|string|min:1|max:250'
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) 
                ->withInput(); 
        }

        $notas_progress_latest = DB::table('notas_progress')
            ->where('pemesanans_id', '=', $request->idpemesanan)
            ->where('notas_id', '=', $request->idnota)
            ->where('progress', '!=', 'memperbaiki')
            ->orderBy('urutan_progress', 'desc')
            ->select('urutan_progress')
            ->first();

        // dd($notas_progress_latest);

        $latest_progress = $notas_progress_latest->urutan_progress + 1;

        $existing_progress = DB::table('notas_progress')
            ->where('pemesanans_id', '=', $request->idpemesanan)
            ->where('notas_id', '=', $request->idnota)
            ->where('urutan_progress', '=', $latest_progress)
            ->exists();

        if ($existing_progress) {
            return redirect()->back()->with('error', 'Progress sudah ditangani');
        }

        $nota_progress = new NotaProgress();
        $nota_progress->pemesanans_id = $request->idpemesanan;
        $nota_progress->notas_id = $request->idnota;
        $nota_progress->urutan_progress = $latest_progress;
        $nota_progress->waktu_progress = Carbon::now('Asia/Jakarta');
        $nota_progress->perubahan = $request->perubahan;
        $nota_progress->progress = 'memperbaiki';

        $nota_progress->save();

        return redirect()->route('detailPesanan', $request->idnota);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'waktu_transaksi' => 'required',
            'opsi_pengambilan' => 'required',
            'alamat_pengambilan' => 'required',
            'tanggal_selesai' => 'required',
            'ulasan' => 'required',
        ]);

        Nota::create($request->all());

        return redirect()->route('notas.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('notas.show', compact('nota'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('notas.edit', compact('nota'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Nota $nota)
    {
        $request->validate([
            'waktu_transaksi' => 'required',
            'opsi_pengambilan' => 'required',
            'alamat_pengambilan' => 'required',
            'tanggal_selesai' => 'required',
            'ulasan' => 'required',
        ]);

        $nota->update($request->all());

        return redirect()->route('notas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Nota $nota)
    {
        $nota->delete();
        return redirect()->route('notas.index');
    }
}
