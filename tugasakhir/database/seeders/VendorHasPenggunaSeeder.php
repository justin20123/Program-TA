<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class VendorHasPenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $vendors = DB::table('vendors')->select()->get();
        
        $manajers = DB::table('penggunas')->where('role', 'manajer')->select()->get();
        $pengantars = DB::table('penggunas')->where('role', 'pengantar')->select()->get();
        $pegawais = DB::table('penggunas')->where('role', 'pegawai')->select()->get();
        foreach($vendors as $v){
            // dd($manajers);
            //insert manajer
            
            DB::table('vendors_has_penggunas')->insert([
               'vendors_id' => $v->id,
                'penggunas_email' => $manajers[$v->id-1]->email,
                'penggunas_id' => $manajers[$v->id-1]->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // dd($manajers);

            for ($i = 0; $i < 3; $i++){
                
                DB::table('vendors_has_penggunas')->insert([
                    'vendors_id' => $v->id,
                     'penggunas_email' => $pengantars[($v->id*($i+1)-1)]->email,
                     'penggunas_id' => $pengantars[($v->id*($i+1)-1)]->id,
                     'created_at' => now(),
                     'updated_at' => now(),
                 ]);

                DB::table('vendors_has_penggunas')->insert([
                    'vendors_id' => $v->id,
                     'penggunas_email' => $pegawais[($v->id*($i+1)-1)]->email,
                     'penggunas_id' => $pegawais[($v->id*($i+1)-1)]->id,
                     'created_at' => now(),
                     'updated_at' => now(),
                 ]);
            }
        }
    }
}
