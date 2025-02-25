<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
        if ($nota->waktu_selesai) {
            $status_nota = "Pesanan Selesai";
        }
        else if ($nota->waktu_diantar || $nota->waktu_tunggu_diambil) {
            if ($nota->waktu_diantar) {
                $status_nota = "Sedang Diantar";
            } else {
                $status_nota = "Menunggu Diambil";
            }
        }
        elseif ($nota->waktu_menerima_pesanan) {
            $status_nota = "Pesanan diterima";
        } 
        else {
            $status_nota = "Menunggu diterima vendor";
        }
        
        return $status_nota;
    }

    public function index()
    {
        $vendor = DB::table('vendors')
            ->join('penggunas', 'penggunas.vendors_id', '=', 'vendors.id')
            ->where('penggunas.id', '=', Auth::user()->id)
            ->select('vendors.id as id_vendor')
            ->first();

        $vendor_id = $vendor->id_vendor;
            
        $nota_data = DB::table('notas')
            ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
            ->join('penggunas', 'penggunas.email', '=', 'pemesanans.penggunas_email')
            ->where('pemesanans.vendors_id', '=', $vendor_id)
            ->select('notas.*', 'pemesanans.id as pemesanans_id', 'penggunas.nama as nama')
            ->get();

        $nota_detail = [];
        foreach ($nota_data as $n) {
            $n->status = $this->getStatus($n);

            $pemesanan = DB::table('pemesanans')
                ->join('jenis_bahan_cetaks', 'jenis_bahan_cetaks.id', '=', 'pemesanans.jenis_bahan_cetaks_id')
                ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
                ->join('layanan_cetaks', 'layanan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id')
                ->where('pemesanans.id', '=', $n->pemesanans_id)
                ->select('pemesanans.*', 'layanan_cetaks.satuan as satuan', 'layanan_cetaks.nama as layanan')
                ->first();

            if (!isset($nota_detail[$n->id])) {
                $nota_detail[$n->id] = [
                    'nota' => $n,
                    'pemesanans' => [],
                ];
            }
            array_push($nota_detail[$n->id]['pemesanans'], $pemesanan);
        }

        $nota_detail = array_values($nota_detail);

        return view('pesanan.orders', compact('nota_detail'));
    }

    public function detailPesanan($vendor_id, $id_nota)
    {
        $nota_data = DB::table('notas')
            ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
            ->join('penggunas', 'penggunas.email', '=', 'pemesanans.penggunas_email')
            ->where('pemesanans.vendors_id', '=', $vendor_id)
            ->where('notas.id', '=', $id_nota)
            ->select('notas.*', 'pemesanans.id as id_pemesanans', 'penggunas.nama as nama')
            ->first();

        $nota_detail = [];

        $pemesanan = DB::table('pemesanans')
            ->join('jenis_bahan_cetaks', 'jenis_bahan_cetaks.id', '=', 'pemesanans.jenis_bahan_cetaks_id')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->join('layanan_cetaks', 'layanan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id')
            ->where('pemesanans.notas_id', '=', $id_nota)
            ->select('pemesanans.*', 'layanan_cetaks.satuan as satuan', 'layanan_cetaks.nama as layanan')
            ->get();

        $is_verifikasi_selesai = true;
        $is_menunggu_selesai = true; 
        $is_selesai = true;

        if (!$nota_data->waktu_diantar && !$nota_data->waktu_tunggu_diambil) {
            $is_menunggu_selesai = false; 
        }

        if (!$nota_data->waktu_selesai) {
            $is_selesai = false; 
        }

        foreach ($pemesanan as $p) {
            $harga_cetak = DB::table('harga_cetaks')
                ->where('id', '=', $p->harga_cetaks_id) // Assuming harga_cetak_id exists in pemesanan
                ->select('harga_satuan')
                ->first();

            $notas_progress_latest = DB::table('notas_progress')
                ->where('pemesanans_id', '=', $p->id)
                ->where('notas_id', '=', $p->notas_id)
                ->orderBy('urutan_progress', 'desc')
                ->select('progress')
                ->first();
            
            if ($harga_cetak) {
                $p->harga_satuan = $harga_cetak->harga_satuan;
            }

            if ($p->perlu_verifikasi == 1) {
                if ($notas_progress_latest) {
                    $p->latest_progress = $notas_progress_latest->progress;
                    if ($notas_progress_latest->progress != 'terverifikasi') {
                        $is_verifikasi_selesai = false;
                    }
                }
            }

            if (!isset($nota_detail[$nota_data->id])) {
                $nota_detail[$nota_data->id] = [
                    'nota' => $nota_data,
                    'pemesanans' => [],
                ];
            }

            array_push($nota_detail[$nota_data->id]['pemesanans'], $p);
        }

        $nota_detail = array_values($nota_detail);
        
        return view('pesanan.orderdetail', compact('nota_detail', 'is_verifikasi_selesai', 'is_menunggu_selesai', 'is_selesai'));
    }

    public function pilihpengantar(Request $request)
    {
        $nota_data = DB::table('notas')
            ->where('id', '=', $request->idnota)
            ->select()
            ->first();

        $vendor = DB::table('pemesanans')
            ->where('notas_id', '=', $request->idnota)
            ->select('vendors_id')
            ->first();

        $pengantar = DB::table('penggunas')
            ->where('vendors_id', '=', $vendor->vendors_id)
            ->where('role', '=', 'pengantar')
            ->select('nama as namapengantar', 'id')
            ->get();

        $data_pemesan = DB::table('pemesanans')
            ->join('penggunas', 'penggunas.email', '=', 'pemesanans.penggunas_email')
            ->where('pemesanans.notas_id', '=', $request->idnota)
            ->select('penggunas.nama', 'penggunas.email')
            ->first();
        $nota_data->namaPemesan = $data_pemesan->nama;

        return view('pesanan.pilihpengantar', compact('nota_data', 'pengantar'));
    }

    public function tugaskanpengantar(Request $request)
    {
        $nota = Nota::findOrFail($request->idnota);

        
        $nota->waktu_diantar = Carbon::now('Asia/Jakarta');
        $nota->idpengantar = $request->input('idpengantar');
        $nota->save();

        // dd($nota);

        return redirect()->route('pesanan.index');
    }

    public function requestambil(Request $request)
    {
        $nota = Nota::findOrFail($request->idnota);
        $nota->waktu_tunggu_diambil = Carbon::now('Asia/Jakarta');
        $nota->save();

        $_SESSION['message'] = 'Status pesanan saat ini berhasil diubah menjadi menunggu diambil';

        return ['status' => 'done'];
    }
    
    public function selesaikanpesanan(Request $request)
    {
        $nota = Nota::findOrFail($request->idnota);
        $nota->waktu_selesai = Carbon::now('Asia/Jakarta');
        $nota->save();

        $_SESSION['message'] = 'Pesanan berhasil diselesaikan';

        return ['status' => 'done'];
    }

    public function lihatcatatan($id_pemesanan) {
        $pemesanan = Pemesanan::find($id_pemesanan);
        $layanan = DB::table('vendors_has_jenis_bahan_cetaks')
                ->join('layanan_cetaks', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', 'layanan_cetaks.id')
                ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $pemesanan->jenis_bahan_cetaks_id)
                ->select('layanan_cetaks.nama as namalayanan', 'layanan_cetaks.satuan as satuanlayanan')
                ->first();
        return ["jumlah" => $pemesanan->jumlah, "catatan" => $pemesanan->catatan, "layanan" => $layanan->namalayanan, "satuan" => $layanan->satuanlayanan];
    }

    public function create()
    {
        $notas = Nota::all();
        return view('pemesanans.create', compact('notas'));
    }

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

    public function show($id)
    {
        return view('notas.show', compact('nota'));
    }

    public function edit($id)
    {
        return view('notas.edit', compact('nota'));
    }

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

    public function destroy(Pemesanan $pemesanan)
    {
        $pemesanan->delete();
        return redirect()->route('pemesanans.index');
    }
}
