<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemesananController extends Controller
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


    public function index($vendor_id)
    {
        $notaData = DB::table('notas')
            ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
            ->join('penggunas', 'penggunas.email', '=', 'pemesanans.penggunas_email')
            ->where('pemesanans.vendors_id', '=', $vendor_id)
            ->select('notas.*', 'pemesanans.id as pemesanans_id', 'penggunas.nama as nama')
            ->get();

        $notaDetail = [];
        foreach ($notaData as $n) {
            $n->status = $this->getStatus($n);


            $pemesanan = DB::table('pemesanans')
                ->join('jenis_bahan_cetaks', 'jenis_bahan_cetaks.id', '=', 'pemesanans.jenis_bahan_cetaks_id')
                ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
                ->join('layanan_cetaks', 'layanan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id')
                ->where('pemesanans.id', '=', $n->pemesanans_id)
                ->select('pemesanans.*', 'layanan_cetaks.satuan as satuan', 'layanan_cetaks.nama as layanan')
                ->first();
            // Check if the nota already exists in the detail array
            if (!isset($notaDetail[$n->id])) {
                $notaDetail[$n->id] = [
                    'nota' => $n,
                    'pemesanans' => [],
                ];
            }
            array_push($notaDetail[$n->id]['pemesanans'], $pemesanan);
        }

        $notaDetail = array_values($notaDetail);

        // dd($notaDetail);

        return view('pesanan.orders', compact('notaDetail'));
    }

    public function detailPesanan($vendor_id, $idnota)
    {
        $notaData = DB::table('notas')
            ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
            ->join('penggunas', 'penggunas.email', '=', 'pemesanans.penggunas_email')
            ->where('pemesanans.vendors_id', '=', $vendor_id)
            ->where('notas.id', '=', $idnota)
            ->select('notas.*', 'pemesanans.id as idpemesanans', 'penggunas.nama as nama')
            ->first();

        $notaDetail = [];

        $pemesanan = DB::table('pemesanans')
            ->join('jenis_bahan_cetaks', 'jenis_bahan_cetaks.id', '=', 'pemesanans.jenis_bahan_cetaks_id')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->join('layanan_cetaks', 'layanan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id')
            ->where('pemesanans.id', '=', $notaData->idpemesanans)
            ->select('pemesanans.*', 'layanan_cetaks.satuan as satuan', 'layanan_cetaks.nama as layanan')
            ->first();

        $harga_cetak = DB::table('harga_cetaks')
            ->where('id', '=', $pemesanan->harga_cetaks_id)
            ->select('harga_satuan')
            ->first();
        $pemesanan->harga_satuan = $harga_cetak->harga_satuan;
        if (!isset($notaDetail[$notaData->id])) {
            $notaDetail[$notaData->id] = [
                'nota' => $notaData,
                'pemesanans' => [],
            ];
        }
        array_push($notaDetail[$notaData->id]['pemesanans'], $pemesanan);

        $notaDetail = array_values($notaDetail);
        // dd($notaDetail);

        return view('pesanan.orderdetail', compact('notaDetail'));
    }

    public function pilihpengantar($idvendor, $idnota)
    {
        $notaData = DB::table('notas')
            ->where('id', '=', $idnota)
            ->select()
            ->first();

        $pengantar = DB::table('vendors_has_penggunas')
            ->join('penggunas', 'penggunas.email', '=', 'vendors_has_penggunas.penggunas_email')
            ->where('vendors_has_penggunas.vendors_id', '=', $idvendor)
            ->where('penggunas.role', '=', 'pengantar')
            ->select('penggunas.nama as namapengantar', 'penggunas.email as email')
            ->get();

        $data_pemesan = DB::table('pemesanans')
            ->join('penggunas', 'penggunas.email', '=', 'pemesanans.penggunas_email')
            ->where('pemesanans.notas_id', '=', $notaData->id)
            ->select('penggunas.nama', 'penggunas.email')
            ->first();
        $notaData->namaPemesan = $data_pemesan->nama;

        // dd($notaData);
        // dd($pengantar);

        return view('pesanan.pilihpengantar', compact('notaData', 'pengantar'));
    }

    public function antarkan($emailpengantar, $idnota)
    {
        $nota = Nota::findOrFail($idnota);
        $nota->waktu_diantar = now();
        $nota->save();

        $pemesanans = DB::table('pemesanans')
            ->where('notas_id', '=', $idnota)
            ->select('id', 'vendors_id')
            ->get();

        // foreach($pemesanans as $p){
        //     $pesanan = Pemesanan::findOrFail($p->id);

        // }
        // $pemesanan->pengantar = $emailpengantar;
        // $pemesanan->save();

        $url = '/pesanancetak/' . $pemesanans[0]->vendors_id;
        return redirect($url);
        // return redirect()->route('pemesanans.index', [$pemesanans[0]->vendors_id]);
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
            'jumlah' => 'required',
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
            'jumlah' => 'required',
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
