<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateWaktuDiterima extends Command
{
    protected $signature = 'update:waktu-diterima';
    protected $description = 'Update waktu_diterima for notas after 24 hours';
    public function handle()
    {
        $notas = DB::table('notas')
            ->whereNull('waktu_diterima')
            ->update(['waktu_menerima_pesanan' => now()]);

        $this->info("Updated {$notas} records with waktu_diterima.");
    }
}
