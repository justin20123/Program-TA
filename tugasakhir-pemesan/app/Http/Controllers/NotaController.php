<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\NotaProgress;
use App\Models\Pemesanan;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        } else if ($nota->waktu_diantar || $nota->waktu_tunggu_diambil) {
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

        // return response()->json(['message' => 'idpemesanans : '. $idpemesanans], 400);
        if ($request->opsiantar == "diambil") {
            $request->validate([
                'idpemesanans' => 'required',
            ]);

            $idnota = DB::table('notas')->insertGetid([
                'harga_total' => $request->harga_total,
                'waktu_transaksi' => now(),
                'opsi_pengambilan' => "diambil",
                'waktu_menerima_pesanan' => now(),
                'waktu_diantar' => null,
                'waktu_tunggu_diambil' => null,
                'waktu_selesai' => null,
                'ulasan' => "",
                'catatan_antar' => $request->catatan_antar,
            ]);
        } else {
            $request->validate([
                'idpemesanans' => 'required',
                'latitude' => 'required|',
                'longitude' => 'required|'
            ]);

            $idnota = DB::table('notas')->insertGetid([
                'harga_total' => $request->harga_total,
                'waktu_transaksi' => now(),
                'opsi_pengambilan' => "diantar",
                'waktu_menerima_pesanan' => now(),
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
                'waktu_progress' => now(),
                'progress' => "proses",
            ]);
        }

        return ["idnota" => $idnota, "message" => "Pesanan berhasil dibuat, silahkan menunggu diproses"];
    }

    public function index()
    {
        $notas = Nota::all();
        return view('notas.index', compact('notas'));
    }

    public function indexPesanan()
    {

        $notas = DB::table('notas')
            ->join('pemesanans', 'notas.id', '=', 'pemesanans.notas_id')
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

    public function showDetailPesanan($idnota)
    {


        $jumlah_pesanan = DB::table('pemesanans')
            ->where('notas_id', '=', $idnota)
            ->count();

        $arrSummary = [];
        $arrProgress = [];
        $maxPrioritas = 0;

        $notas = DB::table('notas')
            ->where('id', '=', $idnota)
            ->first();

        $status_antar = $notas->opsi_pengambilan;

        $transaksi = [
            'waktu_progress_format' => $this->formatDateTime($notas->waktu_transaksi),
            'progress' => 'Transaksi berhasil'
        ];

        array_push($arrSummary, $transaksi);

        $terima = [
            'waktu_progress_format' => $this->formatDateTime($notas->waktu_menerima_pesanan),
            'progress' => 'Pesanan diterima'
        ];

        array_push($arrSummary, $terima);

        $proses = [
            'waktu_progress_format' => $this->formatDateTime($notas->waktu_menerima_pesanan),
            'progress' => 'Pesanan diproses'
        ];

        array_push($arrSummary, $proses);

        $notas_progress = DB::table('notas_progress')
            ->where('notas_id', '=', $idnota)
            ->orderBy('waktu_progress')
            ->get();

        $jumlah_selesai = 0;
        foreach ($notas_progress as $key => $np) {
            if ($key == 0) {
                continue;
            } else if ($np->progress == 'menunggu verifikasi') {
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
        }
        if ($jumlah_selesai == $jumlah_pesanan) {
            $verifikasi_selesai = [
                'waktu_progress_format' => $this->formatDateTime($np->waktu_progress),
                'progress' => 'Pesanan selesai diverifikasi'
            ];
            array_push($arrSummary, $verifikasi_selesai);
            $proses = [
                'waktu_progress_format' => $this->formatDateTime($np->waktu_progress),
                'progress' => 'Pesanan sedang diselesaikan sesuai verifikasi'
            ];
            array_push($arrSummary, $proses);
            $arrProgress = null;
            $arrProgressReverse = [];
            $array = [];
            if ($notas->waktu_tunggu_diambil || $notas->waktu_diantar) {
                if ($status_antar == 'diambil') {
                    $array = [
                        'waktu_progress_format' => $this->formatDateTime($notas->waktu_tunggu_diambil),
                        'progress' => 'Menunggu diambil'
                    ];
                } else if ($status_antar == 'diantar') {
                    $array = [
                        'waktu_progress_format' => $this->formatDateTime($notas->waktu_diantar),
                        'progress' => 'Pesanan sedang diantar'
                    ];
                }
                array_push($arrSummary, $array);
            }


            if ($notas->waktu_selesai) {
                $selesai = [
                    'waktu_progress_format' => $this->formatDateTime($notas->waktu_selesai),
                    'progress' => 'Pesanan sudah selesai'
                ];
                array_push($arrSummary, $selesai);
            }
        } else {
            $arrProgressReverse = [];

            for ($i = count($arrProgress) - 1; $i >= 0; $i--) {
                array_push($arrProgressReverse, $arrProgress[$i]);
            }
        }
        $arrSummaryReverse = [];

        for ($i = count($arrSummary) - 1; $i >= 0; $i--) {
            array_push($arrSummaryReverse, $arrSummary[$i]);
        }

        return view('pesanan.orderinfo', compact('arrProgressReverse', 'arrSummaryReverse'));
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
            return response()->json(['message' => 'Progress sudah ditangani'], 409); // Conflict status
        }

        $nota_progress = new NotaProgress();
        $nota_progress->pemesanans_id = $request->idpemesanan;
        $nota_progress->notas_id = $request->idnota;
        $nota_progress->urutan_progress = $latest_progress;
        $nota_progress->waktu_progress = now();
        $nota_progress->progress = 'terverifikasi';

        $nota_progress->save();

        return $this->showDetailPesanan($request->idnota);
    }
    public function ajukanperubahan(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'perubahan' => 'required|string|min:1|max:250'
        ]);
        if ($validator->fails()) {
            // Handle the failure, for example:
            return redirect()->back()
                ->withErrors($validator) // Pass the error messages
                ->withInput(); // Keep the old input
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
        $nota_progress->waktu_progress = now();
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
