<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
                    'vendor_rating' => 3, //data netral u/ memberi peluang vendor baru
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
                    'vendor_rating' => 3, //data netral u/ memberi peluang vendor baru
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
