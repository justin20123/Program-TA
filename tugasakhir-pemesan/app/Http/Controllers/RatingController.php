<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Rating;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function doreview(Request $request){
        $statusantar = $request->input('statusantar');
        try{
            $komentar = $request->input('komentar');
            if(!$komentar){
                return back()->with('error' , 'Semua rating dan komentar harus diisi');
            }
            if (strlen($komentar) > 200) {
                return back()->with('error', 'Komentar tidak boleh lebih dari 200 karakter');
            }
            if($statusantar == 'diantar'){
                $request->validate([
                    'ratingkualitas' => 'required|integer|min:1|max:5',
                    'ratingpelayanan' => 'required|integer|min:1|max:5',
                    'ratingfasilitas' => 'required|integer|min:1|max:5',
                    'ratingpengantaran' => 'required|integer|min:1|max:5',
                ]);
            }
            else{
                $request->validate([
                    'ratingkualitas' => 'required|integer|min:1|max:5',
                    'ratingpelayanan' => 'required|integer|min:1|max:5',
                    'ratingfasilitas' => 'required|integer|min:1|max:5',
                ]);
            }
            $namaratingarr = ["kualitas", "pelayanan", "fasilitas"];
        if($statusantar == 'diantar'){
            array_push($namaratingarr, "pengantaran");
        }

        foreach($namaratingarr as $n){
            $rating = new Rating();
            $rating->notas_id = $request->input('idnota');
            $rating->nama = $n;
            $rating->nilai = $request->input('rating'. $n);
            $rating->save();
        }
        

        $nota = Nota::findOrFail($request->input('idnota'));
        $nota->ulasan = $request->input('komentar');
        $nota->save();

        return back()->with('message', 'Rating berhasil ditambahkan');
        }
        catch(Exception $e){
            return back()->with('error' , 'Semua rating harus diisi' . $e->getMessage());
        }
        
    }

    public function getRating($idvendor, $data='average'){
        $ratings = 0;
        if($data == 'average'){
            $ratings = DB::table('notas')
            ->join('pemesanans','pemesanans.notas_id','=','notas.id')
            ->join('ratings','ratings.notas_id','=','notas.id')
            ->where('pemesanans.vendors_id','=', $idvendor)
            ->whereNotNull('ratings.nilai')
            ->select('notas.id',
                DB::raw('avg(ratings.nilai) as average_rating'),
            )
            ->groupBy('notas.id')
            ->get();
            if($ratings->isNotEmpty()){
                $totalRating = 0;
                $totalNota = DB::table('notas')
                ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
                ->where('pemesanans.vendors_id', '=', $idvendor)
                ->distinct('notas.id') // Ensure distinct count
                ->count('notas.id');
        
                foreach ($ratings as $r) {
                    $totalRating += $r->average_rating;

                }        
                $vendor_rating = $totalRating / $totalNota;
                return [
                    'vendor_rating' => $vendor_rating,
                    'total_nota' => $totalNota
                ];              
            }
            else{
                return [
                    'vendor_rating' => 0, 
                    'total_nota' => 0
                ];
            }
        }
        else{
            $ratings = DB::table('notas')
            ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
            ->join('ratings', 'ratings.notas_id', '=', 'notas.id')
            ->where('pemesanans.vendors_id', '=', $idvendor)
            ->where('ratings.nama', '=', $data) // Filter by specific ratings.nama
            ->whereNotNull('ratings.nilai')
            ->select('notas.id', 'ratings.nama', DB::raw('AVG(ratings.nilai) as average_rating')) // Select average rating
            ->groupBy('notas.id', 'ratings.nama') // Group by notas.id and ratings.nama
            ->get();
            // dd($ratings);
            if($ratings->isNotEmpty()){
                $totalRating = 0;
                $totalNota = 0;
                foreach ($ratings as $r) {
                    $totalRating += $r->average_rating;
                    $totalNota++;
                }        
                $vendor_rating = $totalRating / $totalNota;
                return [
                    'vendor_rating' => $vendor_rating,
                    'total_nota' => $totalNota
                ];           
            }
            else{
                return [
                    'vendor_rating' => 0, 
                    'total_nota' => 0
                ]; 
            }
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
