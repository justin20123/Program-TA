<?php

namespace Database\Seeders;

use App\Models\Nota;
use App\Models\Rating;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Assuming you have some vendors in the database
        $notas = Nota::all();

        foreach ($notas as $n) {
            $rating = ['kualitas','pelayanan','fasilitas','pengantaran'];
            foreach ($rating as $key => $value) {
                Rating::create([
                    'notas_id' => $n->id,
                    'nama' => $rating[$key],
                    'nilai' => $faker->numberBetween(1, 5)
                    
                ]);
            }
        }
    }
}
