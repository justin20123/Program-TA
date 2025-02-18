<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LayananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRating($vendors_id, $layanans_id)
    {
        $ratings = DB::table('ratings')
            ->join('notas', 'ratings.notas_id', '=', 'notas.id')
            ->join('pemesanans', 'notas.id', '=', 'pemesanans.notas_id')
            ->join('jenis_bahan_cetaks', 'pemesanans.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->where('pemesanans.vendors_id', '=', $vendors_id)
            ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $layanans_id)
            ->whereNotNull('ratings.nilai')
            ->avg('ratings.nilai');
        return $ratings;
    }

    public function formatDate($datetime)
    {
        $dateTime = new DateTime($datetime);
        $formattedDate = $dateTime->format('d F, Y');

        return $formattedDate;
    }
    //ambil avg rating setiap layanan dalam 1 vendor
    public function getTotalNota($vendors_id, $layanans_id)
    {
        $total_nota = DB::table('notas')
            ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
            ->join('jenis_bahan_cetaks', 'pemesanans.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->where('pemesanans.vendors_id', '=', $vendors_id)
            ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $layanans_id)
            ->count();
        return $total_nota;
    }
    //ambil totap nota setiap layanan dalam 1 vendor

    public function index($vendor_id)
    {
        $layanans = DB::table('layanan_cetaks')
            ->join('vendors_has_jenis_bahan_cetaks', 'layanan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id')
            ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
            ->select('layanan_cetaks.*')->distinct()
            ->get();

        $hargacetakcontroller = new HargacetakController();
        foreach ($layanans as $l) {
            $l->hargamin = $hargacetakcontroller->getMinValue($vendor_id, $l->id);
            $l->hargamax = $hargacetakcontroller->getMaxValue($vendor_id, $l->id);
        }
        $vendor = DB::table('vendors')
            ->where('vendors.id', '=', $vendor_id)
            ->first();

        return view('vendors.layanan', compact('layanans', 'vendor'));
    }

    public function getDetailLayanan($vendor_id, $idlayanan)
    {
        $jenisbahan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->join('jenis_bahan_cetaks', 'jenis_bahan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id')
            ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
            ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $idlayanan)
            ->select('jenis_bahan_cetaks.*', 'vendors_has_jenis_bahan_cetaks.vendors_id as idvendor')
            ->where('jenis_bahan_cetaks.deleted_at', '=', null)
            ->get();
        $jenisbahanfiltered = array();

        foreach ($jenisbahan as $jb) {
            $jumlahhargacetaks = DB::table('harga_cetaks')
                ->where('id_bahan_cetaks', $jb->id)
                ->count();

            if ($jumlahhargacetaks > 0) {
                array_push($jenisbahanfiltered, $jb);
            }
        }
        $jenisbahan = $jenisbahanfiltered;

        $detailcetaks = DB::table('detail_cetaks')
            ->join('jenis_bahan_cetaks', 'detail_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
            ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $idlayanan)
            ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $jenisbahan[0]->id)
            ->select('detail_cetaks.*')
            ->get();

        // dd($detailcetaks);

        $opsidetail = [];
        foreach ($detailcetaks as $detail) {
            $opsidetail[$detail->id] = [
                'detail' => $detail,
                'opsi' => [],
            ];

            $opsi = DB::table('opsi_details')
                ->where('detail_cetaks_id', '=', $detail->id)
                ->get();

            // dd($detail->id);

            $arr = array();

            foreach ($opsi as $o) {
                $arr = [
                    'id' => $o->id,
                    'opsi' => $o->opsi,
                    'biaya_tambahan' => $o->biaya_tambahan,
                ];
            }
            $opsidetail[$detail->id]['opsi'][] = $arr;
        }

        $opsidetail = array_values($opsidetail);

        $hargacetaks = DB::table('harga_cetaks')
            ->where('id_bahan_cetaks', '=', $jenisbahan[0]->id)
            ->get();

        $layanan = DB::table('layanan_cetaks')
            ->where('id', '=', $idlayanan)
            ->first();

        $review = [];


        $vendor_layanan_cetak = DB::table('vendors_has_jenis_bahan_cetaks')
            ->where('layanan_cetaks_id', '=', $idlayanan)
            ->where('vendors_id', '=', $vendor_id)
            ->select('jenis_bahan_cetaks_id')
            ->get();

        $idvendorlayanancetak = [];
        foreach ($vendor_layanan_cetak as $vl) {
            array_push($idvendorlayanancetak, $vl->jenis_bahan_cetaks_id);
        }


        $pemesanans = DB::table('pemesanans')
            ->whereIn('jenis_bahan_cetaks_id', $idvendorlayanancetak)
            ->select('notas_id')
            ->get();

        $idnotas = [];
        foreach ($pemesanans as $p) {
            $currentidnota = $idnotas;
            $isinserted = false;
            foreach ($currentidnota as $id) {
                if ($id == $p->notas_id) {
                    $isinserted = true;
                }
            }
            if (!$isinserted) {
                array_push($idnotas, $p->notas_id);
            }
        }
        $avgrating = 0;


        if (count($pemesanans) > 0) {
            $totalrating = 0;
            $notas = DB::table('notas')
                ->leftJoin('ratings', 'notas.id', '=', 'ratings.notas_id')
                ->whereIn('notas.id', $idnotas)
                ->where('notas.waktu_selesai', '!=', null)
                ->where('notas.ulasan', '!=', "")
                ->select('notas.id', 'notas.ulasan', 'notas.waktu_selesai')
                ->distinct()
                ->get();

            if (count($notas) > 0) {
                foreach ($notas as $n) {

                    $rating = DB::table('ratings')
                        ->where('notas_id', '=', $n->id)
                        ->average('nilai');
                    $n->rating = $rating;
                    $totalrating += $rating;
                    $pemesanan = DB::table('pemesanans')
                        ->where('notas_id', $n->id)
                        ->select('penggunas_email')
                        ->first();

                    $pemesan = DB::table('penggunas')
                        ->where('email', '=', $pemesanan->penggunas_email)
                        ->select('nama')
                        ->first();

                    $n->pemesan = $pemesan->nama;

                    $n->waktu_selesai_formatted = $this->formatDate($n->waktu_selesai);
                }
                $review = $notas;

                $avgrating = $totalrating / count($notas);
            }
        }

        // dd($review);
        // dd($hargacetaks);
        // dd($opsidetail);

        return view('vendors.detaillayanan', compact('jenisbahan', 'opsidetail', 'hargacetaks', 'layanan', 'review', 'avgrating'));
    }

    public function detail_layanan_load($vendor_id, $idlayanan, $idjenisbahan)
    {
        $detailcetaks = DB::table('detail_cetaks')
            ->join('jenis_bahan_cetaks', 'detail_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->leftJoin('opsi_details', 'detail_cetaks.id', '=', 'opsi_details.detail_cetaks_id')
            ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
            ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $idlayanan)
            ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $idjenisbahan)
            ->select('detail_cetaks.*', 'opsi_details.id as idopsi', 'opsi_details.opsi as opsi', 'opsi_details.biaya_tambahan as biaya_tambahan')
            ->get();

        $opsiDetail = [];
        foreach ($detailcetaks as $detail) {
            if (!isset($opsiDetail[$detail->id])) {
                $opsiDetail[$detail->id] = [
                    'detail' => $detail,
                    'opsi' => [],
                ];
            }
            if ($detail->idopsi) {
                $opsiDetail[$detail->id]['opsi'][] = [
                    'id' => $detail->idopsi,
                    'opsi' => $detail->opsi,
                    'biaya_tambahan' => $detail->biaya_tambahan,
                ];
            }
        }

        $opsiDetail = array_values($opsiDetail);

        $listharga = DB::table('harga_cetaks')
            ->where('id_bahan_cetaks', '=', $idjenisbahan)
            ->get();

        $layanan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->join('layanan_cetaks', 'layanan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id')
            ->where('jenis_bahan_cetaks_id', '=', $idjenisbahan)
            ->select('layanan_cetaks.satuan')
            ->first();

        foreach ($listharga as $l) {
            $l->satuan = $layanan->satuan;
        }

        $jenisbahan = DB::table('jenis_bahan_cetaks')
            ->where('id', '=', $idjenisbahan)
            ->select('deskripsi','gambar')
            ->first();
        $deskripsi = $jenisbahan->deskripsi;
        $gambar = $jenisbahan->gambar;

        return json_encode(['result' => 'success', 'data' => ['opsidetail' => $opsiDetail, 'listharga' => $listharga, 'deskripsi' => $deskripsi, 'gambar' => $gambar]]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('layanans.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vendorid = $request->input('idvendor');
        $layananid = $request->input('idlayanan');

        $layanan = new Layanan();
        $layanan->nama = $request->input('nama');
        if ($request->input('deskripsi')) {
            $layanan->deskripsi = $request->input('deskripsi');
        }
        $layanan->biaya_tambahan = $request->input('biaya_tambahan');
        $layanan->save();
        return redirect()->route('layanan.detail_layanan', [$vendorid, $layananid]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Layanan  $layanan
     * @return \Illuminate\Http\Response
     */
    public function show(Layanan $layanan)
    {
        return view('layanans.show', compact('layanan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Layanan  $layanan
     * @return \Illuminate\Http\Response
     */
    public function edit(Layanan $layanan)
    {
        return view('layanans.edit', compact('layanan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Layanan  $layanan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Layanan $layanan) {}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Layanan  $layanan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Layanan $layanan)
    {
        $layanan->delete();
        return redirect()->route('layanans.index');
    }
}
