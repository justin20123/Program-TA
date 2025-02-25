<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Vendor;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()) {
            return redirect()->route('login');
        }
        
        if (Auth::user()->role == "admin") {
            $vendors = DB::table('vendors')
                ->where('status', '=', 'active')
                ->get();
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();
            $rating_rendah = 0;
            foreach ($vendors as $key => $v) {
                $ratings = DB::table('notas')
                    ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
                    ->join('ratings', 'ratings.notas_id', '=', 'notas.id')
                    ->where('pemesanans.vendors_id', '=', $v->id)
                    ->whereNotNull('ratings.nilai')
                    ->whereBetween('notas.created_at', [$startOfWeek, $endOfWeek]) // Filter by current week
                    ->select('notas.id', DB::raw('avg(ratings.nilai) as average_rating'))
                    ->groupBy('notas.id')
                    ->get();

                if ($ratings->isNotEmpty()) {
                    $totalRating = 0;
                    $totalNota = DB::table('notas')
                        ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
                        ->where('pemesanans.vendors_id', '=', $v->id)
                        ->whereBetween('notas.created_at', [$startOfWeek, $endOfWeek]) // Filter by current week
                        ->distinct('notas.id') // Ensure distinct count
                        ->count('notas.id');

                    foreach ($ratings as $r) {
                        if($r->average_rating < 3.0){
                            $rating_rendah++;
                        }
                        $totalRating += $r->average_rating;
                    }

                    $vendor_rating = $totalRating / $totalNota;
                    $v->jumlah_pesanan = $totalNota;
                    $v->rating = $vendor_rating;
                } else {
                    $v->jumlah_pesanan = 0;
                    $v->rating = 0;
                }
                
            }
            $jumlah_vendor = count($vendors);
            // dd($jumlah_vendor);
            return view('home', compact('vendors', 'rating_rendah', 'jumlah_vendor'));
            Auth::logout();
            return redirect()->route('login')->with('error', 'Anda bukan manajer maupun pegawai, silahkan login menggunakan akun yang bersangkutan.');
        } else {
            Auth::logout();
            return redirect()->route('login');
        }


        return view('home', compact('vendors'));
    }

    public function bukalepasblokir(){
        $vendors = DB::table('vendors')
        ->where('status', '=', 'diblokir')
        ->get();
        foreach($vendors as $v){
            $manajer = DB::table('penggunas')
            ->where('vendors_id', '=', $v->id)
            ->where('role', '=', 'manajer')
            ->first();
            $v->nama = $manajer->nama;
            $v->email = $manajer->email;
            $v->tanggal_diblokir = date('d/m/Y', strtotime($v->updated_at));
            
        }
        // dd($vendors);
        return view('lepasblokir', compact('vendors'));
    }
    public function bukaverifikasi(){
        $vendors = DB::table('vendors')
        ->where('status', '=', 'menunggu verifikasi')
        ->get();
        foreach($vendors as $v){
            $manajer = DB::table('penggunas')
            ->where('vendors_id', '=', $v->id)
            ->where('role', '=', 'manajer')
            ->first();
            $v->nama = $manajer->nama;
            $v->email = $manajer->email;
            $v->tanggal_daftar = date('d/m/Y', strtotime($v->created_at));
            
        }
        // dd($vendors);
        return view('verifikasi', compact('vendors'));
    }
    public function blokir(Request $request)
    {
        $idvendor = $request->input('idvendor');
        $vendor = Vendor::findOrFail($idvendor);
        $vendor->status = "diblokir";
        $vendor->updated_at =  Carbon::now('Asia/Jakarta');;
        $vendor->save();
        return redirect()->route('home')->with('success', 'Vendor berhasil diblokir');
    }

    public function aktifkan(Request $request)
    {
        $idvendor = $request->input('idvendor');
        $vendor = Vendor::findOrFail($idvendor);
        $vendor->status = "active";
        $vendor->save();
        return redirect()->route('home')->with('success', 'Vendor berhasil diblokir');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('vendor.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max: 50',
                'fotopercetakan' => 'file|mimes:jpeg,jpg,png,gif,|max:20480',
                'longitude' => 'required',
                'latitude' => 'required',
            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambah vendor. ' . $e->getMessage());
        }

        $idvendor = $request->input('vendorid');

        $vendor = Vendor::findOrFail($idvendor);
        $vendor->nama = $request->input('nama');
        if ($request->file('fotopercetakan')) {
            $file = $request->file('fotopercetakan');
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = "$idvendor.$fileExtension";

            $directory = base_path('../vendors');
            $file->move($directory, $fileName);
            Cache::flush();
            Artisan::call('view:clear');
        }
        $vendor->longitude = $request->input('longitude');
        $vendor->latitude = $request->input('latitude');
        $vendor->save();

        return redirect()->route('home');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index');
    }
}
