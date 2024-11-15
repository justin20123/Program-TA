<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function getJarak($vendor, $latitude, $longitude){
        $latitudeUser = deg2rad($latitude);
        $longitudeUser = deg2rad($longitude);
        $latitudeVendor = deg2rad($vendor->latitude);
        $longitudeVendor = deg2rad($vendor->longitude);

        $deltaLatitude = $latitudeVendor - $latitudeUser;
        $deltaLongitude = $longitudeVendor - $longitudeUser;

        $r = 6371;  //radius bumi (km)

        $d = (2*$r)*(asin(sqrt(pow(sin($deltaLatitude/2),2) + 
            cos($latitudeUser)*cos($latitudeVendor)*pow(sin($deltaLongitude/2),2))));
        //rumus haversine
        // jarak(d) = 2*radius bumi * arcsin(sqrt(sin^2 * (deltaLatitude/2) + cos(lat1) * cos(lat2) *(sin^2 * (deltaLongitude/2)))


        return round($d, 8);
    }

    public function getLocation(Request $request=null, $latitude=null, $longitude=null, $method, $number=null)
    {
        
    
        if($request){
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);
        
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        }
        else if(!$latitude && !$longitude){
            return response()->json(['error'=>'Latitude and longitude required']);
        }
        
        //hitung jarak
        $vendors = DB::table('vendors')
        ->select()
        ->get();

        foreach($vendors as $v){
            $jarak = $this->getJarak($v, $latitude, $longitude);
            $v->jarak = round((float)$jarak,2);
        }
        if($method == 'sorted'){
            //sort dari yang terdekat
            $jarak_unsort = [];
            $jarak_idvendor_unsort = [];
            foreach($vendors as $v){
                array_push($jarak_unsort, $v->jarak);
                array_push($jarak_idvendor_unsort, $v->id);
            }
            $jarak_sorted = [];
            $jarak_idvendor = [];

            while (count($jarak_unsort) > 0){
                $min = 99999;
                $index = 0;
                foreach($jarak_unsort as $key => $j){
                    if($j < $min){
                        $min = $j;
                        $index = $key;
                    }
                }
                array_push($jarak_sorted, $min);
                array_push($jarak_idvendor, $jarak_idvendor_unsort[$index]);

                unset($jarak_unsort[$index]);
                unset($jarak_idvendor_unsort[$index]);
            }
        }
        else{
            $jarak_idvendor = $vendors; 
        }

        

        $vendorResult = [];
        foreach($jarak_idvendor as $key=>$vs){
            if($number != null){
                if($key<$number){
                    $vendorData = DB::table('vendors')
                    ->where('id', '=', $vs)
                    ->first();
    
                    $vendorData->jarak = $jarak_sorted[$key];
                    array_push($vendorResult, $vendorData);
                    //beberapa vendor terdekat
                }
            }
            else{
                $vendorData = DB::table('vendors')
                    ->where('id', '=', $vs)
                    ->first();
    
                    $vendorData->jarak = $jarak_sorted[$key];
                    array_push($vendorResult, $vendorData);
                    //semua vendor
            }
            
            
        }

        return $vendorResult;

        
    }

    public function getSingleLocation($latitude, $longitude,$vendorid){
        $vendor = Vendor::find($vendorid);
        // $latitude = $request->latitude;
        // $longitude = $request->longitude;
        $jarak = $this->getJarak($vendor, $latitude, $longitude);
        // dd($vendor->id);
        return $jarak;
    }

    public function getRating($idvendor, $status=null){
        $vendor = Vendor::find($idvendor);
        $ratingController = new RatingController();
        if($status == 'average'){
            $ratingVendor = $ratingController->getRating($idvendor);
            // echo($ratingVendor['vendor_rating']);
        }
        else{
            
            
            $ratingKualitas = $ratingController->getRating($vendor->id, "kualitas");
            $ratingPelayanan = $ratingController->getRating($vendor->id, "pelayanan");
            $ratingFasilitas = $ratingController->getRating($vendor->id, "fasilitas");
            $ratingPengantaran = $ratingController->getRating($vendor->id, "pengantaran");
            $ratingVendor = [
                "id"=>$vendor->id,
                "nama"=>$vendor->nama,
                "kualitas"=>$ratingKualitas,
                "pelayanan"=>$ratingPelayanan,
                "fasilitas"=>$ratingFasilitas,
                "pengantaran"=>$ratingPengantaran,
            ];        
        }
        
        // dd($ratingVendor);
        return $ratingVendor;
    }

    public function getHarga($idvendor, $layanan){
        $vendor = Vendor::find($idvendor);
        $hargaCetakController = new HargaCetakController();
        $arrDataHarga = $hargaCetakController->getHarga($vendor->id, $layanan);
        $arrDataVendor = [
            "id"=>$vendor->id,
            "nama"=>$vendor->nama,
        ];
        array_push($arrDataHarga, $arrDataVendor);
        return $arrDataHarga;
    }

    public function getHargaTermurah($idlayanan){
        $vendors = DB::table('vendors')->get();
        $hargaMin = null;
        $vendorMin = null;
        foreach($vendors as $key=>$v){
            $harga = $this->getHarga($v->id, $idlayanan);
            if($key ==0){
                $vendorMin = $v;
                $hargaMin = $harga;
            }
            else{
                if($harga < $hargaMin){
                    $vendorMin = $v;
                    $hargaMin = $harga;
                }
            }
            
        }
        return $vendorMin;
    }

    public function getLayananSatuan($idlayanan){
        $layananSatuan = DB::table('layanan_cetaks')
        ->where('id','=',$idlayanan)
        ->select('nama','satuan')
        ->first();
        return $layananSatuan;
    }

    public function loadUntukAnda(Request $request){

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $idlayanan = $request->idlayanan;
        $layananSatuan = $this->getLayananSatuan($idlayanan);
        $vendorLokasiTerdekat = $this->getLocation(null, $latitude, $longitude, 'sorted', 1);
        $vendorLokasiTerdekat = $vendorLokasiTerdekat[0];

        $vendorTermurah = $this->getHargaTermurah($idlayanan);

        $vendorRekomendasi = $this->topsisRecommended($idlayanan, $latitude, $longitude);

        $vendors = [
            $vendorLokasiTerdekat, 
            $vendorTermurah, 
            $vendorRekomendasi
        ];

        foreach($vendors as $v){
            $v->jarak = round((float)$this->getSingleLocation($latitude, $longitude, $v->id),2);
            $ratingData = $this->getRating($v->id,'average');
            $v->rating = round($ratingData['vendor_rating'],2);
            $v->total_nota = $ratingData['total_nota'];
            $hargaCetakController = new HargaCetakController();
            $v->hargamin = $hargaCetakController->getMinValue($v->id,$idlayanan);
            $v->hargamaks = $hargaCetakController->getMaxValue($v->id,$idlayanan);
        }
        
        return response()->json(['message' => 'success', 'data' => [
            'layanan' => $layananSatuan->nama,
            'satuan' => $layananSatuan->satuan,
            'vendors' => $vendors
        ]]);
    }

    public function loadVendorsTerdekat(Request $request) {
        // Validate the incoming request data
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
    
        // Access the latitude and longitude from the request
        $latitude = $request->latitude;
        $longitude = $request->longitude;
    
        // Call your method to get the vendors
        $vendors = $this->getLocation(null, $latitude, $longitude, 'sorted', 4);
        
        return response()->json(['message' => 'success', 'data' => $vendors]);
    }

    public function loadLayananTerdekat(Request $request){
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
    
        // Access the latitude and longitude from the request
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $layanans = DB::table('layanan_cetaks')
        ->select('id','nama')
        ->get();
        $vendorLayanans = [];
        $i = 0;
        foreach($layanans as $l){
            if($i == 4){
                break;
            }
            $closestVendors = $this->getLocation(null, $latitude, $longitude, 'sorted');
            foreach($closestVendors as $c){
                $getLayanan = DB::table('vendors_has_jenis_bahan_cetaks')
                ->join('vendors', 'vendors.id', '=', 'vendors_has_jenis_bahan_cetaks.vendors_id')
                ->where('layanan_cetaks_id', '=', $l->id)
                ->where('vendors.id', '=', $c->id)
                ->where('vendors.status', '=', "active")
                ->first();
                if($getLayanan){
                    $c->idlayanan = $l->id;
                    $c->layanan = $l->nama;
                    array_push($vendorLayanans, $c);
                    break;
                }
            }
            $i++;
            
            
        }
        return response()->json(['message' => 'success', 'data' => $vendorLayanans]);
    }

    public function loadLayanans(){
        $layanans = DB::table('layanan_cetaks')
        ->select('id','nama')
        ->get();
        return response()->json(['message' => 'success', 'data' => $layanans]);
    }

    //topsis functions
    public function normalize($matriksTopsis){
        $matriksNormal = [];
        $numRows = count($matriksTopsis);
        $numCols = count($matriksTopsis[0]);
        for ($i = 0; $i < $numRows; $i++) {
            $matriksNormal[$i] = array_fill(0, $numCols, 0);
        }
        $arrpenyebut = [];
        $cols = count($matriksTopsis[0]);
        for ($i = 0; $i < $cols; $i++) {
            $sumpenyebut = 0;
            foreach ($matriksTopsis as $key=>$value){
                $sumpenyebut += pow($matriksTopsis[$key][$i], 2);
            }
            array_push($arrpenyebut, sqrt($sumpenyebut));
        }
        foreach ($matriksTopsis as $key=>$mt){
            for ($j = 0; $j < $cols; $j++) {
                if($j==2 || $j==3){
                    //karena "jarak" dan "harga" sifatnya cost, maka semakin tinggi semakin jelek
                    $matriksNormal[$key][$j] = 1- ($matriksTopsis[$key][$j]/($arrpenyebut[$j]));
                }
                else{
                    $matriksNormal[$key][$j] = $matriksTopsis[$key][$j]/($arrpenyebut[$j]);
                }
            }
        }
        return $matriksNormal;
    }

    public function addWeightToMatrix($weight, $matriks2d){
        $matriksBerbobot = [];
        $numRows = count($matriks2d);
        $numCols = count($matriks2d[0]);
        
        for ($i = 0; $i < $numRows; $i++) {
            $matriksBerbobot[$i] = array_fill(0, $numCols, 0);
        }
        foreach ($matriks2d as $key=>$mt){
            for ($j = 0; $j < $numCols; $j++) {

                $matriksBerbobot[$key][$j] = $matriks2d[$key][$j]*$weight[$j];
            }
        }
        return $matriksBerbobot;
    }

    public function getMax($array){
        $max = 0;
        foreach($array as $a){
            if($a > $max){
                $max = $a;
            }
        }
        return $max;
    }
    public function getMin($array, $max){
        $min = $max;
        foreach($array as $a){
            if($a < $min){
                $min = $a;
            }
        }
        return $min;
    }

    public function cariDPositif($array, $max){
        $hasil = 0;
        foreach($array as $a){
            $hasil += pow($max - $a,2);
        }
        return sqrt($hasil);
    }

    public function cariDNegatif($array, $min){
        $hasil = 0;
        foreach($array as $a){
            $hasil += pow($a - $min, 2);
        }
        return sqrt($hasil);
    }
    //end topsis functions

    public function topsisRecommended($layanan, $latitude, $longitude,){
        $vendors = DB::table('vendors')
        ->where('status','=','active')
        ->select()
        ->get();

        $kualitasWeight = (4.96 + 4.89)/2; //rata-rata dari kualitas hasil cetak dan kesesuaian permintaan
        $pelayananWeight = 4.66;
        //jarak dan harga dikali 4 untuk menyesuaikan banyaknya variabel rating (kualitas, pelayanan, fasilitas, dan rata-rata)
        $jarakWeight = 4 * 4.57;
        $hargaWeight = 4 *4.53; //rata-rata harga cetak yang diinginkan oleh customer
        $fasilitasWeight = (4.45 + 4.38)/2; //rata-rata dari fasilitas pemesanan online dan fasilitas edit sebelum diambil
        $ratingRataRataWeight = 3.87;
        $weights = [$kualitasWeight, $pelayananWeight, $jarakWeight, $hargaWeight, $fasilitasWeight, $ratingRataRataWeight];

        $matriksTopsis = [];
        foreach($vendors as $v){
            
            $rating = $this->getRating($v->id);
            // dd($rating);
            $values = [];

            //kualitas
            $kualitas = $rating['kualitas']['vendor_rating'];
            array_push($values, $kualitas);

            //pelayanan pelanggan
            $pelayanan = $rating['pelayanan']['vendor_rating'];            
            array_push($values, $pelayanan);
            //jarak
            $jarak = $this->getSingleLocation($latitude, $longitude,$v->id);
            $jarak = round($jarak*100, 2); //jadikan meter & bulatkan 2
            array_push($values, $jarak);

            //harga
            $harga = $this->getHarga($v->id,$layanan);
            array_push($values, $harga['avg_harga']);
            
            //fasilitas pengantaran
            $fasilitas = $rating['fasilitas']['vendor_rating'];
            array_push($values, $fasilitas);

            //rating rata-rata (rating)
            $ratingController = new RatingController();
            $ratingRataRata = $ratingController->getRating($v->id, 'average');
            array_push($values, $ratingRataRata['vendor_rating']);

            array_push($matriksTopsis, $values);
        }
       
        $matriksNormal = $this->normalize($matriksTopsis);
        $matriksBerbobot = $this->addWeightToMatrix($weights, $matriksNormal);
         
        // dd($matriksBerbobot);

        $arrMax = [];
        $arrMin = [];
        foreach($matriksBerbobot as $mb){
            $max = $this->getMax($mb);
            $min = $this->getMin($mb, $max);
            array_push($arrMax, $max);
            array_push($arrMin, $min);
        }

        // dd($arrMax, $arrMin);
        
        $dPositif = [];
        $dNegatif = [];
        foreach($matriksBerbobot as $key=>$mb){
            array_push($dPositif, $this->cariDPositif($mb, $arrMax[$key]));
            array_push($dNegatif, $this->cariDNegatif($mb, $arrMin[$key]));
        }
        $nilaiAkhirVariabel = [];
        foreach($matriksBerbobot as $key=>$mb){
            $result = $dNegatif[$key]/($dNegatif[$key] + $dPositif[$key]);
            array_push($nilaiAkhirVariabel, $result);
        }
        foreach($vendors as $key=>$v){
            $v->nilaiakhir = $nilaiAkhirVariabel[$key]; 
        }
        $idrecommendvendor = 0;
        $maxnilaiakhir = 0;
        foreach($vendors as $key=>$v){
            if($v->nilaiakhir > $maxnilaiakhir){
                $maxnilaiakhir = $v->nilaiakhir;
                $idrecommendvendor = $v->id;
            }
        }
        $recommendvendor = DB::table('vendors')
        ->where('id','=',$idrecommendvendor)
        ->first();
        return $recommendvendor;
    }
 
    public function getLayananVendor($idvendor){
        $layanans = DB::table('vendors_has_jenis_bahan_cetaks')
        ->join('layanan_cetaks', 'layanan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id')
        ->where('vendors_has_jenis_bahan_cetaks.vendors_has_layanan_cetaks.vendors_id', '=', $idvendor)
        ->where('vendors.status','=','active')
        ->select('vendors_has_jenis_bahan_cetaks.layanan_cetaks.*')
        ->get();
        return $layanans;
    }

    public function index()
    {
        $vendors = Vendor::all();
        foreach($vendors as $key=>$v){

            $ratings = DB::table('notas')
            ->join('pemesanans','pemesanans.notas_id','=','notas.id')
            ->join('ratings','ratings.notas_id','=','notas.id')
            ->where('pemesanans.vendors_id','=', $v->id)
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
                ->where('pemesanans.vendors_id', $v->id)
                ->count();
        
                foreach ($ratings as $r) {
                    $totalRating += $r->average_rating;

                }        
                $vendor_rating = $totalRating / $totalNota;
                $v->vendor_rating = $vendor_rating;
                $v->total_nota = $totalNota;               
            }
            else{
                $v->vendor_rating = null;
                $v->total_nota = 0;
            }
            
        }
        
        return view('home', compact('vendors'));
    }

    public function indexCart()
    {
        $vendors = DB::table('pemesanans')
        ->join('vendors', 'pemesanans.vendors_id', '=', 'vendors.id')
        ->select('vendors.id', 'vendors.nama','vendors.foto_lokasi', DB::raw('COUNT(pemesanans.id) as total_pemesanan')) // Use aggregate function
        ->groupBy('vendors.id', 'vendors.nama','vendors.foto_lokasi')
        ->where('pemesanans.notas_id', '=', null)
        ->get();
        
        return view('cart.vendors', compact('vendors'));
    }
    


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vendors.create');
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
            'nama_vendor' => 'required',
            'alamat_vendor' => 'required',
            'kontak_vendor' => 'required',
        ]);

        Vendor::create($request->all());

        return redirect()->route('vendors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        return view('vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'nama_vendor' => 'required',
            'alamat_vendor' => 'required',
            'kontak_vendor' => 'required',
        ]);

        $vendor->update($request->all());

        return redirect()->route('vendors.index');
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
