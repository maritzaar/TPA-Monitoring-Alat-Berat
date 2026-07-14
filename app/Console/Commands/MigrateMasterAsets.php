<?php

namespace App\Console\Commands;

use App\Models\DataAlat;
use App\Models\FuelTransaction;
use App\Models\MasterAset;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:migrate-master-asets')]
#[Description('Command description')]
class MigrateMasterAsets extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi data Master Aset...');

        // Ambil data unik dari data_alat
        $this->info('Mengambil dari data_alat...');
        $telemetries = DataAlat::select(
            'id_aset as unit_code',
            'nomor_seri',
            'model',
            'group_aset',
            'area',
            'internal_order',
            'group_internal_order',
            'group_desc',
            'pt'
        )->distinct()->get();

        foreach ($telemetries as $t) {
            MasterAset::updateOrCreate(
                ['unit_code' => $t->unit_code],
                [
                    'nomor_seri' => $t->nomor_seri,
                    'model' => $t->model,
                    'group_aset' => $t->group_aset,
                    'area' => $t->area,
                    'internal_order' => $t->internal_order,
                    'group_internal_order' => $t->group_internal_order,
                    'group_desc' => $t->group_desc,
                    'pt' => $t->pt,
                ]
            );
        }

        // Ambil data unik dari fuel_transactions
        $this->info('Mengambil dari fuel_transactions...');
        $fuels = FuelTransaction::select(
            'unit_code',
            'internal_order',
            'group_aset',
            'area',
            'company_code'
        )->distinct()->get();

        foreach ($fuels as $f) {
            $master = MasterAset::firstOrCreate(
                ['unit_code' => $f->unit_code]
            );

            $master->group_aset = $master->group_aset ?: $f->group_aset;
            $master->area = $master->area ?: $f->area;
            $master->internal_order = $master->internal_order ?: $f->internal_order;
            $master->company_code = $master->company_code ?: $f->company_code;

            $master->save();
        }

        $this->info('Selesai! Total master aset saat ini: '.MasterAset::count());
    }
}
