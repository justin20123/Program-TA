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

    public function getVendors()
    {
        $vendors = DB::table('vendors')
            ->where('status', 'active')
            ->get();
        return $vendors;
    }

    public function getJarak($vendor, $latitude, $longitude)
    {
        $latitudeUser = deg2rad($latitude);
        $longitudeUser = deg2rad($longitude);
        $latitudeVendor = deg2rad($vendor->latitude);
        $longitudeVendor = deg2rad($vendor->longitude);

        $deltaLatitude = $latitudeVendor - $latitudeUser;
        $deltaLongitude = $longitudeVendor - $longitudeUser;

        $r = 6371;  //radius bumi (km)

        $d = (2 * $r) * (asin(sqrt(pow(sin($deltaLatitude / 2), 2) +
            cos($latitudeUser) * cos($latitudeVendor) * pow(sin($deltaLongitude / 2), 2))));
        //rumus haversine
        // jarak(d) = 2*radius bumi * arcsin(sqrt(sin^2 * (deltaLatitude/2) + cos(lat1) * cos(lat2) *(sin^2 * (deltaLongitude/2)))


        return round($d, 8);
    }

    public function getLocation(Request $request = null, $latitude = null, $longitude = null, $method, $number = null)
    {


        if ($request) {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $latitude = $request->latitude;
            $longitude = $request->longitude;
        } else if (!$latitude && !$longitude) {
            return response()->json(['error' => 'Latitude and longitude required']);
        }

        //hitung jarak
        $vendors = $this->getVendors();

        foreach ($vendors as $v) {
            $jarak = $this->getJarak($v, $latitude, $longitude);
            $v->jarak = round((float)$jarak, 2);
        }
        if ($method == 'sorted') {
            //sort dari yang terdekat
            $jarak_unsort = [];
            $jarak_idvendor_unsort = [];
            foreach ($vendors as $v) {
                array_push($jarak_unsort, $v->jarak);
                array_push($jarak_idvendor_unsort, $v->id);
            }
            $jarak_sorted = [];
            $jarak_idvendor = [];

            while (count($jarak_unsort) > 0) {
                $min = 99999;
                $index = 0;
                foreach ($jarak_unsort as $key => $j) {
                    if ($j < $min) {
                        $min = $j;
                        $index = $key;
                    }
                }
                array_push($jarak_sorted, $min);
                array_push($jarak_idvendor, $jarak_idvendor_unsort[$index]);

                unset($jarak_unsort[$index]);
                unset($jarak_idvendor_unsort[$index]);
            }
        } else {
            $jarak_idvendor = $vendors;
        }



        $vendorResult = [];
        foreach ($jarak_idvendor as $key => $vs) {
            if ($number != null) {
                if ($key < $number) {
                    $vendorData = DB::table('vendors')
                        ->where('id', '=', $vs)
                        ->first();

                    $vendorData->jarak = $jarak_sorted[$key];
                    array_push($vendorResult, $vendorData);
                    //beberapa vendor terdekat
                }
            } else {
                $vendorData = DB::table('vendors')
                    ->where('id', '=', $vs)
                    ->first();

                $vendorData->jarak = $jarak_sorted[$key];
                $vendorResult = $vendorData;
                //semua vendor
            }
        }

        return $vendorResult;
    }

    public function getSingleLocation($latitude, $longitude, $vendorid)
    {
        $vendor = Vendor::find($vendorid);
        $jarak = $this->getJarak($vendor, $latitude, $longitude);
        // dd($vendor->id);
        return $jarak;
    }

    public function getSingleLocationRequest(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $vendorid = $request->input('idvendor');

        $vendor = Vendor::find($vendorid);
        $jarak = $this->getJarak($vendor, $latitude, $longitude);
        // dd($vendor->id);
        return round((float)$jarak, 1);
    }

    public function getRating($idvendor, $status = null)
    {
        $vendor = Vendor::find($idvendor);
        $ratingController = new RatingController();
        if ($status == 'average') {
            $ratingVendor = $ratingController->getRating($idvendor);
            // echo($ratingVendor['vendor_rating']);
        } else {


            $ratingKualitas = $ratingController->getRating($vendor->id, "kualitas");
            $ratingPelayanan = $ratingController->getRating($vendor->id, "pelayanan");
            $ratingFasilitas = $ratingController->getRating($vendor->id, "fasilitas");
            $ratingPengantaran = $ratingController->getRating($vendor->id, "pengantaran");
            $ratingVendor = [
                "id" => $vendor->id,
                "nama" => $vendor->nama,
                "kualitas" => $ratingKualitas,
                "pelayanan" => $ratingPelayanan,
                "fasilitas" => $ratingFasilitas,
                "pengantaran" => $ratingPengantaran,
            ];
        }

        // dd($ratingVendor);
        return $ratingVendor;
    }

    public function getHarga($idvendor, $layanan)
    {
        $vendor = Vendor::find($idvendor);
        $hargaCetakController = new HargaCetakController();
        $arrDataHarga = $hargaCetakController->getHarga($vendor->id, $layanan);
        $arrDataVendor = [
            "id" => $vendor->id,
            "nama" => $vendor->nama,
        ];
        array_push($arrDataHarga, $arrDataVendor);
        return $arrDataHarga;
    }

    public function getHargaTermurah($idlayanan)
    {
        $vendors = $this->getVendors();
        $hargaMin = null;
        $vendorMin = null;
        foreach ($vendors as $key => $v) {
            $harga = $this->getHarga($v->id, $idlayanan);
            if ($key == 0) {
                $vendorMin = $v;
                $hargaMin = $harga;
            } else {
                if ($harga < $hargaMin) {
                    $vendorMin = $v;
                    $hargaMin = $harga;
                }
            }
        }
        return $vendorMin;
    }

    public function getLayananSatuan($idlayanan)
    {
        $layananSatuan = DB::table('layanan_cetaks')
            ->where('id', '=', $idlayanan)
            ->select('nama', 'satuan')
            ->first();
        return $layananSatuan;
    }

    public function loadUntukAnda(Request $request)
    {
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $idlayanan = $request->idlayanan;

        $layanans = DB::table('vendors_has_jenis_bahan_cetaks')
            ->where('layanan_cetaks_id', '=', $idlayanan)
            ->count();

        if ($layanans == 0) {
            return response()->json(['message' => 'Layanan ini belum memiliki vendor.']);
        }

        $layananSatuan = $this->getLayananSatuan($idlayanan);
        $vendorTermurah = $this->getHargaTermurah($idlayanan);
        if (!$vendorTermurah) {
            return response()->json(['message' => 'Layanan ini belum memiliki vendor']);
        }
        $vendorLokasiTerdekat = $this->getLocation(null, $latitude, $longitude, 'sorted');
        $vendorRekomendasi = $this->topsisRecommended($idlayanan, $latitude, $longitude);

        $vendors = [];

        array_push($vendors, $vendorLokasiTerdekat);
        array_push($vendors, $vendorTermurah);
        array_push($vendors, $vendorRekomendasi);

        // dd($vendors);
        $hargaCetakController = new HargaCetakController();
        foreach ($vendors as $v) {
            $idvendor = $v->id;
            // echo 'idvendor:'. $idvendor . "idlayanan:".$idlayanan."\n";
            $v->jarak = round((float)$this->getSingleLocation($latitude, $longitude, $idvendor), 2);
            $ratingData = $this->getRating($idvendor, 'average');
            $v->rating = round($ratingData['vendor_rating'], 2);
            $v->total_nota = $ratingData['total_nota'];
            $v->hargamin = $hargaCetakController->getMinValue($idvendor, $idlayanan);
            $v->hargamaks = $hargaCetakController->getMaxValue($idvendor, $idlayanan);
        }

        // dd($vendors);

        return response()->json(['message' => 'success', 'data' => [
            'layanan' => $layananSatuan->nama,
            'satuan' => $layananSatuan->satuan,
            'vendors' => $vendors,
            'data' => $vendors[2]->data
        ]]);
    }

    public function loadVendorsTerdekat(Request $request)
    {
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

    public function loadLayananTerdekat(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Access the latitude and longitude from the request
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $layanans = DB::table('layanan_cetaks')
            ->select('id', 'nama')
            ->get();
        $vendorLayanans = [];
        $i = 0;
        foreach ($layanans as $l) {
            if ($i == 4) {
                break;
            }
            $closestVendors = $this->getLocation(null, $latitude, $longitude, 'sorted', 4);

            foreach ($closestVendors as $c) {
                $getLayanan = DB::table('vendors_has_jenis_bahan_cetaks')
                    ->join('vendors', 'vendors.id', '=', 'vendors_has_jenis_bahan_cetaks.vendors_id')
                    ->where('layanan_cetaks_id', '=', $l->id)
                    ->where('vendors.id', '=', $c->id)
                    ->where('vendors.status', '=', "active")
                    ->first();
                if ($getLayanan) {
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

    public function loadLayanans()
    {
        $layanans = DB::table('layanan_cetaks')
            ->select('id', 'nama')
            ->get();
        return response()->json(['message' => 'success', 'data' => $layanans]);
    }

    public function checkzero($int)
    {
        if ($int == 0) {
            $int = 0.00000000001;
        }
        return $int;
    }

    //topsis functions
    public function normalize($matriksTopsis)
    {
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

            foreach ($matriksTopsis as $key => $value) {
                $sumpenyebut += pow($matriksTopsis[$key][$i], 2);
            }
            array_push($arrpenyebut, sqrt($sumpenyebut));
        }
        foreach ($matriksTopsis as $key => $mt) {
            for ($j = 0; $j < $cols; $j++) {

                $penyebut = $arrpenyebut[$j];
                if ($penyebut == 0) {
                    $penyebut = 0.000001;
                }
                $matriksNormal[$key][$j] = $matriksTopsis[$key][$j] / ($penyebut);
            }
        }
        return $matriksNormal;
    }

    public function addWeightToMatrix($weight, $matriks2d)
    {
        $matriksBerbobot = [];
        $numRows = count($matriks2d);
        $numCols = count($matriks2d[0]);

        for ($i = 0; $i < $numRows; $i++) {
            $matriksBerbobot[$i] = array_fill(0, $numCols, 0);
        }
        foreach ($matriks2d as $key => $mt) {
            for ($j = 0; $j < $numCols; $j++) {

                $matriksBerbobot[$key][$j] = $matriks2d[$key][$j] * $weight[$j];
            }
        }
        return $matriksBerbobot;
    }

    public function hitungMaxMin($matriks, $jumlvendor)
    {
        $arrYMax = [];
        $arrYMin = [];

        for ($i = 0; $i < 6; $i++) {
            $maxValue = $matriks[0][$i];
            $minValue = $matriks[0][$i];

            foreach ($matriks as $row) {
                if ($row[$i] > $maxValue) {
                    $maxValue = $row[$i];
                }
                if ($row[$i] < $minValue) {
                    $minValue = $row[$i];
                }
            }

            if ($i == 2 || $i == 3) {
                array_push($arrYMax, $minValue);
                array_push($arrYMin, $maxValue);
            } else {
                array_push($arrYMax, $maxValue);
                array_push($arrYMin, $minValue);
            }
        }

        return ["ymax" => $arrYMax, "ymin" => $arrYMin];
    }


    public function cariDPositif($array, $maxArray)
    {
        $hasil = 0;
        foreach ($array as $key => $a) {
            $hasil += pow($maxArray[$key] - $a, 2);
        }
        return sqrt($hasil);
    }

    public function cariDNegatif($array, $minArray)
    {
        $hasil = 0;
        foreach ($array as $key => $a) {
            $hasil += pow($a - $minArray[$key], 2);
        }
        return sqrt($hasil);
    }

    //end topsis functions

    public function topsisRecommended($layanan, $latitude, $longitude,)
    {
        $datacheck = array();

        $vendors = DB::table('vendors')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.vendors_id', '=', 'vendors.id')
            ->where('status', '=', 'active')
            ->groupBy('vendors_has_jenis_bahan_cetaks.vendors_id')
            ->select('vendors_has_jenis_bahan_cetaks.vendors_id as id', DB::raw('COUNT(vendors_has_jenis_bahan_cetaks.vendors_id) as count_layanan'))
            ->distinct()
            ->having('count_layanan', '>', 0)
            ->get();
        $listvendors = [];
        foreach ($vendors as $v){
            $vendor = Vendor::find($v->id);
            array_push($listvendors, $vendor["nama"]);    
        }
        $datacheck['vendors'] = $listvendors;


        //rata-rata dari kualitas hasil cetak dan kesesuaian permintaan
        $kualitasWeight = (4.96 + 4.89) / 2; 
        $pelayananWeight = 4.66;
        $jarakWeight = 4.57;
        //rata-rata harga cetak yang diinginkan oleh customer
        $hargaWeight = 4.53; 
        //rata-rata dari fasilitas pemesanan online dan fasilitas edit sebelum diambil
        $fasilitasWeight = (4.45 + 4.38) / 2; 
        $ratingRataRataWeight = 3.87;
        $weights = [$kualitasWeight, $pelayananWeight, $jarakWeight, $hargaWeight, $fasilitasWeight, $ratingRataRataWeight];

        $datacheck['weightsindex'] = ['kualitas', 'pelayanan', 'jarak', 'harga', 'fasilitas', 'rating rata-rata'];
        $datacheck['weights'] = $weights;


        $matriksTopsis = [];
        foreach ($vendors as $key => $v) {

            $rating = $this->getRating($v->id);
            $values = [];

            //kualitas
            $kualitas = $this->checkzero($rating['kualitas']['vendor_rating']);

            array_push($values, $kualitas);

            //pelayanan pelanggan
            $pelayanan = $this->checkzero($rating['pelayanan']['vendor_rating']);
            array_push($values, $pelayanan);
            //jarak
            $jarak = $this->checkzero($this->getSingleLocation($latitude, $longitude, $v->id));
            $jarak = round($jarak * 100, 2); //jadikan meter & bulatkan 2
            array_push($values, $jarak);

            //harga
            $hargaarr = $this->getHarga($v->id, $layanan);
            $harga = $this->checkzero($hargaarr['avg_harga']);
            array_push($values, $harga);

            //fasilitas pengantaran
            $fasilitas = $this->checkzero($rating['fasilitas']['vendor_rating']);
            array_push($values, $fasilitas);

            //rating rata-rata (rating)
            $ratingController = new RatingController();
            $ratingRataRata = $ratingController->getRating($v->id, 'average');
            array_push($values, $this->checkzero($ratingRataRata['vendor_rating']));

            array_push($matriksTopsis, $values);
        }
        $datacheck['matriksTopsis'] = $matriksTopsis;

        $matriksNormal = $this->normalize($matriksTopsis);
        $datacheck['matriksNormal'] = $matriksNormal;

        $matriksBerbobot = $this->addWeightToMatrix($weights, $matriksNormal);
        $datacheck['matriksBerbobot'] = $matriksBerbobot;
        // dd($matriksBerbobot);

        $nilaiPlusMin = $this->hitungMaxMin($matriksBerbobot, count($vendors));
        $ymax = $nilaiPlusMin["ymax"];
        $ymin = $nilaiPlusMin["ymin"];
        $datacheck['solusiIdeal'] = ["Solusi Ideal Positif" => $ymax, "Solusi Ideal Negatif" => $ymin];


        $dPositif = [];
        $dNegatif = [];
        foreach ($matriksBerbobot as $mb) {
            $dPositif[] = $this->cariDPositif($mb, $ymax);
            $dNegatif[] = $this->cariDNegatif($mb, $ymin);
        }
        $datacheck['d'] = ["d+" => $dPositif, "d-" => $dNegatif];
        $nilaiAkhirVariabel = [];

        foreach ($matriksBerbobot as $key => $mb) {
            $result = $dNegatif[$key] / ($dNegatif[$key] + $dPositif[$key]);
            array_push($nilaiAkhirVariabel, $result);
        }
        $datacheck['nilaiAkhirVariabel'] = $nilaiAkhirVariabel;

        foreach ($vendors as $key => $v) {
            $v->nilaiakhir = $nilaiAkhirVariabel[$key];
        }
        $idrecommendvendor = 0;
        $maxnilaiakhir = 0;
        foreach ($vendors as $key => $v) {
            if ($v->nilaiakhir > $maxnilaiakhir) {
                $maxnilaiakhir = $v->nilaiakhir;
                $idrecommendvendor = $v->id;
            }
        }
        $recommendvendor = DB::table('vendors')
            ->where('id', '=', $idrecommendvendor)
            ->first();
        $recommendvendor->data = $datacheck;
        return $recommendvendor;
    }

    public function getLayananVendor($idvendor)
    {
        $layanans = DB::table('vendors_has_jenis_bahan_cetaks')
            ->join('layanan_cetaks', 'layanan_cetaks_id', '=', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id')
            ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $idvendor)
            ->where('vendors.status', '=', 'active')
            ->select('vendors_has_jenis_bahan_cetaks.layanan_cetaks.*')
            ->get();
        return $layanans;
    }

    public function index($layanan_id = 1)
    {
        $listvendor = DB::table("vendors_has_jenis_bahan_cetaks")
            ->where('layanan_cetaks_id', '=', $layanan_id)
            ->get();

        $vendors = [];

        foreach ($listvendor as $lv) {
            $vendor = DB::table("vendors")
                ->where('id', '=', $lv->vendors_id)
                ->first();

            if ($vendor->status == "active") {
                if (!isset($vendors[$vendor->id])) {

                    $vendors[$vendor->id] = $vendor;
                }
            }
        }

        $layananvendor = DB::table('layanan_cetaks')
            ->where('id', '=', $layanan_id)
            ->first();

        foreach ($vendors as $key => $v) {
            $fileName = $v->foto_lokasi;
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);

            $v->file_extension = $extension;

            $pengantars = DB::table('penggunas')
                ->where('vendors_id', '=', $v->id)
                ->where('role', '=', 'pengantar')
                ->count();

            $v->statusantar = $pengantars > 0 ? "Tersedia pengantaran" : "";

            $ratings = DB::table('notas')
                ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
                ->join('ratings', 'ratings.notas_id', '=', 'notas.id')
                ->where('pemesanans.vendors_id', '=', $v->id)
                ->whereNotNull('ratings.nilai')
                ->select('notas.id', DB::raw('avg(ratings.nilai) as average_rating'))
                ->groupBy('notas.id')
                ->get();

            if ($ratings->isNotEmpty()) {
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
            } else {
                $v->vendor_rating = null;
                $v->total_nota = 0;
            }

            $hargaCetakController = new HargaCetakController();
            $v->hargamin = $hargaCetakController->getMinValue($v->id, $layanan_id);
            $v->hargamaks = $hargaCetakController->getMaxValue($v->id, $layanan_id);
        }

        $vendors = array_values($vendors);

        $layanan_cetaks = DB::table('layanan_cetaks')
            ->select('id', 'nama')
            ->get();


        // dd($vendors);

        return view('vendors.vendors', compact('vendors', 'layananvendor', 'layanan_cetaks', 'layanan_id'));
    }
    public function isvendorada()
    {
        $vendors = DB::table('vendors')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.vendors_id', '=', 'vendors.id')
            ->where('status', '=', 'active')
            ->groupBy('vendors_has_jenis_bahan_cetaks.vendors_id')
            ->select(DB::raw('COUNT(vendors_has_jenis_bahan_cetaks.vendors_id) as count_layanan'))
            ->having('count_layanan', '>', 0)
            ->get();
        $value = false;
        if (count($vendors) > 0) {
            $value = true;
        }
        return response()->json(['value' => $value]);
    }

    public function indexCart()
    {
        $vendors = DB::table('pemesanans')
            ->join('vendors', 'pemesanans.vendors_id', '=', 'vendors.id')
            ->select('vendors.id', 'vendors.nama', 'vendors.foto_lokasi', DB::raw('COUNT(pemesanans.id) as total_pemesanan')) // Use aggregate function
            ->groupBy('vendors.id', 'vendors.nama', 'vendors.foto_lokasi')
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
